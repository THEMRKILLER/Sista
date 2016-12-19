<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHorasHabiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hora_habil', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('diahabil_id')->unsigned()->index();
            $table->foreign('diahabil_id')->references('id')->on('dia_habil')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('horas_habiles');
    }
}
