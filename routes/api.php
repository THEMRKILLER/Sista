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
   Route::get('registro_test','Auth\RegisterController@createTest');
   Route::get('servicio-disponible','CitaController@horasDisponibles');

   Route::get('images/{image_name}','ArticuloController@getImage');
   Route::get('articulo/{id}','ArticuloController@getArticulo');


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

   Route::get('cita','CitaController@index');
   Route::get('/cita/{id}','CitaController@show');
   Route::post('cita','CitaController@store');
   Route::put('/cita/{id}','CitaController@update');
   Route::put('/cita-r/{id}','CitaController@reagendar');
   Route::delete('/cita/{id}','CitaController@destroy');
   //tipo-cita
   Route::get('/tipo','TipoController@index');
   Route::get('/tipo/{id}','TipoController@show');
   Route::post('/tipo','TipoController@store');
   Route::put('/tipo/{id}','TipoController@update');
   Route::delete('/tipo/{id}','TipoController@destroy');


   //Articulos 

   Route::post('articulo','ArticuloController@store');
   Route::get('articulos','ArticuloController@getArticulos');

 });
