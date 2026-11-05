<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Tabla independiente para las franjas horarias (Cumple con 3NF)
        Schema::create('turnos_trabajo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Ej: Turno Mañana, Turno Tarde, Horario Corrido
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->boolean('incluye_siesta')->default(false); // 0 = Cortado con siesta, 1 = Corrido (como Fede)
            $table->timestamps();
        });

        // 2. Modificamos la tabla usuarios existente para asociarla mediante Clave Foránea
        Schema::table('usuarios', function (Blueprint $table) {
            $table->foreignId('turno_trabajo_id')
                  ->nullable()
                  ->constrained('turnos_trabajo')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['turno_trabajo_id']);
            $table->dropColumn('turno_trabajo_id');
        });

        Schema::dropIfExists('turnos_trabajo');
    }
};