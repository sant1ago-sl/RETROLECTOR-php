<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('resenas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('libro_id')->constrained('libros')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->tinyInteger('calificacion');
            $table->text('comentario');
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('resenas');
    }
}; 