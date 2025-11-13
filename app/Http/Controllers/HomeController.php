<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use App\Models\User;
use App\Models\Prestamo;
use App\Models\Resena;
use App\Models\Favorito;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        // Obtener estadísticas reales de la base de datos
        $stats = $this->getRealTimeStats();
        
        return view('welcome', compact('stats'));
    }
    
    private function getRealTimeStats()
    {
        try {
            // Estadísticas básicas
            $totalBooks = Libro::count();
            $activeUsers = User::where('created_at', '>=', Carbon::now()->subDays(30))->count();
            $totalLoans = Prestamo::count();
            $totalReviews = Resena::count();
            
            // Calcular porcentaje de satisfacción basado en reseñas
            $satisfactionRate = 0;
            if ($totalReviews > 0) {
                $avgRating = Resena::avg('puntuacion');
                $satisfactionRate = round(($avgRating / 5) * 100);
            }
            
            // Crecimiento del mes actual vs mes anterior
            $currentMonthBooks = Libro::whereMonth('created_at', Carbon::now()->month)->count();
            $lastMonthBooks = Libro::whereMonth('created_at', Carbon::now()->subMonth()->month)->count();
            $booksGrowth = $lastMonthBooks > 0 ? round((($currentMonthBooks - $lastMonthBooks) / $lastMonthBooks) * 100) : 0;
            
            $currentMonthUsers = User::whereMonth('created_at', Carbon::now()->month)->count();
            $lastMonthUsers = User::whereMonth('created_at', Carbon::now()->subMonth()->month)->count();
            $usersGrowth = $lastMonthUsers > 0 ? round((($currentMonthUsers - $lastMonthUsers) / $lastMonthUsers) * 100) : 0;
            
            $currentMonthLoans = Prestamo::whereMonth('created_at', Carbon::now()->month)->count();
            $lastMonthLoans = Prestamo::whereMonth('created_at', Carbon::now()->subMonth()->month)->count();
            $loansGrowth = $lastMonthLoans > 0 ? round((($currentMonthLoans - $lastMonthLoans) / $lastMonthLoans) * 100) : 0;
            
            // Estadísticas adicionales
            $avgReadingTime = 2.5; // Horas promedio (esto podría calcularse de logs de lectura)
            $readingClubs = 5; // Número de clubes de lectura (implementar cuando se agregue la funcionalidad)
            
            return [
                'total_books' => $totalBooks,
                'active_users' => $activeUsers,
                'total_loans' => $totalLoans,
                'satisfaction_rate' => $satisfactionRate,
                'total_reviews' => $totalReviews,
                'books_growth' => $booksGrowth,
                'users_growth' => $usersGrowth,
                'loans_growth' => $loansGrowth,
                'avg_reading_time' => $avgReadingTime,
                'reading_clubs' => $readingClubs,
            ];
            
        } catch (\Exception $e) {
            // En caso de error, devolver estadísticas por defecto
            return [
                'total_books' => 0,
                'active_users' => 0,
                'total_loans' => 0,
                'satisfaction_rate' => 0,
                'total_reviews' => 0,
                'books_growth' => 0,
                'users_growth' => 0,
                'loans_growth' => 0,
                'avg_reading_time' => 0,
                'reading_clubs' => 0,
            ];
        }
    }

    public function getStatsAjax()
    {
        $stats = $this->getRealTimeStats();
        return response()->json($stats);
    }
} 