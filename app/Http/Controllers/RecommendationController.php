<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use App\Models\Prestamo;
use App\Models\Favorito;
use App\Models\Resena;
use App\Models\Categoria;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RecommendationController extends Controller
{
    /**
     * Mostrar página de recomendaciones personalizadas
     */
    public function index()
    {
        $user = Auth::user();
        
        // Obtener recomendaciones personalizadas
        $recomendaciones = $this->getPersonalizedRecommendations($user);
        
        // Obtener libros populares en categorías favoritas
        $libros_populares_categoria = $this->getPopularBooksInFavoriteCategories($user);
        
        return view('books.recommendations', compact('recomendaciones', 'libros_populares_categoria'));
    }

    /**
     * Obtener recomendaciones personalizadas usando algoritmos de IA
     */
    private function getPersonalizedRecommendations($user)
    {
        // Obtener historial de lectura del usuario
        $historial_lectura = $this->getUserReadingHistory($user);
        
        if (empty($historial_lectura)) {
            return collect();
        }

        // Calcular preferencias del usuario
        $preferencias = $this->calculateUserPreferences($historial_lectura);
        
        // Obtener libros similares
        $libros_similares = $this->findSimilarBooks($preferencias, $user);
        
        // Aplicar filtros adicionales
        $recomendaciones = $this->applyRecommendationFilters($libros_similares, $user);
        
        return $recomendaciones;
    }

    /**
     * Obtener historial de lectura del usuario
     */
    private function getUserReadingHistory($user)
    {
        return Prestamo::where('usuario_id', $user->id)
            ->where('estado', 'devuelto')
            ->with(['libro.autor', 'libro.categoria'])
            ->get()
            ->map(function ($prestamo) {
                return [
                    'libro' => $prestamo->libro,
                    'rating' => $prestamo->libro->resenas()->avg('rating') ?? 0,
                    'fecha_lectura' => $prestamo->fecha_devolucion,
                    'categoria' => $prestamo->libro->categoria,
                    'autor' => $prestamo->libro->autor
                ];
            });
    }

    /**
     * Calcular preferencias del usuario basadas en su historial
     */
    private function calculateUserPreferences($historial_lectura)
    {
        $preferencias = [
            'categorias' => [],
            'autores' => [],
            'rating_promedio' => 0,
            'años_preferidos' => [],
            'generos_favoritos' => []
        ];

        // Analizar categorías favoritas
        $categorias_count = [];
        foreach ($historial_lectura as $lectura) {
            $categoria_id = $lectura['libro']->categoria_id;
            $categorias_count[$categoria_id] = ($categorias_count[$categoria_id] ?? 0) + 1;
        }
        
        arsort($categorias_count);
        $preferencias['categorias'] = array_keys(array_slice($categorias_count, 0, 3, true));

        // Analizar autores favoritos
        $autores_count = [];
        foreach ($historial_lectura as $lectura) {
            $autor_id = $lectura['libro']->autor_id;
            $autores_count[$autor_id] = ($autores_count[$autor_id] ?? 0) + 1;
        }
        
        arsort($autores_count);
        $preferencias['autores'] = array_keys(array_slice($autores_count, 0, 3, true));

        // Calcular rating promedio
        $ratings = array_column($historial_lectura, 'rating');
        $preferencias['rating_promedio'] = !empty($ratings) ? array_sum($ratings) / count($ratings) : 0;

        // Analizar años preferidos
        $años = [];
        foreach ($historial_lectura as $lectura) {
            if ($lectura['libro']->anio_publicacion) {
                $año = $lectura['libro']->anio_publicacion;
                $años[$año] = ($años[$año] ?? 0) + 1;
            }
        }
        
        arsort($años);
        $preferencias['años_preferidos'] = array_keys(array_slice($años, 0, 5, true));

        return $preferencias;
    }

    /**
     * Encontrar libros similares basados en preferencias
     */
    private function findSimilarBooks($preferencias, $user)
    {
        $query = Libro::with(['autor', 'categoria', 'resenas'])
            ->where('estado', 'disponible')
            ->whereNotIn('id', $user->prestamos()->pluck('libro_id'));

        // Filtrar por categorías favoritas
        if (!empty($preferencias['categorias'])) {
            $query->whereIn('categoria_id', $preferencias['categorias']);
        }

        // Filtrar por autores favoritos
        if (!empty($preferencias['autores'])) {
            $query->orWhereIn('autor_id', $preferencias['autores']);
        }

        // Filtrar por años preferidos
        if (!empty($preferencias['años_preferidos'])) {
            $query->orWhere(function($q) use ($preferencias) {
                foreach ($preferencias['años_preferidos'] as $año) {
                    $q->orWhereYear('anio_publicacion', $año);
                }
            });
        }

        // Filtrar por rating similar
        if ($preferencias['rating_promedio'] > 0) {
            $rating_min = max(0, $preferencias['rating_promedio'] - 1);
            $rating_max = min(5, $preferencias['rating_promedio'] + 1);
            
            $query->whereHas('resenas', function($q) use ($rating_min, $rating_max) {
                $q->havingRaw('AVG(rating) BETWEEN ? AND ?', [$rating_min, $rating_max]);
            });
        }

        // Agregar información de favoritos
        $libros = $query->get()->map(function ($libro) use ($user) {
            $libro->is_favorite = $user->favoritos()->where('libro_id', $libro->id)->exists();
            // $libro->rating = $libro->resenas()->avg('rating') ?? 0;
            $libro->prestamos_count = $libro->prestamos()->count();
            return $libro;
        });

        return $libros;
    }

    /**
     * Aplicar filtros adicionales a las recomendaciones
     */
    private function applyRecommendationFilters($libros, $user)
    {
        // Calcular score de recomendación para cada libro
        $libros_con_score = $libros->map(function ($libro) use ($user) {
            $score = 0;
            
            // Score por rating
            $score += $libro->rating * 2;
            
            // Score por popularidad (número de préstamos)
            $score += min($libro->prestamos_count * 0.5, 10);
            
            // Score por ser favorito del usuario
            if ($libro->is_favorite) {
                $score += 5;
            }
            
            // Score por ser libro nuevo
            if ($libro->anio_publicacion && $libro->anio_publicacion->diffInDays(now()) < 90) {
                $score += 3;
            }
            
            // Score por tener muchas reseñas
            $resenas_count = $libro->resenas()->count();
            $score += min($resenas_count * 0.1, 5);
            
            $libro->recommendation_score = $score;
            return $libro;
        });

        // Ordenar por score y limitar resultados
        return $libros_con_score->sortByDesc('recommendation_score')->take(12);
    }

    /**
     * Obtener libros populares en categorías favoritas
     */
    private function getPopularBooksInFavoriteCategories($user)
    {
        // Obtener categorías favoritas del usuario
        $categorias_favoritas = $this->getFavoriteCategories($user);
        
        if (empty($categorias_favoritas)) {
            // Si no hay categorías favoritas, usar las más populares
            $categorias_favoritas = Categoria::withCount('libros')
                ->orderBy('libros_count', 'desc')
                ->take(3)
                ->pluck('id');
        }

        // Obtener libros populares en esas categorías
        $libros_populares = Libro::with(['autor', 'categoria', 'resenas'])
            ->whereIn('categoria_id', $categorias_favoritas)
            ->where('estado', 'disponible')
            ->whereNotIn('id', $user->prestamos()->pluck('libro_id'))
            ->withCount('prestamos')
            ->orderBy('prestamos_count', 'desc')
            ->take(8)
            ->get()
            ->map(function ($libro) use ($user) {
                $libro->is_favorite = $user->favoritos()->where('libro_id', $libro->id)->exists();
                // $libro->rating = $libro->resenas()->avg('rating') ?? 0;
                return $libro;
            });

        return $libros_populares;
    }

    /**
     * Obtener categorías favoritas del usuario
     */
    private function getFavoriteCategories($user)
    {
        return Prestamo::where('usuario_id', $user->id)
            ->where('estado', 'devuelto')
            ->join('libros', 'prestamos.libro_id', '=', 'libros.id')
            ->select('libros.categoria_id', DB::raw('COUNT(*) as total'))
            ->groupBy('libros.categoria_id')
            ->orderBy('total', 'desc')
            ->take(3)
            ->pluck('categoria_id');
    }

    /**
     * Obtener recomendaciones para la API
     */
    public function getRecommendations()
    {
        $user = Auth::user();
        $recomendaciones = $this->getPersonalizedRecommendations($user);
        
        return response()->json([
            'success' => true,
            'data' => $recomendaciones->map(function ($libro) {
                return [
                    'id' => $libro->id,
                    'titulo' => $libro->titulo,
                    'autor' => $libro->autor->nombre ?? 'Autor',
                    'categoria' => $libro->categoria->nombre ?? 'Categoría',
                    'rating' => $libro->rating,
                    'imagen_portada' => $libro->imagen_portada,
                    'disponible' => $libro->estado,
                    'is_favorite' => $libro->is_favorite,
                    'recommendation_score' => $libro->recommendation_score ?? 0
                ];
            })
        ]);
    }

    /**
     * Actualizar preferencias del usuario
     */
    public function updatePreferences(Request $request)
    {
        $request->validate([
            'categorias' => 'array',
            'autores' => 'array',
            'rating_min' => 'numeric|min:0|max:5',
            'rating_max' => 'numeric|min:0|max:5',
            'años' => 'array'
        ]);

        $user = Auth::user();
        
        // Guardar preferencias en la base de datos
        $preferencias = [
            'categorias' => $request->categorias ?? [],
            'autores' => $request->autores ?? [],
            'rating_min' => $request->rating_min ?? 0,
            'rating_max' => $request->rating_max ?? 5,
            'años' => $request->años ?? []
        ];

        $user->update([
            'preferences' => json_encode($preferencias)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Preferencias actualizadas correctamente'
        ]);
    }

    /**
     * Obtener estadísticas de recomendaciones
     */
    public function getRecommendationStats()
    {
        $user = Auth::user();
        
        $stats = [
            'total_libros_leidos' => $user->prestamos()->where('estado', 'devuelto')->count(),
            'categorias_favoritas' => $this->getFavoriteCategories($user)->count(),
            'autores_favoritos' => $this->getFavoriteAuthors($user)->count(),
            'rating_promedio' => $this->getAverageRating($user),
            'libros_favoritos' => $user->favoritos()->count(),
            'recomendaciones_disponibles' => $this->getPersonalizedRecommendations($user)->count()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Obtener autores favoritos del usuario
     */
    private function getFavoriteAuthors($user)
    {
        return Prestamo::where('usuario_id', $user->id)
            ->where('estado', 'devuelto')
            ->join('libros', 'prestamos.libro_id', '=', 'libros.id')
            ->select('libros.autor_id', DB::raw('COUNT(*) as total'))
            ->groupBy('libros.autor_id')
            ->orderBy('total', 'desc')
            ->take(5)
            ->pluck('autor_id');
    }

    /**
     * Obtener rating promedio del usuario
     */
    private function getAverageRating($user)
    {
        return Resena::where('usuario_id', $user->id)->avg('rating') ?? 0;
    }
} 