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
    Schema::create('tipos_servicio', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');              // Ej: Corte, Barba, Coloración
        $table->text('descripcion')->nullable(); // Opcional
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('tipos_servicio');
}

};
