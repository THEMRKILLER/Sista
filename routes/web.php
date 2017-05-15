<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::group(['prefix' => 'admin'], function () {
  Route::get('/login', 'AdminAuth\LoginController@showLoginForm');
  Route::post('/login', 'AdminAuth\LoginController@login');
  Route::post('/logout', 'AdminAuth\LoginController@logout');

  Route::get('/register', 'AdminAuth\RegisterController@showRegistrationForm');
  Route::post('/register', 'AdminAuth\RegisterController@register');
  Route::post('/password/email', 'AdminAuth\ForgotPasswordController@sendResetLinkEmail');
  Route::post('/password/reset', 'AdminAuth\ResetPasswordController@reset');
  Route::get('/password/reset', 'AdminAuth\ForgotPasswordController@showLinkRequestForm');
  Route::get('/password/reset/{token}', 'AdminAuth\ResetPasswordController@showResetForm');
});



//Route::post('loginuser', 'Auth\AuthController@postLogin');
Route::any('testcreate','Auth\RegisterController@createTest');
Route::any('authtest','ValidacionController@authenticate');

  Route::group(['prefix' => 'sysadmin'], function () {
  Route::get('/',function(){
       return redirect()->route('syshome');
  });
   Route::get('/altausuario','UsuarioController@showform')->middleware('auth:sysadmin');;
  Route::post('/altausuario','UsuarioController@altausuario')->middleware('auth:sysadmin');;


  Route::get('/login', 'SysadminAuth\LoginController@showLoginForm');
  Route::post('/login', 'SysadminAuth\LoginController@login');
  Route::post('/logout', 'SysadminAuth\LoginController@logout');

  Route::get('/register', 'SysadminAuth\RegisterController@showRegistrationForm');
  Route::post('/register', 'SysadminAuth\RegisterController@register');

  Route::post('/password/email', 'SysadminAuth\ForgotPasswordController@sendResetLinkEmail');
  Route::post('/password/reset', 'SysadminAuth\ResetPasswordController@reset');
  Route::get('/password/reset', 'SysadminAuth\ForgotPasswordController@showLinkRequestForm');
  Route::get('/password/reset/{token}', 'SysadminAuth\ResetPasswordController@showResetForm');
  Route::get('/home','UsuarioController@getUsers')->name('syshome')->middleware('auth:sysadmin');
  Route::post('/completar','UsuarioController@completar_registro');
  Route::post('/borrar','UsuarioController@delete_user');

  
 
});
