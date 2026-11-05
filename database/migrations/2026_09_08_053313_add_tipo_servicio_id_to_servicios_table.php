<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('servicios', function (Blueprint $table) {
        // ⚠️ Primero eliminamos el campo viejo si existe
        if (Schema::hasColumn('servicios', 'categoria')) {
            $table->dropColumn('categoria');
        }

        // Agregamos la FK hacia tipos_servicio
        $table->foreignId('tipo_servicio_id')
              ->constrained('tipos_servicio')
              ->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('servicios', function (Blueprint $table) {
        // Restauramos el campo viejo
        $table->string('categoria')->nullable();

        // Quitamos la FK
        $table->dropForeign(['tipo_servicio_id']);
        $table->dropColumn('tipo_servicio_id');
    });
}

};
