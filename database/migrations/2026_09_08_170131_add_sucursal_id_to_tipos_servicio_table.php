<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tipos_servicio', function (Blueprint $table) {
            // 🔹 Agregamos la columna y la FK
            $table->foreignId('sucursal_id')
                  ->after('id')
                  ->constrained('sucursales')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('tipos_servicio', function (Blueprint $table) {
            $table->dropForeign(['sucursal_id']);
            $table->dropColumn('sucursal_id');
        });
    }
};


