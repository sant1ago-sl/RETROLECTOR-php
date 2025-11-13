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
        Schema::create('libros', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('isbn', 20)->nullable();
            $table->text('sinopsis')->nullable();
            $table->integer('anio_publicacion')->nullable();
            $table->string('editorial')->nullable();
            $table->integer('paginas')->nullable();
            $table->string('idioma', 50)->default('Español');
            $table->enum('estado', ['disponible', 'prestado', 'reservado', 'mantenimiento'])->default('disponible');
            $table->unsignedBigInteger('autor_id');
            $table->unsignedBigInteger('categoria_id');
            $table->string('imagen_portada')->nullable();
            $table->string('archivo_pdf')->nullable();
            $table->longText('contenido')->nullable();
            $table->integer('preview_limit')->default(1000); // Por defecto 1000 caracteres gratuitos
            $table->integer('stock')->default(1);
            $table->string('ubicacion', 100)->nullable();
            $table->unsignedBigInteger('creado_por')->nullable();
            $table->timestamps();

            $table->foreign('autor_id')->references('id')->on('autors')->onDelete('cascade');
            $table->foreign('categoria_id')->references('id')->on('categorias')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('libros');
    }
};
