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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('tipo', ['admin', 'cliente'])->default('cliente');
            $table->string('telefono', 20)->nullable();
            $table->text('direccion')->nullable();
            $table->enum('idioma_preferencia', ['es', 'en'])->default('es');
            $table->enum('tema_preferencia', ['claro', 'oscuro'])->default('claro');
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
