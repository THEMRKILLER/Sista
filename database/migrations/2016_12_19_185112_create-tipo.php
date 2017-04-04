<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('tipo')) {
           Schema::create('tipo', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('calendario_id')->unsigned()->index();
            $table->foreign('calendario_id')->references('id')->on('calendario')->onDelete('cascade');
            $table->string('nombre');
            $table->string('duracion');
            $table->decimal('costo',10,4);
            $table->string('denominacion');
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
        Schema::dropIfExists('tipo');
    }
}
