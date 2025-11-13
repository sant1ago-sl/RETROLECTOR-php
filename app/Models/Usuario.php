<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'password',
        'tipo',
        'telefono',
        'direccion',
        'idioma_preferencia',
        'tema_preferencia',
        'estado',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    // Relaciones
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

    public function notificaciones(): HasMany
    {
        return $this->hasMany(Notificacion::class);
    }

    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class);
    }

    // Relaciones para mensajes
    public function mensajesEnviados(): HasMany
    {
        return $this->hasMany(Mensaje::class, 'remitente_id');
    }

    public function mensajesRecibidos(): HasMany
    {
        return $this->hasMany(Mensaje::class, 'destinatario_id');
    }

    // Relaciones para reseñas
    public function resenas(): HasMany
    {
        return $this->hasMany(Resena::class);
    }

    // Métodos de ayuda
    public function isAdmin(): bool
    {
        return $this->tipo === 'admin';
    }

    public function isCliente(): bool
    {
        return $this->tipo === 'cliente';
    }

    public function getNombreCompletoAttribute(): string
    {
        return $this->nombre . ' ' . $this->apellido;
    }

    public function getPrestamosActivosAttribute()
    {
        return $this->prestamos()->where('estado', 'prestado')->get();
    }

    public function getReservasPendientesAttribute()
    {
        return $this->reservas()->where('estado', 'pendiente')->get();
    }

    public function getPrestamosVencidosAttribute()
    {
        return $this->prestamos()->where('estado', 'vencido')->get();
    }

    public function getNotificacionesNoLeidasAttribute()
    {
        return $this->notificaciones()->where('leida', false)->get();
    }

    public function getMensajesNoLeidosAttribute()
    {
        return $this->mensajesRecibidos()->where('leido', false)->get();
    }

    // Métodos de negocio
    public function puedePrestar(): bool
    {
        return $this->getPrestamosVencidosAttribute()->count() === 0;
    }

    public function tieneReservaActiva(Libro $libro): bool
    {
        return $this->reservas()
            ->where('libro_id', $libro->id)
            ->where('estado', 'pendiente')
            ->exists();
    }

    public function tieneAccesoVirtual(Libro $libro): bool
    {
        // Verificar si tiene préstamo activo
        $tienePrestamo = $this->prestamos()
            ->where('libro_id', $libro->id)
            ->where('estado', 'prestado')
            ->exists();

        // Verificar si tiene compra completada
        $tieneCompra = $this->compras()
            ->where('libro_id', $libro->id)
            ->where('estado', 'completada')
            ->exists();

        return $tienePrestamo || $tieneCompra;
    }

    // Métodos de estadísticas
    public function getEstadisticasAttribute(): array
    {
        return [
            'total_prestamos' => $this->prestamos()->count(),
            'prestamos_activos' => $this->prestamos()->where('estado', 'prestado')->count(),
            'prestamos_vencidos' => $this->prestamos()->where('estado', 'vencido')->count(),
            'total_reservas' => $this->reservas()->count(),
            'reservas_pendientes' => $this->reservas()->where('estado', 'pendiente')->count(),
            'total_favoritos' => $this->favoritos()->count(),
            'total_compras' => $this->compras()->count(),
            'compras_completadas' => $this->compras()->where('estado', 'completada')->count(),
            'notificaciones_no_leidas' => $this->notificaciones()->where('leida', false)->count(),
            'mensajes_no_leidos' => $this->mensajesRecibidos()->where('leido', false)->count(),
        ];
    }
}
