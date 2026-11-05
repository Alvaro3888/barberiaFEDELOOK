<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPenalizationFieldsToUsuariosTable extends Migration
{
    public function up()
    {
        Schema::table('usuarios', function (Blueprint $table) {
            if (!Schema::hasColumn('usuarios', 'ausencias_online_count')) {
                $table->unsignedInteger('ausencias_online_count')->default(0)->after('activo');
            }
            if (!Schema::hasColumn('usuarios', 'penalizado_hasta')) {
                $table->timestamp('penalizado_hasta')->nullable()->after('ausencias_online_count');
            }
        });
    }

    public function down()
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn(['ausencias_online_count', 'penalizado_hasta']);
        });
    }
}