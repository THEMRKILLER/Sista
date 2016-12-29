<?php

namespace App\Http\Controllers;

use App\calendario;
use Illuminate\Http\Request;
use JWTAuth;
use json;
use Validator;
use Auth;
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
       $servicios = $calendario->tipos;
       $citas =$calendario->citas()->get();//->whereMonth('fecha', '=', '06')->get();
       
       $events=array();

      foreach ($citas as $cita) {
        
        $title=$cita->tipo->nombre;
        $start=$cita['fecha_inicio'];
        $end=$cita['fecha_final'];
        $id=$cita->id;
        $cliente_nombre = $cita->cliente_nombre;
        $cliente_telefono = $cita->cliente_telefono;
        $cliente_email = $cita->cliente_email;
        $cita_tipo      = $cita->tipo->nombre;
        array_push($events, ['id' => $id,'title' => $title, 'start' => $start, 'end' => $end,'cliente_nombre' => $cliente_nombre , 'cliente_telefono' => $cliente_telefono,'cliente_email' => $cliente_email , 'servicio' => $cita_tipo ]);
    }
 
       return \Response::json(['citas' => $events, 'servicios' => $servicios],200);
   
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

    public function asignar_horario(Request $request)
    {
        /*
            "dias_habiles"{
                {'dia' : 'Lunes',
                 'horas' : '1,2,3,4,5,6,7,8,9'
                },
                {'dia' : 'Martes',
                 'horas' : '4,5,6,7,8,9
                }
            }
        */


         $dias_habiles = [
                            ['dia' => 1, 'horas'=>[1,2,3,4,5,6,7,8,9,10]],
                            ['dia' => 2, 'horas'=>[1,2,3,4,5,6,7,8,9,10]],
                            ['dia' => 3, 'horas'=>[1,2,3,4,5,6,7,8,9,10]],
                            ['dia' => 4, 'horas'=>[1,2,3,4,5,6,7,8,9,10]],
                            ['dia' => 5, 'horas'=>[1,2,3,4,5,6,7,8,9,10]],
                            ['dia' => 6, 'horas'=>[1,2,3,4,5,6,7,8,9,10]]

                        ];
      // $dias_habiles = $request->get('dias_habiles');
       $token = JWTAuth::getToken();
       $user = JWTAuth::toUser($token);
       $user->calendario->asignar_horario($dias_habiles);



       

    }

    public function asignar_horario_validate($horario)
    {


    }

    public function inhabilitar_fecha(Request $request)
    {
        //$fecha = $request->get('fecha');

                $fechas = [
                        ['fecha' => '2016-12-28' , 'completo' => false, 'horas' =>[1,2,3,4,5] ],
                        ['fecha' => '2016-12-29' , 'completo' => false, 'horas' =>[1,2,3,4,5] ],
                        ['fecha' => '2016-12-30' , 'completo' => false, 'horas' =>[1,2,3,4,5] ],
                        ['fecha' => '2016-12-31' , 'completo' => false, 'horas' =>[1,2,3,4,5] ],
                        ['fecha' => '2017-01-01' , 'completo' => true, 'horas' => [] ],
                ];
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $user->calendario->inhabilitar_fecha($fechas);

    }
}
