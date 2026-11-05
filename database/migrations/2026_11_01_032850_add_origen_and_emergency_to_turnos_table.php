<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrigenAndEmergencyToTurnosTable extends Migration
{
    public function up()
    {
        Schema::table('turnos', function (Blueprint $table) {
            if (!Schema::hasColumn('turnos', 'origen')) {
                $table->string('origen', 20)->default('web')->after('estado'); // 'web' o 'mostrador'
            }
            if (!Schema::hasColumn('turnos', 'motivo_cancelacion_emergencia')) {
                $table->text('motivo_cancelacion_emergencia')->nullable()->after('origen');
            }
        });
    }

    public function down()
    {
        Schema::table('turnos', function (Blueprint $table) {
            $table->dropColumn(['origen', 'motivo_cancelacion_emergencia']);
        });
    }
}
