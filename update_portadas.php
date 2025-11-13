<?php

use App\Models\Libro;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Actualizar portadas
Libro::where('titulo', 'El Aleph')->update(['imagen_portada' => 'https://covers.openlibrary.org/b/id/8228691-L.jpg']);
Libro::where('titulo', 'La casa de los espíritus')->update(['imagen_portada' => 'https://covers.openlibrary.org/b/id/10523338-L.jpg']);
Libro::where('titulo', 'La ciudad y los perros')->update(['imagen_portada' => 'https://covers.openlibrary.org/b/id/11156341-L.jpg']);
Libro::where('titulo', 'Rayuela')->update(['imagen_portada' => 'https://covers.openlibrary.org/b/id/11156340-L.jpg']);
Libro::where('titulo', 'Veinte poemas de amor')->update(['imagen_portada' => 'https://covers.openlibrary.org/b/id/10523336-L.jpg']);

echo "Portadas actualizadas correctamente.\n"; 