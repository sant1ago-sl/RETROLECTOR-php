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
        if (!Schema::hasColumn('compras', 'modalidad')) {
            Schema::table('compras', function (Blueprint $table) {
                $table->string('modalidad', 20)->nullable()->after('precio');
            });
        }
        if (!Schema::hasColumn('prestamos', 'modalidad')) {
            Schema::table('prestamos', function (Blueprint $table) {
                $table->string('modalidad', 20)->nullable()->after('precio');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('compras', 'modalidad')) {
            Schema::table('compras', function (Blueprint $table) {
                $table->dropColumn('modalidad');
            });
        }
        if (Schema::hasColumn('prestamos', 'modalidad')) {
            Schema::table('prestamos', function (Blueprint $table) {
                $table->dropColumn('modalidad');
            });
        }
    }
}; 