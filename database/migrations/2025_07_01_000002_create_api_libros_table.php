<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('api_libros', function (Blueprint $table) {
            $table->id();
            $table->string('api_origen');
            $table->string('api_id');
            $table->json('datos_json');
            $table->foreignId('libro_id')->nullable()->constrained('libros')->onDelete('set null');
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('api_libros');
    }
}; 