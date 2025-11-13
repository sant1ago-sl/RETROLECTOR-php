<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Libro;
use App\Models\Reserva;
use App\Models\Autor;
use App\Models\Categoria;
use App\Models\Usuario;

class LibroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $autor = Autor::firstOrCreate(['nombre' => 'Ejemplo Autor']);
        $categoria = Categoria::firstOrCreate(['nombre' => 'Ejemplo Categoría']);
        $user = Usuario::firstOrCreate(['email' => 'ejemplo@correo.com'], [
            'nombre' => 'Usuario',
            'apellido' => 'Ejemplo',
            'password' => bcrypt('password'),
            'tipo' => 'cliente',
            'estado' => 'activo'
        ]);

        // Libro disponible
        $libro1 = Libro::create([
            'titulo' => 'Libro Disponible',
            'autor_id' => $autor->id,
            'categoria_id' => $categoria->id,
            'estado' => 'disponible',
            'stock' => 2,
            'contenido' => 'Capítulo 1: Gratis...\n\nCapítulo 2: Solo para compradores...',
            'preview_limit' => 20,
            'precio_compra_fisica' => 35.50,
            'precio_compra_online' => 20.00,
            'precio_prestamo_fisico' => 5.00,
            'precio_prestamo_online' => 2.50
        ]);

        // Libro no disponible
        $libro2 = Libro::create([
            'titulo' => 'Libro No Disponible',
            'autor_id' => $autor->id,
            'categoria_id' => $categoria->id,
            'estado' => 'reservado',
            'stock' => 0,
            'contenido' => 'Inicio gratis...\n\nContenido premium...',
            'preview_limit' => 15,
            'precio_compra_fisica' => 40.00,
            'precio_compra_online' => 25.00,
            'precio_prestamo_fisico' => 6.00,
            'precio_prestamo_online' => 3.00
        ]);

        // Reserva para el libro no disponible
        Reserva::create([
            'usuario_id' => $user->id,
            'libro_id' => $libro2->id,
            'estado' => 'pendiente',
            'fecha_reserva' => now(),
            'fecha_expiracion' => now()->addDays(7)
        ]);
    }
}
