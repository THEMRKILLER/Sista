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
     Route::post('getrawToken', 'AuthenticateController@testtoken');
    Route::get('test_login', 'ValidacionController@authenticate');
    Route::get('registro_test', 'Auth\RegisterController@createTest');
    Route::get('servicio-disponible', 'CitaController@horasDisponibles');
    Route::get('disponibilidad', 'CitaController@disponibilidadCalendario');
    Route::get('prueba', 'CitaController@inhabil');
    Route::get('sms', 'CitaController@sms');
    Route::get('email', 'CitaController@mail');
    
    Route::put('cita-r', 'CitaController@reagendar');
    Route::get('verificar_cita','CitaController@comprobar');

    Route::get('articulos_list','ArticuloController@articulos_list');

    Route::get('pdf', 'PdfController@CitasPorMes');


    Route::get('citas','CitaController@index');

    Route::get('images/{image_name}', 'ArticuloController@getImage');
    Route::get('articulo/{id}', 'ArticuloController@getArticulo');
    Route::get('resolve_articulo/{id}','ArticuloController@resolveArticulo');
    Route::get('/tipo', 'TipoController@index');


    Route::get('testurl', 'CalendarioController@url');
    Route::get('obtener_horas', 'CitaController@filtrarHoras');
    Route::get('dias_habiles', 'CalendarioController@getDiasHabiles');

    Route::get('fecha_inhabil', 'CalendarioController@getDiasHorasInhabiles');

    Route::get('algoritmo', 'CalendarioController@algoritmo');

    Route::get('user_info', 'UsuarioController@getPerfilInfo');

    Route::get('user_cv','UsuarioController@getCv');
    
    Route::get('foto_perfil/{image_name}', 'UsuarioController@getProfilePicture');

    Route::post('cita', 'CitaController@store');
    Route::get('verificar_cita', 'CitaController@verificarhora');
    
    Route::delete('cita', 'CitaController@destroy');


    /*cupones*/

    Route::get('verificarcupon','CuponController@verificar');

    /*Restablecimiento de contraseña*/

    //verificar email
    Route::get('verificar_email','UsuarioController@validar_email');
    Route::post('enviar_email_password','UsuarioController@enviar_email_forgotten');
    //verifica token 
    Route::get('validar_password_codigo','UsuarioController@validar_password_codigo');   
   //cambiar password olvidado

    Route::get('servicios_domicilio','ServicioDomicilioController@index');

   Route::post('cambiar_password_forgotten','UsuarioController@cambiar_password_forgotten');

       Route::get('user','UsuarioController@index');


});

Route::group(['middleware' => ['cors','refrescartoken'],'prefix' => 'v1'], function () {

    Route::put('refresh_token', function () {
    });

});




Route::group(['middleware' => ['cors','jwt.auth'], 'prefix' => 'v1'],
 function () {

    /* USUARIO */
    Route::put('user','UsuarioController@update');
    Route::put('password','UsuarioController@settingsUpdatePassword');

    
     Route::get('dashboard', 'CalendarioController@index');
   /*CRUD HORARIO*/
   Route::get('horario', 'CalendarioController@asignar_horario');

   /*FIN CRUD HORARIO */
   Route::get('inhabilitar_fecha', 'CalendarioController@inhabilitar_fecha');

   /*Crud Citas*/

  
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
    Route::delete('cupon','CuponController@destroy');

  //Codigo cupones
      Route::get('codigo_cupon/{key}','CuponController@generarCodigo'); 

    Route::get('checktoken',function(){});
  
Route::get('checktoken',function(){});

Route::post('invalidar_t','AuthenticateController@invalidar_token');

 });
