<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserInformacionExtra extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('informacion_extra', function (Blueprint $table) {
                $table->string('domicilio')->nullable();
                $table->string('telefono')->nullable();
                $table->decimal('cuota',10,4)->default(0);
                $table->float('radio', 8, 2)->nullable();

            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
