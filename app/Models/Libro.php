<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Libro extends Model
{
    protected $table = 'libros';

    protected $fillable = [
        'titulo',
        'isbn',
        'sinopsis',
        'anio_publicacion',
        'editorial',
        'paginas',
        'idioma',
        'estado',
        'autor_id',
        'categoria_id',
        'imagen_portada',
        'archivo_pdf',
        'stock',
        'ubicacion',
        'precio_compra_fisica',
        'precio_compra_online',
        'precio_prestamo_fisico',
        'precio_prestamo_online',
        'preview_limit',
        'contenido',
        'descripcion_vista_previa',
    ];

    protected $casts = [
        'precio_compra_fisica' => 'float',
        'precio_compra_online' => 'float',
        'precio_prestamo_fisico' => 'float',
        'precio_prestamo_online' => 'float',
        'preview_limit' => 'integer',
    ];

    // Relaciones
    public function autor(): BelongsTo
    {
        return $this->belongsTo(Autor::class);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function prestamos(): HasMany
    {
        return $this->hasMany(Prestamo::class);
    }

    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class);
    }

    public function favoritos(): HasMany
    {
        return $this->hasMany(Favorito::class);
    }

    public function resenas()
    {
        return $this->hasMany(Resena::class);
    }

    public function resenas_aprobadas()
    {
        return $this->hasMany(Resena::class)->where('estado', 'aprobada');
    }

    // Métodos de ayuda
    public function isDisponible(): bool
    {
        return $this->estado === 'disponible';
    }

    public function isPrestado(): bool
    {
        return $this->estado === 'prestado';
    }

    public function isReservado(): bool
    {
        return $this->estado === 'reservado';
    }

    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'disponible');
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', '!=', 'mantenimiento');
    }

    public function getPrestamosActivosAttribute()
    {
        return $this->prestamos()->where('estado', 'prestado')->get();
    }

    public function getReservasPendientesAttribute()
    {
        return $this->reservas()->where('estado', 'pendiente')->get();
    }

    public function isFavoritedBy($user)
    {
        if (!$user) return false;
        return $this->favoritos()->where('usuario_id', $user->id)->exists();
    }

    public function getPromedioRatingAttribute()
    {
        return $this->resenas()->where('estado', 'aprobada')->avg('calificacion') ?? 0;
    }
}
