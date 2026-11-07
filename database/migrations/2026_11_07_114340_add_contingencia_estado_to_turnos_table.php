
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('turnos', function (Blueprint $table) {
            // Si tu columna estado es un ENUM estricto, lo ampliamos para incluir 'sin_asignar_contingencia'
            // O si prefieres asegurarte, lo pasamos temporalmente a string o modificamos el enum:
            $table->string('estado', 50)->default('pendiente')->change();
        });
    }

    public function down(): void
    {
        Schema::table('turnos', function (Blueprint $table) {
            $table->enum('estado', ['pendiente', 'confirmado', 'completado', 'cancelado'])->default('pendiente')->change();
        });
    }
};