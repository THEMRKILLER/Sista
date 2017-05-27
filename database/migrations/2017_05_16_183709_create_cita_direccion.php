<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCitaDireccion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('cita_direccion')) {
            Schema::create('cita_direccion', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cita_id')->unsigned()->index();
            $table->foreign('cita_id')->references('id')->on('cita')->onDelete('cascade');
            $table->string('direccion');
            $table->string('latitud');
            $table->string('longitud');
            $table->text('referencia');
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
                Schema::dropIfExists('cita_direccion');

    }
}
