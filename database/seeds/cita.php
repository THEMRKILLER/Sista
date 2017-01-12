<?php

use Illuminate\Database\Seeder;
use Laracasts\TestDummy\Factory as TestDummy;
class cita extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	TestDummy::times(5)->create('App\tipo');
        TestDummy::times(100)->create('App\cita');

    }
}
