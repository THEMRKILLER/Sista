<?php

namespace App\Http\Controllers;

use App\calendario;
use Illuminate\Http\Request;
use JWTAuth;
use json;
use Validator;
use Auth;
use App\fecha_inhabil;
use App\fechahora_inhabil;

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
 
        return \Response::json(['citas' => $events, 'servicios' => $servicios], 200);
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

    public function url(Request $request)
    {
        $url =  $request->getHttpHost();
        echo $url;
    }

    public function asignar_horario_validate($horario)
    {
    }

    public function inhabilitar_fecha(Request $request)
    {
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $user->calendario->inhabilitar_fecha($fechas);
    }

    /**
     * Obtiene los días habiles así como también las horas de servicio de cada día habil
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */

    public function getDiasHabiles(Request $request)
    {
        $calendario_id = $request->get('calendario');

        $calendario = calendario::find($calendario_id);
        $dias_habiles = array();
        $c_dias_habiles = $calendario->diasHabiles;
        $horas_dia_habil = array(); //declaracion global
        foreach ($c_dias_habiles as $dia_habil) {
            $horas_dia_habil = array(); // se limpia el array
                //se recorre las horas de los días habiles para darle un formato que el
                //cliente pueda interpretar
                foreach ($dia_habil->horasHabiles as $hora_model) {
                    array_push($horas_dia_habil, $hora_model->hora);
                }
            array_push($dias_habiles, ['dia' => $dia_habil->dia,'horas' => $horas_dia_habil]);
        }

        if (count($dias_habiles) > 0) {
            return response()->json(['horario' => $dias_habiles, 'hora_inicio' => $calendario->hora_inicio,'hora_final' => $calendario->hora_final], 200);
        } else {
            return response()->json(null, 404);
        }
    }

    public function setDiasHabiles(Request $request)
    {
        $dias_habiles_request = $request->get('dias');
        $hora_inicio = $request->get('hora_inicio');
        $hora_final = $request->get('hora_final');

        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);

        $dias_habiles = array();

        foreach ($dias_habiles_request as $dia_habil) {
            $horas = array();

            foreach ($dia_habil['horas'] as $hora) {
                if ($hora['disponible']) {
                    array_push($horas, $hora['hora']);
                } else {
                }
            }
               
            array_push($dias_habiles, ['dia' => $dia_habil['dia'] , 'horas' => $horas , 'laboral' => $dia_habil['laboral'] ]);
        }

        $user->calendario->hora_inicio = $hora_inicio;
        $user->calendario->hora_final = $hora_final;
        $user->push();
        $user->calendario->asignar_horario($dias_habiles);
    }
    public function getDiasHorasInhabiles(Request $request)
    {
        $calendario_id = $request->get('calendario_id');
        $calendario = calendario::find($calendario_id);
        $dias_inhabiles  = $calendario->fechasInhabiles;
 //       dd($dias_inhabiles);
        $dias_inhabiles_arr = array();

        foreach ($dias_inhabiles as $dia_inhabil) {
            array_push($dias_inhabiles_arr, ['dia' => $dia_inhabil->fecha,'completo' => $dia_inhabil->completo,'horas' => $dia_inhabil->horasInhabiles]);
        }
        return response()->json($dias_inhabiles_arr, 200);
    }
    public function setDiasHorasInhabiles(Request $request)
    {
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);

        $dia_inhabil_r = $request->get('fecha');
        $completo = $request->get('completo');
        $horas = $request->get('horas');

        $calendario = $user->calendario;
        if ($calendario->fechasInhabiles()->where('fecha', $dia_inhabil_r)->first()) {
            return response()->json(null, 400);
        }
        

       // dd(get_class_methods($user->calendario->fechasInhabiles->horasInhabiles) );
        $dia_inhabil = new fecha_inhabil();
        $dia_inhabil->fecha = $dia_inhabil_r;
        $dia_inhabil->completo = $completo;

        $calendario->fechasInhabiles()->save($dia_inhabil);

        $dia_inhabil->horasInhabiles()->whereNotIn('hora', $horas)->delete();

        if (!$completo) {
            foreach ($horas as $hora) {
                $hora_inhabil = fechahora_inhabil::firstOrNew(['fechainhabil_id'=> $dia_inhabil->id, 'hora' => $hora]);
                $dia_inhabil->horasInhabiles()->save($hora_inhabil);
            }
        }
    }

    private $horas_filtrado = [8,9,10,11,12,13,15,16,17,18,19];

    public function algoritmo()
    {
        $horas_propuestas = array();
        $duracion_servicio = 50/100;
        $hora_inicial = reset($this->horas_filtrado);
        $hora_final_dia = end($this->horas_filtrado);
        $horas_propuestas = $this->rellenarHoras($duracion_servicio, $hora_inicial, $horas_propuestas, $hora_final_dia);
        return $horas_propuestas;
    }

    public function consultarCita($hora_inicial, $hora_final)
    {
        $hora_final = $hora_final - 0.01;
        
        $citas = [

            ['hora_inicial' => 8 , 'hora_final' => 8.99  ],

            ['hora_inicial' => 10 , 'hora_final' => 11.99  ],

            ['hora_inicial' => 12 , 'hora_final' => 12.99  ],

            ['hora_inicial' => 15 , 'hora_final' => 16.99  ],
        ];



        foreach ($citas as $cita) {
<<<<<<< HEAD
            if (
                    $hora_inicial <= $cita['hora_inicial'] && $hora_final >= $cita['hora_inicial']
                                                    ||
                    $cita['hora_inicial'] <= $hora_inicial && $cita['hora_final'] >= $hora_final
                    ) {
                return $cita;
            }
=======


                if( 
                    floatval($hora_inicial )<= floatval($cita['hora_inicial'])  && floatval($hora_final) >= floatval($cita['hora_inicial'])
                                                    ||
                   floatval($cita['hora_inicial'] ) <= floatval($hora_inicial)  && floatval($cita['hora_final'] ) >= floatval($hora_final) 
                    )
                    return $cita;
>>>>>>> origin/master
        }


        return false;
    }

    public function rellenarHoras($duracion_servicio, $hora_inicial, $horas_propuestas, $hora_final_dia)
    {
        $h_p = $horas_propuestas;
        if ($hora_inicial >= $hora_final_dia) {
            return $h_p;
        }

        $d_s = $duracion_servicio;
        $h_f_d  = $hora_final_dia;

        $hora_final = floatval($hora_inicial + $duracion_servicio);
        
        $cita = $this->consultarCita($hora_inicial, $hora_final);


<<<<<<< HEAD
        if ($cita) {
            return $this->rellenarHoras($d_s, $cita['hora_final']+0.01, $h_p, $h_f_d);
        } else {
            $hora_inicial_next = $this->nextDisponible($hora_inicial);
            if ($hora_inicial == false) {
                return $horas_propuestas;
            }

            if ($hora_inicial_next == $hora_inicial) {
                array_push($horas_propuestas, $hora_inicial);
            } else {
                return $this->rellenarHoras($d_s, $hora_inicial_next, $horas_propuestas, $h_f_d);
            }

        
            return $this->rellenarHoras($d_s, $hora_final, $horas_propuestas, $h_f_d);
        }
    }

    public function nextDisponible($hora)
    {
        $_h = intval($hora);
        if (end($this->horas_filtrado) < $_h) {
            return $hora;
        }
            
        foreach ($this->horas_filtrado as $h_f) {
            if ($h_f == $_h) {
                return $h_f;
            }
        }

        return $this->nextDisponible($_h+1);
=======
    if($cita != false)
    {
        return $this->rellenarHoras($d_s,$cita['hora_final']+0.01,$horas_propuestas,$hora_final_dia );
    }
    else{

        $hora_inicial_next = $this->nextDisponible($hora_inicial);        

        if($hora_inicial_next == $hora_inicial ) {
            array_push($horas_propuestas,$hora_inicial);
            $hora_final_tmp = floatval($hora_inicial_next + $duracion_servicio);
            echo "\n";
            echo "Next devolvio : ".$hora_inicial_next;
            echo "\n";
            echo "Se hizo push de : ".$hora_inicial;
            echo "Se calcula ahora : ".$hora_final_tmp;
            echo "\n";
            return $this->rellenarHoras($duracion_servicio,$hora_final_tmp,$horas_propuestas,$hora_final_dia );


        }
        else 
            {
                return $this->rellenarHoras($d_s,$hora_inicial_next,$horas_propuestas,$h_f_d );
            }

        
    }


    }


    function nextDisponible($hora)
    {

            $_h = intval($hora);
            if(end($this->horas_filtrado) < $_h) return $hora; 
            $flag = false;
            foreach($this->horas_filtrado as $h_f)
            {
                if($h_f == $_h) {
                    $flag = true;
                    return $hora;
                }
            }
           if($flag == false) return $this->nextDisponible($hora+1);
>>>>>>> origin/master
    }
}
