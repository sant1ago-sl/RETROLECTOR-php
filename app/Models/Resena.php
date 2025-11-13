<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resena extends Model
{
    protected $table = 'resenas';
    protected $fillable = [
        'libro_id', 'usuario_id', 'calificacion', 'comentario', 'estado'
    ];

    public function libro() {
        return $this->belongsTo(Libro::class);
    }
    public function usuario() {
        return $this->belongsTo(Usuario::class);
    }
} 