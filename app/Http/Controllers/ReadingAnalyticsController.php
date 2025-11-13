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

class ReadingAnalyticsController extends Controller
{
    /**
     * Mostrar página de análisis de lectura
     */
    public function index()
    {
        $user = Auth::user();
        
        // Obtener estadísticas generales
        $stats = $this->getReadingStats($user);
        
        // Obtener hábitos de lectura
        $habits = $this->getReadingHabits($user);
        
        // Obtener preferencias
        $preferences = $this->getReadingPreferences($user);
        
        // Obtener logros
        $achievements = $this->getAchievements($user);
        
        // Obtener insights personalizados
        $insights = $this->getPersonalizedInsights($user);
        
        return view('user.reading-analytics', compact('stats', 'habits', 'preferences', 'achievements', 'insights'));
    }

    /**
     * Obtener estadísticas generales de lectura
     */
    private function getReadingStats($user)
    {
        $total_books = $user->prestamos()->where('estado', 'devuelto')->count();
        $total_hours = $this->calculateTotalReadingHours($user);
        $avg_rating = $this->calculateAverageRating($user);
        $reading_streak = $this->calculateReadingStreak($user);
        
        // Calcular tasas de crecimiento
        $growth_rate = $this->calculateGrowthRate($user);
        $time_growth = $this->calculateTimeGrowth($user);
        
        return [
            'total_books' => $total_books,
            'total_hours' => $total_hours,
            'avg_rating' => $avg_rating,
            'reading_streak' => $reading_streak,
            'growth_rate' => $growth_rate,
            'time_growth' => $time_growth
        ];
    }

    /**
     * Calcular horas totales de lectura
     */
    private function calculateTotalReadingHours($user)
    {
        $prestamos = $user->prestamos()->where('estado', 'devuelto')->get();
        $total_hours = 0;
        
        foreach ($prestamos as $prestamo) {
            // Estimación basada en páginas y velocidad promedio
            $pages = $prestamo->libro->numero_paginas ?? 300;
            $hours_per_book = $pages / 50; // 50 páginas por hora promedio
            $total_hours += $hours_per_book;
        }
        
        return round($total_hours);
    }

    /**
     * Calcular rating promedio
     */
    private function calculateAverageRating($user)
    {
        return $user->resenas()->avg('rating') ?? 0;
    }

    /**
     * Calcular racha de lectura
     */
    private function calculateReadingStreak($user)
    {
        $prestamos = $user->prestamos()
            ->where('estado', 'devuelto')
            ->orderBy('fecha_devolucion', 'desc')
            ->get();
        
        if ($prestamos->isEmpty()) {
            return 0;
        }
        
        $streak = 0;
        $current_date = Carbon::now();
        
        foreach ($prestamos as $prestamo) {
            $devolucion_date = Carbon::parse($prestamo->fecha_devolucion);
            $days_diff = $current_date->diffInDays($devolucion_date);
            
            if ($days_diff <= $streak + 1) {
                $streak++;
            } else {
                break;
            }
        }
        
        return $streak;
    }

    /**
     * Calcular tasa de crecimiento
     */
    private function calculateGrowthRate($user)
    {
        $current_month = $user->prestamos()
            ->where('estado', 'devuelto')
            ->whereMonth('fecha_devolucion', Carbon::now()->month)
            ->count();
        
        $last_month = $user->prestamos()
            ->where('estado', 'devuelto')
            ->whereMonth('fecha_devolucion', Carbon::now()->subMonth()->month)
            ->count();
        
        if ($last_month == 0) {
            return $current_month > 0 ? 100 : 0;
        }
        
        return round((($current_month - $last_month) / $last_month) * 100);
    }

    /**
     * Calcular crecimiento de tiempo
     */
    private function calculateTimeGrowth($user)
    {
        $current_month_hours = $this->calculateMonthlyReadingHours($user, Carbon::now()->month);
        $last_month_hours = $this->calculateMonthlyReadingHours($user, Carbon::now()->subMonth()->month);
        
        if ($last_month_hours == 0) {
            return $current_month_hours > 0 ? 100 : 0;
        }
        
        return round((($current_month_hours - $last_month_hours) / $last_month_hours) * 100);
    }

    /**
     * Calcular horas de lectura por mes
     */
    private function calculateMonthlyReadingHours($user, $month)
    {
        $prestamos = $user->prestamos()
            ->where('estado', 'devuelto')
            ->whereMonth('fecha_devolucion', $month)
            ->get();
        
        $total_hours = 0;
        foreach ($prestamos as $prestamo) {
            $pages = $prestamo->libro->numero_paginas ?? 300;
            $hours_per_book = $pages / 50;
            $total_hours += $hours_per_book;
        }
        
        return round($total_hours);
    }

