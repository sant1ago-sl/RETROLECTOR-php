<?php
namespace App\Http\Controllers;

use App\Models\Libro;
use App\Models\Prestamo;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    public function librosPdf() {
        $libros = Libro::all();
        $pdf = Pdf::loadView('reports.libros', compact('libros'));
        return $pdf->download('libros.pdf');
    }
    public function librosExcel() {
        $libros = Libro::all();
        return Excel::download(new \App\Exports\LibrosExport($libros), 'libros.xlsx');
    }
    // Métodos similares para préstamos y usuarios...
} 