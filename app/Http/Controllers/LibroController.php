<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use App\Models\Autor;
use App\Models\Categoria;
use App\Models\Favorito;
use App\Models\Resena;
use App\Models\Prestamo;
use App\Models\Reserva;
use App\Models\Compra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LibroController extends Controller
{
    /**
     * Mostrar catálogo de libros con filtros avanzados
     */
    public function catalog(Request $request)
    {
        $query = Libro::with(['autor', 'categoria', 'resenas'])
            ->where('estado', 'disponible');

        // Filtros de búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                  ->orWhere('sinopsis', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhereHas('autor', function($q) use ($search) {
                      $q->where('nombre', 'like', "%{$search}%")
                        ->orWhere('apellido', 'like', "%{$search}%")
                        ->orWhere(DB::raw("CONCAT(nombre, ' ', apellido)"), 'like', "%{$search}%");
                  })
                  ->orWhereHas('categoria', function($q) use ($search) {
                      $q->where('nombre', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro por categoría
        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->categoria);
        }

        // Filtro por autor
        if ($request->filled('autor')) {
            $query->where('autor_id', $request->autor);
        }

        // Filtro por año de publicación
        if ($request->filled('anio')) {
            $query->whereYear('anio_publicacion', $request->anio);
        }

        // Filtro por idioma
        if ($request->filled('idioma')) {
            $query->where('idioma', $request->idioma);
        }
        
        // Eliminar toda referencia a 'tipo' en consultas a la tabla libros
        
        // Filtro por precio
        if ($request->filled('precio_min')) {
            $query->where('precio', '>=', $request->precio_min);
        }
        if ($request->filled('precio_max')) {
            $query->where('precio', '<=', $request->precio_max);
        }

        // Ordenamiento
        $orden = $request->get('orden', 'titulo');
        $direccion = $request->get('direccion', 'asc');
        
        switch ($orden) {
            case 'anio_publicacion':
                $query->orderBy('anio_publicacion', $direccion);
                break;
            case 'precio':
                $query->orderBy('precio', $direccion);
                break;
            case 'popularidad':
                $query->withCount('prestamos')->orderBy('prestamos_count', $direccion);
                break;
            default:
                $query->orderBy('titulo', $direccion);
        }
        
        // Vista (grid o lista)
        $vista = $request->get('vista', 'grid');
        
        // Paginación
        $per_page = $request->get('per_page', 12);
        $libros = $query->paginate($per_page);
        
        // Datos para filtros
        $categorias = Categoria::orderBy('nombre')->get();
        $autores = Autor::orderBy('nombre')->get();
        $anios = Libro::distinct()->pluck(DB::raw('YEAR(anio_publicacion)'))->sort()->reverse();
        $idiomas = Libro::distinct()->pluck('idioma')->filter()->sort();
        
        // Libros populares para sidebar
        $libros_populares = Libro::with(['autor', 'categoria'])
            ->withCount('prestamos')
            ->orderBy('prestamos_count', 'desc')
            ->limit(5)
            ->get();
        
        // Libros recientes
        $libros_recientes = Libro::with(['autor', 'categoria'])
            ->orderBy('anio_publicacion', 'desc')
            ->limit(5)
            ->get();
        
        // Estadísticas del catálogo
        $stats = [
            'total_libros' => Libro::where('estado', 'disponible')->count(),
            'total_categorias' => Categoria::count(),
            'total_autores' => Autor::count(),
            'libros_disponibles' => Libro::where('estado', 'disponible')->count()
        ];
        
        return view('books.catalog', compact(
            'libros',
            'categorias',
            'autores',
            'anios',
            'idiomas',
            'libros_populares',
            'libros_recientes',
            'stats',
            'vista'
        ));
    }

    /**
     * Mostrar libro específico
     */
    public function show($id)
    {
        $libro = Libro::with(['autor', 'categoria', 'resenas.usuario'])
            ->findOrFail($id);
        
        // Incrementar vistas
        $libro->increment('vistas');
        
        // Verificar si el usuario está autenticado
        $user = Auth::user();
        $es_favorito = false;
        $tiene_resena = false;
        $resena_usuario = null;
        $prestamo_activo = null;
        $reserva_activa = null;
        
        if ($user) {
            // Verificar si está en favoritos
            $es_favorito = Favorito::where('usuario_id', $user->id)
                ->where('libro_id', $libro->id)
                ->exists();
            
            // Verificar si tiene reseña
            $resena_usuario = Resena::where('usuario_id', $user->id)
                ->where('libro_id', $libro->id)
                ->first();
            $tiene_resena = $resena_usuario !== null;
            
            // Verificar préstamo activo
            $prestamo_activo = Prestamo::where('usuario_id', $user->id)
                ->where('libro_id', $libro->id)
                ->where('estado', 'prestado')
                ->first();
            
            // Verificar reserva activa
            $reserva_activa = Reserva::where('usuario_id', $user->id)
                ->where('libro_id', $libro->id)
                ->where('estado', 'pendiente')
                ->first();
        }
        
        // Reseñas (todas, sin filtrar por estado)
        $resenas = Resena::with('usuario')
            ->where('libro_id', $libro->id)
            ->orderBy('created_at', 'desc')
            ->paginate(5);
        
        // Otros libros (no relacionados, solo diferentes al actual)
        $libros_relacionados = Libro::with(['autor', 'categoria'])
            ->where('id', '!=', $libro->id)
            ->where('estado', 'disponible')
            ->inRandomOrder()
            ->limit(6)
            ->get();

        // Estadísticas del libro
        $stats_libro = [
            'total_prestamos' => Prestamo::where('libro_id', $libro->id)->count(),
            'total_reservas' => Reserva::where('libro_id', $libro->id)->count(),
            'total_favoritos' => Favorito::where('libro_id', $libro->id)->count(),
            'total_resenas' => Resena::where('libro_id', $libro->id)->count(),
            'promedio_rating' => Resena::where('libro_id', $libro->id)->avg('calificacion') ?? 0
        ];
        
        return view('books.show', compact(
            'libro',
            'es_favorito',
            'tiene_resena',
            'resena_usuario',
            'prestamo_activo',
            'reserva_activa',
            'resenas',
            'libros_relacionados',
            'stats_libro'
        ));
    }

    /**
     * Solicitar préstamo de libro
     */
    public function requestLoan($id)
    {
        $user = Auth::user();
        $libro = Libro::findOrFail($id);
        
        // Verificar si el libro está disponible
        if ($libro->estado !== 'disponible') {
            return back()->with('error', 'El libro no está disponible para préstamo.');
        }
        
        // Verificar si el usuario ya tiene un préstamo activo de este libro
        $prestamo_existente = Prestamo::where('usuario_id', $user->id)
            ->where('libro_id', $libro->id)
            ->whereIn('estado', ['prestado', 'vencido'])
            ->first();
        
        if ($prestamo_existente) {
            return back()->with('error', 'Ya tienes un préstamo activo de este libro.');
        }
        
        // Verificar límite de préstamos del usuario
        $prestamos_activos = Prestamo::where('usuario_id', $user->id)
            ->whereIn('estado', ['prestado', 'vencido'])
            ->count();
        
        if ($prestamos_activos >= 5) {
            return back()->with('error', 'Has alcanzado el límite máximo de préstamos activos (5).');
        }
        
        // Crear préstamo
        $prestamo = Prestamo::create([
            'usuario_id' => $user->id,
            'libro_id' => $libro->id,
            'fecha_prestamo' => now(),
            'fecha_devolucion' => now()->addDays(15),
            'estado' => 'prestado',
            'renovaciones' => 0
        ]);
        
        // Actualizar disponibilidad del libro
        $libro->estado = 'no_disponible';
        $libro->save();
        
        // Crear notificación
        \App\Models\Notificacion::create([
            'usuario_id' => $user->id,
            'titulo' => 'Préstamo Realizado',
            'mensaje' => "Has prestado '{$libro->titulo}'. Fecha de devolución: " . $prestamo->fecha_devolucion->format('d/m/Y'),
            'tipo' => 'prestamo',
            'leida' => false
        ]);
        
        return back()->with('success', 'Préstamo realizado exitosamente. Fecha de devolución: ' . $prestamo->fecha_devolucion->format('d/m/Y'));
    }

    /**
     * Reservar libro
     */
    public function reserveBook($id)
    {
        $user = Auth::user();
        $libro = Libro::findOrFail($id);
        
        // Verificar si ya tiene una reserva activa
        $reserva_existente = Reserva::where('usuario_id', $user->id)
            ->where('libro_id', $libro->id)
            ->where('estado', 'pendiente')
            ->first();
        
        if ($reserva_existente) {
            return back()->with('error', 'Ya tienes una reserva activa de este libro.');
        }
        
        // Verificar límite de reservas
        $reservas_activas = Reserva::where('usuario_id', $user->id)
            ->where('estado', 'pendiente')
            ->count();
        
        if ($reservas_activas >= 3) {
            return back()->with('error', 'Has alcanzado el límite máximo de reservas activas (3).');
        }
        
        // Crear reserva
        $reserva = Reserva::create([
            'usuario_id' => $user->id,
            'libro_id' => $libro->id,
            'fecha_reserva' => now(),
            'estado' => 'pendiente'
        ]);
        
        // Crear notificación
        \App\Models\Notificacion::create([
            'usuario_id' => $user->id,
            'titulo' => 'Libro Reservado',
            'mensaje' => "Has reservado '{$libro->titulo}'. Te notificaremos cuando esté disponible.",
            'tipo' => 'reserva',
            'leida' => false
        ]);
        
        return back()->with('success', 'Libro reservado exitosamente. Te notificaremos cuando esté disponible.');
    }

    /**
     * Comprar libro
     */
    public function purchaseBook($id)
    {
        $user = Auth::user();
        $libro = Libro::findOrFail($id);
        
        // Verificar si ya lo compró
        $compra_existente = Compra::where('usuario_id', $user->id)
            ->where('libro_id', $libro->id)
            ->first();
        
        if ($compra_existente) {
            return back()->with('error', 'Ya has comprado este libro.');
        }
        
        // Eliminar cualquier uso de $libro->tipo y lógica de compra basada en 'tipo'
        // Usar solo los campos existentes en la tabla libros
        
        return view('books.purchase', compact('libro'));
    }

    /**
     * Procesar compra
     */
    public function processPurchase(Request $request, $id)
    {
        $request->validate([
            'metodo_pago' => 'required|in:tarjeta,paypal,transferencia,yape',
            'tipo_transaccion' => 'required|in:comprar,prestar',
            'modalidad' => 'required|in:fisico,online',
        ]);
        
        $user = Auth::user();
        $libro = Libro::findOrFail($id);
        $tipo = $request->input('tipo_transaccion');
        $modalidad = $request->input('modalidad');
        $precio = null;
        $dias_prestamo = null;

        // Determinar precio y días de préstamo según selección
        if ($tipo === 'comprar') {
            if ($modalidad === 'fisico') {
                $precio = $libro->precio_compra_fisica;
            } elseif ($modalidad === 'online') {
                $precio = $libro->precio_compra_online;
            }
        } elseif ($tipo === 'prestar') {
            if ($modalidad === 'fisico') {
                $precio = $libro->precio_prestamo_fisico;
                $dias_prestamo = 14;
            } elseif ($modalidad === 'online') {
                $precio = $libro->precio_prestamo_online;
                $dias_prestamo = 7;
            }
        }

        if ($precio === null) {
            return back()->with('error', 'No se pudo determinar el precio para la opción seleccionada.');
        }
        
        // Verificar si ya lo compró o prestó
        if ($tipo === 'comprar') {
            $compra_existente = \App\Models\Compra::where('usuario_id', $user->id)
            ->where('libro_id', $libro->id)
                ->where('modalidad', $modalidad)
            ->first();
        if ($compra_existente) {
                return back()->with('error', 'Ya has comprado este libro en esta modalidad.');
        }
        // Crear compra
            $compra = \App\Models\Compra::create([
            'usuario_id' => $user->id,
            'libro_id' => $libro->id,
                'precio' => $precio,
                'modalidad' => $modalidad,
                'estado' => 'completada',
        ]);
            // Notificación
        \App\Models\Notificacion::create([
            'usuario_id' => $user->id,
            'titulo' => 'Compra Exitosa',
                'mensaje' => "Has comprado '{$libro->titulo}' ({$modalidad}) por S/" . number_format($precio, 2),
            'tipo' => 'success', // Cambiado de 'compra' a 'success'
            'leida' => false
        ]);
        return redirect()->route('user.dashboard')->with('success', '¡Compra realizada exitosamente! El libro ya está disponible en tu panel.');
        } elseif ($tipo === 'prestar') {
            $prestamo_existente = \App\Models\Prestamo::where('usuario_id', $user->id)
                ->where('libro_id', $libro->id)
                ->where('modalidad', $modalidad)
                ->whereIn('estado', ['prestado', 'vencido'])
                ->first();
            if ($prestamo_existente) {
                return back()->with('error', 'Ya tienes un préstamo activo de este libro en esta modalidad.');
            }
            // Crear préstamo
            $prestamo = \App\Models\Prestamo::create([
                'usuario_id' => $user->id,
                'libro_id' => $libro->id,
                'fecha_prestamo' => now(),
                'fecha_devolucion' => now()->addDays($dias_prestamo),
                'precio' => $precio,
                'modalidad' => $modalidad,
                'estado' => 'prestado',
                'renovaciones' => 0
            ]);
            // Notificación
            \App\Models\Notificacion::create([
                'usuario_id' => $user->id,
                'titulo' => 'Préstamo Realizado',
                'mensaje' => "Has prestado '{$libro->titulo}' ({$modalidad}). Fecha de devolución: " . $prestamo->fecha_devolucion->format('d/m/Y'),
                'tipo' => 'info', // Cambiado de 'prestamo' a 'info'
                'leida' => false
            ]);
            return redirect()->route('user.dashboard')->with('success', '¡Préstamo realizado exitosamente! El libro ya está disponible en tu panel.');
        }
        return back()->with('error', 'Ocurrió un error inesperado.');
    }

    /**
     * Página de éxito de compra
     */
    public function purchaseSuccess($id)
    {
        $compra = Compra::with(['libro', 'libro.autor'])
            ->where('usuario_id', Auth::id())
            ->findOrFail($id);
        
        return view('books.purchase-success', compact('compra'));
    }

    /**
     * Leer libro (para libros comprados o prestados)
     */
    public function readBook($id)
    {
        $libro = Libro::findOrFail($id);
        $user = Auth::user();
        $tieneAcceso = false;
        $prestamo = null;
        
        if ($user) {
            // Verificar si tiene préstamo activo
            $prestamo = Prestamo::where('usuario_id', $user->id)
                ->where('libro_id', $id)
                ->where('estado', 'prestado')
                ->first();
            
            // Verificar si lo compró
            $compra = Compra::where('usuario_id', $user->id)
                ->where('libro_id', $id)
                ->first();
            
            $tieneAcceso = $prestamo || $compra;
        }
        
        return view('books.read', compact('libro', 'tieneAcceso', 'prestamo'));
    }

    /**
     * Búsqueda avanzada
     */
    public function advancedSearch(Request $request)
    {
        $query = Libro::with(['autor', 'categoria'])
            ->where('estado', 'disponible');
        
        // Búsqueda por texto
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhereHas('autor', function($q) use ($search) {
                      $q->where('nombre', 'like', "%{$search}%");
                  })
                  ->orWhereHas('categoria', function($q) use ($search) {
                      $q->where('nombre', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filtros avanzados
        if ($request->filled('categorias')) {
            $query->whereIn('categoria_id', $request->categorias);
        }
        
        if ($request->filled('autores')) {
            $query->whereIn('autor_id', $request->autores);
        }
        
        if ($request->filled('anio_min')) {
            $query->whereYear('anio_publicacion', '>=', $request->anio_min);
        }
        
        if ($request->filled('anio_max')) {
            $query->whereYear('anio_publicacion', '<=', $request->anio_max);
        }
        
        if ($request->filled('rating_min')) {
            $query->where('rating', '>=', $request->rating_min);
        }
        
        if ($request->filled('idiomas')) {
            $query->whereIn('idioma', $request->idiomas);
        }
        
        if ($request->filled('precio_min')) {
            $query->where('precio', '>=', $request->precio_min);
        }
        
        if ($request->filled('precio_max')) {
            $query->where('precio', '<=', $request->precio_max);
        }
        
        // Ordenamiento
        $orden = $request->get('orden', 'relevancia');
        switch ($orden) {
            case 'fecha':
                $query->orderBy('anio_publicacion', 'desc');
                break;
            case 'precio':
                $query->orderBy('precio', 'asc');
                break;
            case 'titulo':
                $query->orderBy('titulo', 'asc');
                break;
            default:
                // Orden por relevancia (rating + popularidad)
                $query->withCount('prestamos')
                      ->orderBy('prestamos_count', 'desc');
        }
        
        $libros = $query->paginate(20);
        
        // Datos para filtros
        $categorias = Categoria::orderBy('nombre')->get();
        $autores = Autor::orderBy('nombre')->get();
        $idiomas = Libro::distinct()->pluck('idioma')->filter()->sort();
        
        return view('books.advanced-search', compact(
            'libros',
            'categorias',
            'autores',
            'idiomas'
        ));
    }

    /**
     * Libros populares
     */
    public function popularBooks()
    {
        $libros = Libro::with(['autor', 'categoria'])
            ->withCount('prestamos')
            ->orderBy('prestamos_count', 'desc')
            ->paginate(20);
        
        return view('books.popular', compact('libros'));
    }

    /**
     * Libros recientes
     */
    public function recentBooks()
    {
        $libros = Libro::with(['autor', 'categoria'])
            ->orderBy('anio_publicacion', 'desc')
            ->paginate(20);
        
        return view('books.recent', compact('libros'));
    }

    /**
     * Libros por categoría
     */
    public function booksByCategory($categoria_id)
    {
        $categoria = Categoria::findOrFail($categoria_id);
        
        $libros = Libro::with(['autor', 'categoria'])
            ->where('categoria_id', $categoria_id)
            ->where('estado', 'disponible')
            ->orderBy('titulo')
            ->paginate(20);
        
        return view('books.by-category', compact('libros', 'categoria'));
    }

    /**
     * Libros por autor
     */
    public function booksByAuthor($autor_id)
    {
        $autor = Autor::findOrFail($autor_id);
        
        $libros = Libro::with(['autor', 'categoria'])
            ->where('autor_id', $autor_id)
            ->where('estado', 'disponible')
            ->orderBy('anio_publicacion', 'desc')
            ->paginate(20);
        
        return view('books.by-author', compact('libros', 'autor'));
    }

    /**
     * Recomendaciones personalizadas
     */
    public function recommendations()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Obtener recomendaciones basadas en historial
        $recomendaciones = $this->getPersonalizedRecommendations($user);
        
        // Libros populares en categorías favoritas
        $categorias_favoritas = Prestamo::join('libros', 'prestamos.libro_id', '=', 'libros.id')
            ->join('categorias', 'libros.categoria_id', '=', 'categorias.id')
            ->where('prestamos.usuario_id', $user->id)
            ->select('categorias.id', 'categorias.nombre', DB::raw('COUNT(*) as total'))
            ->groupBy('categorias.id', 'categorias.nombre')
            ->orderBy('total', 'desc')
            ->limit(3)
            ->pluck('categorias.id');
        
        $libros_populares_categoria = Libro::with(['autor', 'categoria'])
            ->withCount('prestamos')
            ->whereIn('categoria_id', $categorias_favoritas)
            ->where('estado', 'disponible')
            ->whereNotIn('id', function($query) use ($user) {
                $query->select('libro_id')
                      ->from('prestamos')
                      ->where('usuario_id', $user->id);
            })
            ->orderBy('prestamos_count', 'desc')
            ->limit(10)
            ->get();
        
        return view('books.recommendations', compact('recomendaciones', 'libros_populares_categoria'));
    }

    /**
     * Obtener recomendaciones personalizadas
     */
    private function getPersonalizedRecommendations($user)
    {
        // Algoritmo de recomendación basado en colaboración
        $libros_leidos = Prestamo::where('usuario_id', $user->id)
            ->pluck('libro_id')
            ->toArray();
        
        if (empty($libros_leidos)) {
            // Si no tiene historial, mostrar libros populares
            return Libro::with(['autor', 'categoria'])
                ->withCount('prestamos')
                ->where('estado', 'disponible')
                ->orderBy('prestamos_count', 'desc')
                ->limit(10)
                ->get();
        }
        
        // Encontrar usuarios similares
        $usuarios_similares = Prestamo::whereIn('libro_id', $libros_leidos)
            ->where('usuario_id', '!=', $user->id)
            ->select('usuario_id', DB::raw('COUNT(*) as libros_comunes'))
            ->groupBy('usuario_id')
            ->having('libros_comunes', '>=', 2)
            ->orderBy('libros_comunes', 'desc')
            ->limit(10)
            ->pluck('usuario_id');
        
        if ($usuarios_similares->isEmpty()) {
            // Si no hay usuarios similares, usar recomendaciones basadas en contenido
            return $this->getContentBasedRecommendations($user);
        }
        
        // Obtener libros que les gustan a usuarios similares
        $libros_recomendados = Prestamo::whereIn('usuario_id', $usuarios_similares)
            ->whereNotIn('libro_id', $libros_leidos)
            ->select('libro_id', DB::raw('COUNT(*) as popularidad'))
            ->groupBy('libro_id')
            ->orderBy('popularidad', 'desc')
            ->limit(10)
            ->pluck('libro_id');
        
        return Libro::with(['autor', 'categoria'])
            ->whereIn('id', $libros_recomendados)
            ->where('estado', 'disponible')
            ->get();
    }

    /**
     * Recomendaciones basadas en contenido
     */
    private function getContentBasedRecommendations($user)
    {
        // Obtener características de libros leídos
        $categorias_favoritas = Prestamo::join('libros', 'prestamos.libro_id', '=', 'libros.id')
            ->join('categorias', 'libros.categoria_id', '=', 'categorias.id')
            ->where('prestamos.usuario_id', $user->id)
            ->select('categorias.id', DB::raw('COUNT(*) as peso'))
            ->groupBy('categorias.id')
            ->orderBy('peso', 'desc')
            ->limit(3)
            ->pluck('categorias.id');
        
        $autores_favoritos = Prestamo::join('libros', 'prestamos.libro_id', '=', 'libros.id')
            ->join('autors', 'libros.autor_id', '=', 'autors.id')
            ->where('prestamos.usuario_id', $user->id)
            ->select('autors.id', DB::raw('COUNT(*) as peso'))
            ->groupBy('autors.id')
            ->orderBy('peso', 'desc')
            ->limit(3)
            ->pluck('autors.id');
        
        return Libro::with(['autor', 'categoria'])
            ->where(function($query) use ($categorias_favoritas, $autores_favoritos) {
                $query->whereIn('categoria_id', $categorias_favoritas)
                      ->orWhereIn('autor_id', $autores_favoritos);
            })
            ->where('estado', 'disponible')
            ->whereNotIn('id', function($query) use ($user) {
                $query->select('libro_id')
                      ->from('prestamos')
                      ->where('usuario_id', $user->id);
            })
            ->orderBy('anio_publicacion', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Exportar catálogo
     */
    public function exportCatalog(Request $request)
    {
        $query = Libro::with(['autor', 'categoria'])
            ->where('estado', 'disponible');
        
        // Aplicar filtros si existen
        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->categoria);
        }
        
        if ($request->filled('autor')) {
            $query->where('autor_id', $request->autor);
        }
        
        $libros = $query->get();
        
        $filename = 'catalogo_libros_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($libros) {
            $file = fopen('php://output', 'w');
            
            // Headers del CSV
            fputcsv($file, [
                'ID', 'Título', 'Autor', 'Categoría', 'ISBN', 'Año Publicación',
                'Idioma', 'Precio', 'Rating', 'Descripción'
            ]);
            
            // Datos
            foreach ($libros as $libro) {
                fputcsv($file, [
                    $libro->id,
                    $libro->titulo,
                    $libro->autor->nombre ?? '',
                    $libro->categoria->nombre ?? '',
                    $libro->isbn,
                    $libro->anio_publicacion ? $libro->anio_publicacion->format('Y') : '',
                    $libro->idioma,
                    $libro->precio,
                    $libro->rating,
                    $libro->descripcion
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * API para búsqueda en tiempo real
     */
    public function searchApi(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $libros = Libro::with(['autor', 'categoria'])
            ->where('estado', 'disponible')
            ->where(function($q) use ($query) {
                $q->where('titulo', 'like', "%{$query}%")
                  ->orWhereHas('autor', function($q) use ($query) {
                      $q->where('nombre', 'like', "%{$query}%");
                  });
            })
            ->limit(10)
            ->get()
            ->map(function($libro) {
                return [
                    'id' => $libro->id,
                    'titulo' => $libro->titulo,
                    'autor' => $libro->autor->nombre ?? '',
                    'categoria' => $libro->categoria->nombre ?? '',
                    'url' => route('books.show', $libro->id)
                ];
            });
        
        return response()->json($libros);
    }

    public function create()
    {
        $autores = \App\Models\Autor::all();
        $categorias = \App\Models\Categoria::all();
        return view('admin.books.create', compact('autores', 'categorias'));
    }

    public function edit($id)
    {
        $libro = Libro::findOrFail($id);
        $autores = \App\Models\Autor::all();
        $categorias = \App\Models\Categoria::all();
        return view('admin.books.edit', compact('libro', 'autores', 'categorias'));
    }

    public function createPrestamo(Request $request, $id)
    {
        $user = auth()->user();
        $libro = \App\Models\Libro::findOrFail($id);

        // Verificar disponibilidad
        if ($libro->estado !== 'disponible') {
            return back()->with('error', 'El libro no está disponible para préstamo.');
        }

        // Verificar si ya tiene un préstamo activo
        $prestamoExistente = \App\Models\Prestamo::where('usuario_id', $user->id)
            ->where('libro_id', $libro->id)
            ->whereIn('estado', ['prestado', 'vencido'])
            ->first();
        if ($prestamoExistente) {
            return back()->with('error', 'Ya tienes un préstamo activo para este libro.');
        }

        // Registrar préstamo
        $fecha_prestamo = now();
        $fecha_devolucion = now()->addDays(14);
        \App\Models\Prestamo::create([
            'usuario_id' => $user->id,
            'libro_id' => $libro->id,
            'fecha_prestamo' => $fecha_prestamo,
            'fecha_devolucion_esperada' => $fecha_devolucion,
            'estado' => 'prestado',
        ]);

        // Cambiar estado del libro
        $libro->estado = 'prestado';
        $libro->save();

        return back()->with('success', '¡Préstamo realizado con éxito!');
    }

    public function deleteBook($id)
    {
        $user = Auth::user();
        $libro = Libro::findOrFail($id);
        $titulo = $libro->titulo;
        $libro->delete();
        // Notificación de eliminación
        \App\Models\Notificacion::create([
            'usuario_id' => $user->id,
            'titulo' => 'Libro eliminado',
            'mensaje' => "Has eliminado el libro '{$titulo}' de la biblioteca.",
            'tipo' => 'libro',
            'leida' => false
        ]);
        return back()->with('success', 'Libro eliminado exitosamente.');
    }
}
