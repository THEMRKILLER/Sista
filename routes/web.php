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
  Route::get('sysadmin/login', 'SysadminAuth\LoginController@showLoginForm');
  Route::post('sysadmin/login', 'SysadminAuth\LoginController@login');
  Route::group(['prefix' => 'sysadmin','middleware' => 'sysadmin'], function () {
  Route::get('/',function(){
       return redirect()->route('syshome');
  });
   Route::get('/altausuario','UsuarioController@showform');
  Route::post('/altausuario','UsuarioController@altausuario');



  Route::post('/logout', 'SysadminAuth\LoginController@logout');

  Route::get('/register', 'SysadminAuth\RegisterController@showRegistrationForm');
  Route::post('/register', 'SysadminAuth\RegisterController@register');

  Route::post('/password/email', 'SysadminAuth\ForgotPasswordController@sendResetLinkEmail');
  Route::post('/password/reset', 'SysadminAuth\ResetPasswordController@reset');
  Route::get('/password/reset', 'SysadminAuth\ForgotPasswordController@showLinkRequestForm');
  Route::get('/password/reset/{token}', 'SysadminAuth\ResetPasswordController@showResetForm');
  Route::get('/home','UsuarioController@getUsers')->name('syshome');
  Route::post('/completar','UsuarioController@completar_registro');
  Route::post('/borrar','UsuarioController@delete_user');

  Route::get('/2fa/enable', 'Google2FAController@enableTwoFactor');
Route::get('/2fa/disable', 'Google2FAController@disableTwoFactor');
Route::get('/2fa/validate', 'SysadminAuth\LoginController@getValidateToken');
Route::post('/2fa/validate', ['middleware' => 'throttle:5', 'uses' => 'SysadminAuth\LoginController@postValidateToken']);
 
});

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

