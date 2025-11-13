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
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('libro_id');
            $table->enum('tipo', ['fisico', 'virtual']);
            $table->decimal('precio', 8, 2);
            $table->enum('estado', ['pendiente', 'completada', 'cancelada'])->default('pendiente');
            $table->json('datos_envio')->nullable(); // Información de envío y pago
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
            $table->foreign('libro_id')->references('id')->on('libros')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
}; 