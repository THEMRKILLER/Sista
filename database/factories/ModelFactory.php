<?php
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});
/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\tipo::class, function (Faker\Generator $faker) {

	return[
		'calendario_id'=> 1,
		'nombre'=> $faker->company,
		'duracion'=>  $faker->randomNumber($nbDigits = 2),
		'costo'=>  $faker->randomNumber($nbDigits = 4),
		'denominacion'=> 'MXN',
	];
});
/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define('App\cita', function(Faker\Generator $faker) {
    $startDate = Carbon::createFromTimeStamp($faker->dateTimeBetween('now', '2 years')->getTimestamp());
    $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $startDate)->addHour();

    return [
       	'calendario_id'=> 1,
		'tipo_id' => 1,
		'fecha_inicio'=>  $startDate,
		'fecha_final'=>  $endDate,
		'cliente_nombre'=> $faker->name,
		'cliente_telefono'=> $faker->randomNumber($nbDigits = 8),
		'cliente_email'=> $faker->email,
    ];
});
