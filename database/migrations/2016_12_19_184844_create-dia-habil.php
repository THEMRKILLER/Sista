<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiaHabil extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('dia_habil')) {
            Schema::create('dia_habil', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('calendario_id')->unsigned()->index();
            $table->foreign('calendario_id')->references('id')->on('calendario')->onDelete('cascade');
            $table->integer('dia');
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
        Schema::dropIfExists('dias_habiles');
    }
}
