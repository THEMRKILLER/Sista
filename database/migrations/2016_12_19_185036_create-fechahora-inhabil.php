<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFechahoraInhabil extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fechahora_inhabil', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fechainhabil_id')->unsigned()->index();
            $table->foreign('fechainhabil_id')->references('id')->on('fecha_inhabil')->onDelete('cascade');
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
        Schema::dropIfExists('fechahora_inhabil');
    }
}