    /**
     * Obtener hábitos de lectura
     */
    private function getReadingHabits($user)
    {
        $prestamos = $user->prestamos()
            ->where('estado', 'devuelto')
            ->with('libro')
            ->get();
        
        // Día favorito
        $days_count = [];
        foreach ($prestamos as $prestamo) {
            $day = Carbon::parse($prestamo->fecha_devolucion)->format('l');
            $days_count[$day] = ($days_count[$day] ?? 0) + 1;
        }
        arsort($days_count);
        $favorite_day = array_key_first($days_count) ?? 'N/A';
        
        // Hora pico (estimación basada en devoluciones)
        $hours_count = [];
        foreach ($prestamos as $prestamo) {
            $hour = Carbon::parse($prestamo->fecha_devolucion)->format('H');
            $hours_count[$hour] = ($hours_count[$hour] ?? 0) + 1;
        }
        arsort($hours_count);
        $peak_hour = array_key_first($hours_count) ?? 'N/A';
        
        // Promedio de libros por mes
        $total_months = max(1, Carbon::now()->diffInMonths($user->created_at));
        $avg_books_per_month = round($prestamos->count() / $total_months, 1);
        
        // Tiempo promedio por libro
        $avg_time_per_book = $this->calculateAverageTimePerBook($user);
        
        return [
            'favorite_day' => $this->translateDay($favorite_day),
            'peak_hour' => $peak_hour . ':00',
            'avg_books_per_month' => $avg_books_per_month,
            'avg_time_per_book' => $avg_time_per_book
        ];
    }

    /**
     * Traducir día de la semana
     */
    private function translateDay($day)
    {
        $translations = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo'
        ];
        
