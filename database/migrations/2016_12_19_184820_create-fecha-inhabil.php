<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFechaInhabil extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('fecha_inhabil')) {
          Schema::create('fecha_inhabil', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('calendario_id')->unsigned()->index();
            $table->foreign('calendario_id')->references('id')->on('calendario')->onDelete('cascade');
            $table->date('fecha');
            $table->tinyInteger('completo');

            $table->timestamps();
        });
}

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fechas_inhabiles');
    }
}
