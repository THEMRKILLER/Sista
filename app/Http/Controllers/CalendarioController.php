<?php

namespace App\Http\Controllers;

use App\calendario;
use Illuminate\Http\Request;
use JWTAuth;
use json;
class CalendarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
       $token = JWTAuth::getToken();
       $user = JWTAuth::toUser($token);
       $calendario =$user->calendario;
       $citas =$calendario->citas()->get();//->whereMonth('fecha', '=', '06')->get();
       $events=array();
      foreach ($citas as $cita) {
        
        $title=$cita->tipo($cita['tipo_id']);
        $start=$cita['fecha_inicio'];
        $end=$cita['fecha_final'];
        $id=$cita->id;
        $arr = array('id' => $id,'title' => $title, 'start' => $start, 'end' => $end);
        array_push($events, $arr);
    }
    array_push($events,['id' => 501,'title' => 'Evento 1','start' => '2016-12-07','end' => '2016-12-07']);
    array_push($events,['id' => 502,'title' => 'Evento 2','start' => '2016-12-08','end' => '2016-12-08']);
    array_push($events,['id' => 503,'title' => 'Evento 3','start' => '2016-12-09','end' => '2016-12-09']);
    array_push($events,['id' => 504,'title' => 'Evento 4','start' => '2016-12-10','end' => '2016-12-10']);
    array_push($events,['id' => 505,'title' => 'Evento 5','start' => '2016-12-11','end' => '2016-12-11']);
    array_push($events,['id' => 506,'title' => 'Evento 6','start' => '2016-12-12','end' => '2016-12-12']);
       
       return \Response::json($events,200);
   
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\calendario  $calendario
     * @return \Illuminate\Http\Response
     */
    public function show(calendario $calendario)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\calendario  $calendario
     * @return \Illuminate\Http\Response
     */
    public function edit(calendario $calendario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\calendario  $calendario
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, calendario $calendario)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\calendario  $calendario
     * @return \Illuminate\Http\Response
     */
    public function destroy(calendario $calendario)
    {
        //
    }
}