        return $translations[$day] ?? $day;
    }

    /**
     * Calcular tiempo promedio por libro
     */
    private function calculateAverageTimePerBook($user)
    {
        $prestamos = $user->prestamos()
            ->where('estado', 'devuelto')
            ->get();
        
        if ($prestamos->isEmpty()) {
            return 0;
        }
        
        $total_days = 0;
        foreach ($prestamos as $prestamo) {
            $fecha_prestamo = Carbon::parse($prestamo->fecha_prestamo);
            $fecha_devolucion = Carbon::parse($prestamo->fecha_devolucion);
            $total_days += $fecha_prestamo->diffInDays($fecha_devolucion);
        }
        
        return round($total_days / $prestamos->count());
    }

    /**
     * Obtener preferencias de lectura
     */
    private function getReadingPreferences($user)
    {
        $prestamos = $user->prestamos()
            ->where('estado', 'devuelto')
            ->with(['libro.categoria', 'libro.autor'])
            ->get();
        
        // Categoría favorita
        $categorias_count = [];
        foreach ($prestamos as $prestamo) {
            $categoria_id = $prestamo->libro->categoria_id;
            $categorias_count[$categoria_id] = ($categorias_count[$categoria_id] ?? 0) + 1;
        }
        arsort($categorias_count);
        $favorite_category_id = array_key_first($categorias_count);
        $favorite_category = $favorite_category_id ? Categoria::find($favorite_category_id)->nombre : 'N/A';
        
        // Autor favorito
        $autores_count = [];
        foreach ($prestamos as $prestamo) {
            $autor_id = $prestamo->libro->autor_id;
            $autores_count[$autor_id] = ($autores_count[$autor_id] ?? 0) + 1;
        }
        arsort($autores_count);
        $favorite_author_id = array_key_first($autores_count);
        $favorite_author = $favorite_author_id ? $prestamos->firstWhere('libro.autor_id', $favorite_author_id)->libro->autor->nombre : 'N/A';
        
        // Año preferido
        $años_count = [];
        foreach ($prestamos as $prestamo) {
            if ($prestamo->libro->anio_publicacion) {
                $año = $prestamo->libro->anio_publicacion;
                $años_count[$año] = ($años_count[$año] ?? 0) + 1;
            }
        }
        arsort($años_count);
        $favorite_year = array_key_first($años_count) ?? 'N/A';
        
        // Rating promedio dado
        $avg_rating_given = $user->resenas()->avg('rating') ?? 0;
        
        return [
            'favorite_category' => $favorite_category,
            'favorite_author' => $favorite_author,
            'favorite_year' => $favorite_year,
            'avg_rating_given' => $avg_rating_given
        ];
    }

    /**
     * Obtener logros del usuario
     */
    private function getAchievements($user)
    {
        $total_books = $user->prestamos()->where('estado', 'devuelto')->count();
        $total_reviews = $user->resenas()->count();
        $reading_streak = $this->calculateReadingStreak($user);
        $favorites_count = $user->favoritos()->count();
        
        return [
            [
                'title' => 'Primer Lector',
                'description' => 'Lee tu primer libro',
                'icon' => 'fas fa-book',
                'current' => $total_books,
                'target' => 1,
                'progress' => min(100, ($total_books / 1) * 100),
                'completed' => $total_books >= 1
            ],
            [
                'title' => 'Lector Ávido',
                'description' => 'Lee 10 libros',
                'icon' => 'fas fa-book-open',
                'current' => $total_books,
                'target' => 10,
                'progress' => min(100, ($total_books / 10) * 100),
                'completed' => $total_books >= 10
            ],
            [
                'title' => 'Crítico Literario',
                'description' => 'Escribe 5 reseñas',
                'icon' => 'fas fa-star',
                'current' => $total_reviews,
                'target' => 5,
                'progress' => min(100, ($total_reviews / 5) * 100),
                'completed' => $total_reviews >= 5
            ],
            [
                'title' => 'Racha de Lectura',
                'description' => 'Lee durante 7 días consecutivos',
                'icon' => 'fas fa-fire',
                'current' => $reading_streak,
                'target' => 7,
                'progress' => min(100, ($reading_streak / 7) * 100),
                'completed' => $reading_streak >= 7
            ],
            [
                'title' => 'Coleccionista',
                'description' => 'Agrega 10 libros a favoritos',
                'icon' => 'fas fa-heart',
                'current' => $favorites_count,
                'target' => 10,
                'progress' => min(100, ($favorites_count / 10) * 100),
                'completed' => $favorites_count >= 10
            ],
            [
                'title' => 'Lector Experto',
                'description' => 'Lee 50 libros',
                'icon' => 'fas fa-trophy',
                'current' => $total_books,
                'target' => 50,
                'progress' => min(100, ($total_books / 50) * 100),
                'completed' => $total_books >= 50
            ]
        ];
    }

    /**
     * Obtener insights personalizados
     */
    private function getPersonalizedInsights($user)
    {
        $insights = [];
        
        // Insight sobre categorías
        $categorias_count = $user->prestamos()
            ->where('estado', 'devuelto')
            ->join('libros', 'prestamos.libro_id', '=', 'libros.id')
            ->select('libros.categoria_id', DB::raw('COUNT(*) as total'))
            ->groupBy('libros.categoria_id')
            ->orderBy('total', 'desc')
            ->get();
        
        if ($categorias_count->isNotEmpty()) {
            $top_category = $categorias_count->first();
            $insights[] = [
                'title' => 'Categoría Dominante',
                'description' => 'El ' . round(($top_category->total / $user->prestamos()->where('estado', 'devuelto')->count()) * 100) . '% de tus lecturas son de ' . Categoria::find($top_category->categoria_id)->nombre,
                'icon' => 'fas fa-chart-pie',
                'suggestion' => 'Considera explorar otras categorías para diversificar tu lectura'
            ];
        }
        
        // Insight sobre velocidad de lectura
        $avg_time = $this->calculateAverageTimePerBook($user);
        if ($avg_time > 0) {
            if ($avg_time < 7) {
                $insights[] = [
                    'title' => 'Lector Rápido',
                    'description' => 'Completas un libro en promedio en ' . $avg_time . ' días',
                    'icon' => 'fas fa-bolt',
                    'suggestion' => '¡Excelente ritmo! Considera libros más extensos para un mayor desafío'
                ];
            } elseif ($avg_time > 30) {
                $insights[] = [
                    'title' => 'Lector Pausado',
                    'description' => 'Tomas tu tiempo para disfrutar cada libro',
                    'icon' => 'fas fa-clock',
                    'suggestion' => 'Tu ritmo te permite absorber mejor el contenido'
                ];
            }
        }
        
        // Insight sobre ratings
        $avg_rating = $user->resenas()->avg('rating') ?? 0;
        if ($avg_rating > 0) {
            if ($avg_rating > 4) {
                $insights[] = [
                    'title' => 'Crítico Generoso',
                    'description' => 'Tu rating promedio es de ' . number_format($avg_rating, 1) . '/5',
                    'icon' => 'fas fa-star',
                    'suggestion' => 'Eres muy positivo con tus lecturas'
                ];
            } elseif ($avg_rating < 3) {
                $insights[] = [
                    'title' => 'Crítico Exigente',
                    'description' => 'Tu rating promedio es de ' . number_format($avg_rating, 1) . '/5',
                    'icon' => 'fas fa-star',
                    'suggestion' => 'Eres muy selectivo con tus calificaciones'
                ];
            }
        }
        
        // Insight sobre racha de lectura
        $reading_streak = $this->calculateReadingStreak($user);
        if ($reading_streak > 0) {
            $insights[] = [
                'title' => 'Racha Activa',
                'description' => 'Has leído durante ' . $reading_streak . ' días consecutivos',
                'icon' => 'fas fa-fire',
                'suggestion' => '¡Mantén la racha! La consistencia es clave para formar hábitos'
            ];
        }
        
        return $insights;
    }

    /**
     * Obtener datos para gráficos
     */
    public function getChartData(Request $request)
    {
        $user = Auth::user();
        $period = $request->get('period', 365);
        
        $data = [
            'reading_activity' => $this->getReadingActivityData($user, $period),
            'categories' => $this->getCategoriesData($user),
            'authors' => $this->getAuthorsData($user),
            'ratings' => $this->getRatingsData($user)
        ];
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Obtener datos de actividad de lectura
     */
    private function getReadingActivityData($user, $period)
    {
        $start_date = Carbon::now()->subDays($period);
        
        $activity = $user->prestamos()
            ->where('estado', 'devuelto')
            ->where('fecha_devolucion', '>=', $start_date)
            ->selectRaw('DATE(fecha_devolucion) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return $activity;
    }

    /**
     * Obtener datos de categorías
     */
    private function getCategoriesData($user)
    {
        return $user->prestamos()
            ->where('estado', 'devuelto')
            ->join('libros', 'prestamos.libro_id', '=', 'libros.id')
            ->join('categorias', 'libros.categoria_id', '=', 'categorias.id')
            ->select('categorias.nombre', DB::raw('COUNT(*) as count'))
            ->groupBy('categorias.id', 'categorias.nombre')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Obtener datos de autores
     */
    private function getAuthorsData($user)
    {
        return $user->prestamos()
            ->where('estado', 'devuelto')
            ->join('libros', 'prestamos.libro_id', '=', 'libros.id')
            ->join('autors', 'libros.autor_id', '=', 'autors.id')
            ->select('autors.nombre', DB::raw('COUNT(*) as count'))
            ->groupBy('autors.id', 'autors.nombre')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Obtener datos de ratings
     */
    private function getRatingsData($user)
    {
        return $user->resenas()
            ->select('rating', DB::raw('COUNT(*) as count'))
            ->groupBy('rating')
            ->get();
    }

    /**
     * Exportar datos de lectura
     */
    public function exportData(Request $request)
    {
        $user = Auth::user();
        $format = $request->get('format', 'csv');
        
        $data = [
            'stats' => $this->getReadingStats($user),
            'habits' => $this->getReadingHabits($user),
            'preferences' => $this->getReadingPreferences($user),
            'achievements' => $this->getAchievements($user),
            'insights' => $this->getPersonalizedInsights($user)
        ];
        
        switch ($format) {
            case 'json':
                return response()->json($data);
            case 'csv':
                return $this->exportToCsv($data);
            case 'pdf':
                return $this->exportToPdf($data);
            default:
                return response()->json($data);
        }
    }

    /**
     * Exportar a CSV
     */
    private function exportToCsv($data)
    {
        $filename = 'reading_analytics_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Escribir estadísticas
            fputcsv($file, ['Estadísticas de Lectura']);
            fputcsv($file, ['Métrica', 'Valor']);
            foreach ($data['stats'] as $key => $value) {
                fputcsv($file, [$key, $value]);
            }
            
            fputcsv($file, []); // Línea en blanco
            
            // Escribir hábitos
            fputcsv($file, ['Hábitos de Lectura']);
            fputcsv($file, ['Hábito', 'Valor']);
            foreach ($data['habits'] as $key => $value) {
                fputcsv($file, [$key, $value]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Exportar a PDF
     */
    private function exportToPdf($data)
    {
        // Implementar exportación a PDF
        return response()->json(['message' => 'PDF export not implemented yet']);
    }
} 