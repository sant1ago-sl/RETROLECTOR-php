<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('libros', function (Blueprint $table) {
            if (!Schema::hasColumn('libros', 'descripcion')) {
                $table->text('descripcion')->nullable()->after('editorial');
            }
            if (!Schema::hasColumn('libros', 'contenido')) {
                $table->longText('contenido')->nullable()->after('descripcion');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('libros', function (Blueprint $table) {
            if (Schema::hasColumn('libros', 'descripcion')) {
                $table->dropColumn('descripcion');
            }
            if (Schema::hasColumn('libros', 'contenido')) {
                $table->dropColumn('contenido');
            }
        });
    }
};
