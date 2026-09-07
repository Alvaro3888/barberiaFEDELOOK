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
        $table->integer('duracion')->after('nombre');
    });
}

public function down()
{
    Schema::table('servicios', function (Blueprint $table) {
        $table->dropColumn('duracion');
    });
}


   
};
