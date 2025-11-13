<?php
namespace App\Http\Controllers;

use App\Models\Resena;
use Illuminate\Http\Request;

class ResenaController extends Controller
{
    public function pendientes() {
        $resenas = Resena::where('estado', 'pendiente')->with(['libro', 'usuario'])->get();
        return view('admin.resenas.pendientes', compact('resenas'));
    }
    public function aprobar(Resena $resena) {
        $resena->update(['estado' => 'aprobada']);
        return back()->with('success', 'Reseña aprobada.');
    }
    public function rechazar(Resena $resena) {
        $resena->update(['estado' => 'rechazada']);
        return back()->with('success', 'Reseña rechazada.');
    }
} 