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
   Route::post('test_login','ValidacionController@authenticate');


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
   Route::get('/dashboard',
   function () {
       $token = JWTAuth::getToken();
       $user = JWTAuth::toUser($token);
       $calendario =$user->calendario;
       $citas =$calendario->citas()->get();//->whereMonth('fecha', '=', '06')->get();
       $events=array();
      foreach ($citas as $cita) {
        
        $title=$cita->tipo($cita['tipo_id']);
        $start=$cita['fecha_inicio'];
        $end=$cita['fecha_final'];
        $arr = array('title' => $title, 'start' => $start, 'end' => $end);
        array_push($events, $arr);
    }
    array_push($events,['title' => 'Evento 1','start' => '2016-12-07','end' => '2016-12-07']);
    array_push($events,['title' => 'Evento 2','start' => '2016-12-08','end' => '2016-12-08']);
    array_push($events,['title' => 'Evento 3','start' => '2016-12-09','end' => '2016-12-09']);
    array_push($events,['title' => 'Evento 4','start' => '2016-12-10','end' => '2016-12-10']);
    array_push($events,['title' => 'Evento 5','start' => '2016-12-11','end' => '2016-12-11']);
    array_push($events,['title' => 'Evento 6','start' => '2016-12-12','end' => '2016-12-12']);
       
       return Response::json($events,200);
   }
);

 });
