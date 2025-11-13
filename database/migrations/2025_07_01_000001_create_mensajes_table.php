<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('mensajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('remitente_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('destinatario_id')->constrained('usuarios')->onDelete('cascade');
            $table->string('asunto');
            $table->text('cuerpo');
            $table->boolean('leido')->default(false);
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('mensajes');
    }
}; 