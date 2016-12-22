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


Route::group(['middleware' => 'cors', 'prefix' => 'v1'], function () {
   Route::get('/test',function(){
    return "OK";
   });
   Route::post('login','AuthenticateController@authenticate');

   Route::get('/restricted', [
   'before' => 'jwt-auth',
   function () {
       $token = JWTAuth::getToken();
       $user = JWTAuth::toUser($token);

       return Response::json([
           'data' => [
               'email' => $user->email,
               'registered_at' => $user->created_at->toDateTimeString()
           ]
       ]);
   }
]);

  
});
