<?php
use Carbon\Carbon;


            


$factory('App\tipo',[
	'calendario_id'=> 1,
	'nombre'=> $faker->company,
	'duracion'=>  $faker->randomNumber($nbDigits = 2),
	'costo'=>  $faker->randomNumber($nbDigits = 4),
	'denominacion'=> 'MXN',


]);

$factory('App\cita', function($faker) {
    $startDate = Carbon::createFromTimeStamp($faker->dateTimeBetween('now', '2 years')->getTimestamp());
    $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $startDate)->addHour();

    return [
       	'calendario_id'=> 1,
		'tipo_id' => 'factory:App\tipo',
		'fecha_inicio'=>  $startDate,
		'fecha_final'=>  $endDate,
		'cliente_nombre'=> $faker->name,
		'cliente_telefono'=> $faker->randomNumber($nbDigits = 8),
		'cliente_email'=> $faker->email,
    ];
});

