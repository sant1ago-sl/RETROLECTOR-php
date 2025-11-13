<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLibro extends Model
{
    protected $table = 'api_libros';
    protected $fillable = [
        'api_origen', 'api_id', 'datos_json', 'libro_id'
    ];

    public function libro() {
        return $this->belongsTo(Libro::class);
    }
} 