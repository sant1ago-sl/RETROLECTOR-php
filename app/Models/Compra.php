<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Compra extends Model
{
    use HasFactory;

    protected $table = 'compras';

    protected $fillable = [
        'usuario_id',
        'libro_id',
        'tipo', // 'fisico' o 'virtual'
        'precio',
        'modalidad', // 'fisico' o 'online'
        'estado', // 'pendiente', 'completada', 'cancelada'
        'datos_envio', // JSON con información de envío/pago
    ];

    protected $casts = [
        'datos_envio' => 'array',
        'precio' => 'decimal:2',
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(\App\Models\Usuario::class, 'usuario_id');
    }

    public function libro(): BelongsTo
    {
        return $this->belongsTo(Libro::class);
    }

    // Métodos de ayuda
    public function getMetodoPagoAttribute()
    {
        return $this->datos_envio['metodo_pago'] ?? 'N/A';
    }

    public function getDireccionEnvioAttribute()
    {
        if ($this->tipo === 'fisico') {
            $datos = $this->datos_envio;
            $direccion = $datos['direccion'] . ', ' . $datos['distrito'] . ', ' . $datos['departamento'];
            if (isset($datos['codigo_postal']) && $datos['codigo_postal']) {
                $direccion .= ' ' . $datos['codigo_postal'];
            }
            return $direccion;
        }
        return 'N/A';
    }

    public function getNombreCompletoAttribute()
    {
        if ($this->tipo === 'fisico') {
            return $this->datos_envio['nombre'] ?? 'N/A';
        }
        return $this->usuario->nombre_completo;
    }

    public function getEmailAttribute()
    {
        if ($this->tipo === 'fisico') {
            return $this->datos_envio['email'] ?? 'N/A';
        }
        return $this->usuario->email;
    }

    public function getTelefonoAttribute()
    {
        if ($this->tipo === 'fisico') {
            return $this->datos_envio['telefono'] ?? 'N/A';
        }
        return $this->usuario->telefono;
    }

    public function isFisica(): bool
    {
        return $this->tipo === 'fisico';
    }

    public function isVirtual(): bool
    {
        return $this->tipo === 'virtual';
    }

    public function isCompletada(): bool
    {
        return $this->estado === 'completada';
    }


} 