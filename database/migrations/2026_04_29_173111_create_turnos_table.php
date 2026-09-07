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
    Schema::create('turnos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('cliente_id')->constrained('usuarios');
        $table->foreignId('barbero_id')->constrained('usuarios');
        $table->foreignId('servicio_id')->constrained('servicios');
        $table->foreignId('sucursal_id')->constrained('sucursales');
        $table->date('fecha');
        $table->time('hora');
        $table->enum('estado', ['pendiente','confirmado','cancelado'])->default('pendiente');
        $table->decimal('precio_historico', 10, 2); // precio guardado al momento de reservar
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('turnos');
    }
};
