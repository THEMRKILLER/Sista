<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCupones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    
     Schema::create('cupon', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('servicio_id')->unsigned()->index();
            $table->foreign('servicio_id')->references('id')->on('tipo')->onDelete('cascade');
            $table->string('codigo')->unique();
            $table->integer('porcentaje');
            $table->date('fecha_inicial');
            $table->date('fecha_final');
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
          Schema::drop('cupon');
    }
}
