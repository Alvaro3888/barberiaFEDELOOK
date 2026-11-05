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
        // Eliminar el campo imagen_url de la tabla sucursales si existe
        if (Schema::hasColumn('sucursales', 'imagen_url')) {
            Schema::table('sucursales', function (Blueprint $table) {
                $table->dropColumn('imagen_url');
            });
        }

        // Eliminar el campo imagen_url de la tabla servicios si existe
        if (Schema::hasColumn('servicios', 'imagen_url')) {
            Schema::table('servicios', function (Blueprint $table) {
                $table->dropColumn('imagen_url');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // En caso de querer revertir, volvemos a agregar la columna como string nullable
        if (!Schema::hasColumn('sucursales', 'imagen_url')) {
            Schema::table('sucursales', function (Blueprint $table) {
                $table->string('imagen_url')->nullable();
            });
        }

        if (!Schema::hasColumn('servicios', 'imagen_url')) {
            Schema::table('servicios', function (Blueprint $table) {
                $table->string('imagen_url')->nullable();
            });
        }
    }
};