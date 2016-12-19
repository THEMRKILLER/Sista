<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCita extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cita', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('calendario_id')->unsigned()->index();
            $table->foreign('calendario_id')->references('id')->on('calendario')->onDelete('cascade');
             $table->integer('tipo_id')->unsigned()->index();
            $table->foreign('tipo_id')->references('id')->on('calendario')->onDelete('cascade');
            $table->date('fecha');
            $table->integer('hora');
            $table->string('cliente_nombre');
            $table->string('cliente_telefono');
            $table->string('cliente_email');
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
        Schema::dropIfExists('cita');
    }
}
