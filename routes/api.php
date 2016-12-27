<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['cors'], 'prefix' => 'v1'],function(){

   Route::post('login','AuthenticateController@authenticate');
   Route::get('test_login','ValidacionController@authenticate');

});

Route::group(['middleware' => ['cors','jwt.refresh'],'prefix' => 'v1'],function(){

    Route::get('refresh_token',function(){
      $oldtoken = JWTAuth::getToken();
      $token = JWTAuth::refresh($oldtoken);
      return response()->json(compact('token'));

    });

});

Route::group(['middleware' => ['cors','jwt.auth'], 'prefix' => 'v1'],
 function () {
   Route::get('dashboard','CalendarioController@index');
   /*CRUD HORARIO*/
   Route::get('horario','CalendarioController@asignar_horario');

   /*FIN CRUD HORARIO */
   Route::get('inhabilitar_fecha','CalendarioController@inhabilitar_fecha');

   /*Crud Citas*/

   Route::post('cita','CitaController@store');


 });
