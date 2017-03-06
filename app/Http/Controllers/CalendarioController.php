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
use Carbon\Carbon;

class CalendarioController extends Controller
{
    /**
  * Function index ?????????
  * regresa
  * @param (datetime[])($rango) fecha inicial,fecha final
  * @return 1 en  caso de que la hora sea libre, fechafinal de la cita cuando exista una en ese rango
 */
    public function index()
    {
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $calendario =$user->calendario;
        $servicios = $calendario->tipos;
        $fechaActual= carbon::now();
        $citas =$calendario
                ->citas()
                ->where('fecha_final', '>', $fechaActual)
                ->get();//->whereMonth('fecha', '=', '06')->get();
       
       $events=array();
        $diasHabiles=$calendario->diasHabiles()->get();
        $diasNohabiles= array();
        $dias_semana= [1,2,3,4,5,6,7];
        foreach ($diasHabiles as $dia) {
            $dia_id= $dia->horasHabiles()->distinct()->select('diahabil_id')->get();
            if (count($dia_id)>0) {
                array_push($diasNohabiles, $dia_id->first()->diahabil_id);
            }
        }
            
        $DiasnoHabiles= array_values(array_diff($dias_semana, $diasNohabiles));
        $diasInhabiles=$calendario->fechasInhabiles()->pluck('fecha')->toArray();
        foreach ($citas as $cita) {
            $title=$cita->tipo->nombre;
            $start=$cita['fecha_inicio'];
            $end=$cita['fecha_final'];
            $id=$cita->id;
            $cliente_nombre = $cita->cliente_nombre;
            $cliente_telefono = $cita->cliente_telefono;
            $cliente_email = $cita->cliente_email;
            $cita_tipo      = $cita->tipo->nombre;
            array_push($events, ['id' => $id,'codigo' => $cita->codigo,'title' => $title, 'start' => $start, 'end' => $end,'cliente_nombre' => $cliente_nombre , 'cliente_telefono' => $cliente_telefono,'cliente_email' => $cliente_email , 'servicio' => $cita_tipo ]);
        }
 
        return \Response::json(['citas' => $events, 'servicios' => $servicios,'dia_no_habil'=>$DiasnoHabiles,'dias_inhabiles'=>$diasInhabiles], 200);
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

  /**
  * Function setDiasHabililes
  * crea o actualiza los días habiles del calendario
  * @param (int[])(int)(int) dias,hora_inicio,hora_final
  * ejemplo de arreglo dias ['dia' => '1','horas' => ['1' => true,'2' => false,'3' => true,'4' => true,'5' => true'6' => true,'7' => true] ]
  * las horas siempre tienen que venir las 7 del cliente aun que no haya modificado todas,
  * lo único que cambia es su valor booleano que lo acompaña en dicho día, e indica si el día está disponible o no disponible (laboral|nolaboral)
  * @return json con código de estado 200 cuando el proceso se llevó acabo de manera exitosa
 */

  
    public function setDiasHabiles(Request $request)
    {
        $dias_habiles_request = $request->get('dias');
        $hora_inicio = $request->get('hora_inicio');
        $hora_final = $request->get('hora_final');
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $dias_habiles = array();


        //recorro cada uno de los días hábiles que he obtenido desde el cliente
        foreach ($dias_habiles_request as $dia_habil) {
            $horas = array();

            //voy recorriendo cada una de las horas
            foreach ($dia_habil['horas'] as $hora) {
                //cada hora tiene está marcado como disponible o no disponible (true | false)
                 if ($hora['disponible']) {
                     array_push($horas, $hora['hora']);
                 }
            }
            // se agregan en este array con la estructura de datos que la clase calendario puede interpretar
            array_push($dias_habiles, ['dia' => $dia_habil['dia'] , 'horas' => $horas , 'laboral' => $dia_habil['laboral'] ]);
        }
        //el calendario tiene 2 atributos (hora inicio,hora final) el cual solo contiene el rango de la hora inicio a la hora final
        $user->calendario->hora_inicio = $hora_inicio;
        $user->calendario->hora_final = $hora_final;
        $user->push();
        $user->calendario->asignar_horario($dias_habiles);

        return response()->json(null, 200);
    }

      /**
  * Function getDiasHorasInhabiles
  * obtiene un listado de las horas inhabiles del calendario
  * @param (int) calendario_id
  * @return json con la lista de día inhabiles con código de estado 200 cuando el proceso se llevó acabo de manera exitosa
 */


    public function getDiasHorasInhabiles(Request $request)
    {
        $calendario_id = $request->get('calendario_id');
        $calendario = calendario::find($calendario_id);
        if (!$calendario) {
            return response()->json(['errors' => ['not_found' => ['No se especifico un calendario']]], 404);
        }
        $dias_inhabiles  = $calendario->fechasInhabiles()->orderBy('fecha', 'asc')->get();
 //       dd($dias_inhabiles);
        $dias_inhabiles_arr = array();
        foreach ($dias_inhabiles as $dia_inhabil) {
            array_push($dias_inhabiles_arr, ['dia' => $dia_inhabil->fecha,'id'=> $dia_inhabil->id,'completo' => $dia_inhabil->completo,'horas' => $dia_inhabil->horasInhabiles]);
        }
        return response()->json($dias_inhabiles_arr, 200);
    }

  /**
  * Function setDiasHorasInhabiles
  * envia una fecha para que se deshabilite en el calendario, este día puede estár desactivado de manera completa o parcialmente (solo horas especificadas)
  * @param \Illuminate\Http\Request  $request ('fecha' => datetime,'completo' => integer,'horas' => String[])
  * @return json con la lista de día inhabiles con código de estado 200 cuando el proceso se llevó acabo de manera exitosa
 */
    public function setDiasHorasInhabiles(Request $request)
    {
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $rules = array(
            'fecha' => 'required|date_format:Y-m-d',
            'completo' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(array(
                                            'success' => false,
                                            'errors' => $validator->getMessageBag()->toArray()
                                            ),
                                400); // 400 being the HTTP code for an invalid request.
        }
        if (!$request->get('completo')) {
            if ($request->get('horas') == []) {
                return response()->json(['errors' => ['no_horas' => ['No se han especificado las horas que se van a deshabilitar']]], 400);
            }
        }
        //especifica la fecha que se desea inhabilitar
        $dia_inhabil_r = $request->get('fecha');
        //especifica si el día estará deshabilitado completamente o sólo parcialmente
        $completo = $request->get('completo');
        //en caso de que el dia se va a deshabilitar sólo parcialmente se especifica las horas
        $horas = $request->get('horas');

        $calendario = $user->calendario;
        if ($calendario->fechasInhabiles()->where('fecha', $dia_inhabil_r)->first()) {
            return response()->json(null, 400);
        }
     
        $dia_inhabil = new fecha_inhabil();
        $dia_inhabil->fecha = $dia_inhabil_r;
        $dia_inhabil->completo = $completo;
        $calendario->fechasInhabiles()->save($dia_inhabil);
        //lista todas las horas que anteriormente se desahabilitaron pero que actualmente no viene en el request
        //con la finalidad de eliminarlos en caso de que existan
        $dia_inhabil->horasInhabiles()->whereNotIn('hora', $horas)->delete();
        if (!$completo) {
            foreach ($horas as $hora) {
                $hora_inhabil = fechahora_inhabil::firstOrNew(['fechainhabil_id'=> $dia_inhabil->id, 'hora' => $hora]);
                $dia_inhabil->horasInhabiles()->save($hora_inhabil);
            }
        }
    }

    /**
  * Function deleteDiasHorasInhabiles
  * elimina una fecha inhabil que se ha creado anteriormente
  * @param \Illuminate\Http\Request  $request ('fecha' => 'fecha_inhabil_id')
  * @return json con con codigo de status 200 cuando la peticion se ha atendido de manera correcta
 */
    public function deleteDiasHorasInhabiles(Request $request)
    {
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $fecha_inhabil_id = $request->get('fecha_inhabil_id');

        $fecha_inhabil = fecha_inhabil::find($fecha_inhabil_id);
        if ($fecha_inhabil) {
            $fecha_inhabil->delete();
            return response()->json(null, 200);
        } else {
            return response()->json(['error' => true,'message' => 'Fecha inhabil no encontrada'], 404);
        }
    }
/////desde aca parece que no se usa
    private $horas_filtrado = [8,9,10,11,12,13,15,16,17,18,19];
    public function algoritmo()
    {
        $horas_propuestas = array();
        $duracion_servicio = 132/100;
        $hora_inicial = reset($this->horas_filtrado);
        $hora_final_dia = end($this->horas_filtrado);
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
            if (
                    floatval($hora_inicial)<= floatval($cita['hora_inicial'])  && floatval($hora_final) >= floatval($cita['hora_inicial'])
                                                    ||
                   floatval($cita['hora_inicial']) <= floatval($hora_inicial)  && floatval($cita['hora_final']) >= floatval($hora_final)
                    ) {
                return $cita;
            }
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

        if ($cita != false) {
            return $this->rellenarHoras($d_s, $cita['hora_final']+0.01, $horas_propuestas, $hora_final_dia);
        } else {
            $hora_inicial_next = $this->nextDisponible($hora_inicial);
            if ($hora_inicial_next == $hora_inicial) {
                array_push($horas_propuestas, $hora_inicial);
                $hora_final_tmp = floatval($hora_inicial_next + $duracion_servicio);
                echo "\n";
                echo "Next devolvio : ".$hora_inicial_next;
                echo "\n";
                echo "Se hizo push de : ".$hora_inicial;
                echo "Se calcula ahora : ".$hora_final_tmp;
                echo "\n";
                return $this->rellenarHoras($duracion_servicio, $hora_final_tmp, $horas_propuestas, $hora_final_dia);
            } else {
                return $this->rellenarHoras($d_s, $hora_inicial_next, $horas_propuestas, $h_f_d);
            }
        }
    }
    public function nextDisponible($hora)
    {
        $_h = intval($hora);
        if (end($this->horas_filtrado) < $_h) {
            return $hora;
        }
        $flag = false;
        foreach ($this->horas_filtrado as $h_f) {
            if ($h_f == $_h) {
                $flag = true;
                return $hora;
            }
        }
        if ($flag == false) {
            return $this->nextDisponible($hora+1);
        }
    }
}
