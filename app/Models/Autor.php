<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Autor extends Model
{
    protected $table = 'autors';

    protected $fillable = [
        'nombre',
        'apellido',
        'biografia',
        'nacionalidad',
        'fecha_nacimiento',
        'foto',
        'estado',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    // Relaciones
    public function libros(): HasMany
    {
        return $this->hasMany(Libro::class);
    }

    // Métodos de ayuda
    public function getNombreCompletoAttribute(): string
    {
        return $this->nombre . ' ' . $this->apellido;
    }

    public function getLibrosActivosAttribute()
    {
        return $this->libros()->where('estado', '!=', 'mantenimiento')->get();
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }
}
