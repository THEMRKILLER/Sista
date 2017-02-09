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

Route::group(['middleware' => ['cors'], 'prefix' => 'v1'], function () {
    Route::post('login', 'AuthenticateController@authenticate');
    Route::get('test_login', 'ValidacionController@authenticate');
    Route::get('registro_test', 'Auth\RegisterController@createTest');
    Route::get('servicio-disponible', 'CitaController@horasDisponibles');
    Route::get('disponibilidad', 'CitaController@disponibilidadCalendario');
    Route::get('prueba', 'CitaController@inhabil');
    Route::get('sms', 'CitaController@sms');
    Route::get('email', 'CitaController@mail');
    
    Route::put('cita-r', 'CitaController@reagendar');
    Route::get('verificar_cita','CitaController@comprobar');




    Route::get('images/{image_name}', 'ArticuloController@getImage');
    Route::get('articulo/{id}', 'ArticuloController@getArticulo');

    Route::get('/tipo', 'TipoController@index');


    Route::get('testurl', 'CalendarioController@url');
    Route::get('obtener_horas', 'CitaController@filtrarHoras');
    Route::get('dias_habiles', 'CalendarioController@getDiasHabiles');

    Route::get('fecha_inhabil', 'CalendarioController@getDiasHorasInhabiles');

    Route::get('algoritmo', 'CalendarioController@algoritmo');

    Route::get('user_info', 'UsuarioController@getPerfilInfo');

    Route::get('foto_perfil/{image_name}', 'UsuarioController@getProfilePicture');

    Route::post('cita', 'CitaController@store');
    Route::put('/cita/{id}', 'CitaController@update'); 
    Route::delete('cita', 'CitaController@destroy');
});

Route::group(['middleware' => ['cors','jwt.refresh'],'prefix' => 'v1'], function () {
    Route::get('refresh_token', function () {
        $oldtoken = JWTAuth::getToken();
        $token = JWTAuth::refresh($oldtoken);
        return response()->json(compact('token'));
    });
});

Route::group(['middleware' => ['cors','jwt.auth'], 'prefix' => 'v1'],
 function () {
     Route::get('dashboard', 'CalendarioController@index');
   /*CRUD HORARIO*/
   Route::get('horario', 'CalendarioController@asignar_horario');

   /*FIN CRUD HORARIO */
   Route::get('inhabilitar_fecha', 'CalendarioController@inhabilitar_fecha');

   /*Crud Citas*/

  Route::get('cita', 'CitaController@index');

  
   //tipo-cita (servicios)
   Route::get('/tipo/{id}', 'TipoController@show');
     Route::post('/tipo', 'TipoController@store');
     Route::put('/tipo', 'TipoController@update');
     Route::delete('/tipo', 'TipoController@destroy');


   //Articulos

   Route::post('articulo', 'ArticuloController@store');
   Route::put('articulo', 'ArticuloController@update');
   Route::get('articulos', 'ArticuloController@getArticulos');
   Route::delete('articulo','ArticuloController@delete');


   //dias habiles
   Route::post('dias_habiles', 'CalendarioController@setDiasHabiles');

     Route::post('fecha_inhabil', 'CalendarioController@setDiasHorasInhabiles');

     Route::delete('fecha_inhabil', 'CalendarioController@deleteDiasHorasInhabiles');

     Route::post('logout', 'UsuarioController@logout2');

     Route::put('avatar', 'UsuarioController@updateAvatar');

   //cupones

   Route::post('cupon', 'CuponController@create');
     Route::get('cupon', 'CuponController@index');

  //Codigo cupones
      Route::get('codigo_cupon/{key}','CuponController@generarCodigo'); 
  

 });
