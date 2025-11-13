<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('libros', function (Blueprint $table) {
            if (!Schema::hasColumn('libros', 'precio_compra_fisica')) {
                $after = Schema::hasColumn('libros', 'precio') ? 'precio' : 'editorial';
                $table->decimal('precio_compra_fisica', 8, 2)->nullable()->after($after);
            }
            if (!Schema::hasColumn('libros', 'precio_compra_online')) {
                $table->decimal('precio_compra_online', 8, 2)->nullable()->after('precio_compra_fisica');
            }
            if (!Schema::hasColumn('libros', 'precio_prestamo_fisico')) {
                $table->decimal('precio_prestamo_fisico', 8, 2)->nullable()->after('precio_compra_online');
            }
            if (!Schema::hasColumn('libros', 'precio_prestamo_online')) {
                $table->decimal('precio_prestamo_online', 8, 2)->nullable()->after('precio_prestamo_fisico');
            }
        });
    }

    public function down()
    {
        // No hacer nada para evitar errores de drop si no existen
    }
};
