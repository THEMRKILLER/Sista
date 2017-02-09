<?php

namespace App\Http\Controllers;

use App\cita;
use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;
use Nexmo\Client;
use App\Mail\NotificacionNCita;

class CitaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
    /**
     * Store a newly crea6ted resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
       

            $rules = array(
                        'calendario_id' => 'required|numeric|max:255',
                        'tipo_id' => 'required|numeric',
                        'fecha_inicio' => 'required|date_format:Y-m-d H:i:s',
                        'fecha_final' => 'required|date_format:Y-m-d H:i:s',
                        'cliente_nombre' => 'required|',
                        'cliente_telefono' => 'required',
                        'cliente_email' => 'email',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(array(
                                            'success' => false,
                                            'errors' => $validator->getMessageBag()->toArray()
                                            ),
                                400); // 400 being the HTTP code for an invalid request.
            }

            
                    $val=cita::fechaDisponible($request->all())&&cita::revisarDiasInhabiles($request->all() );
                    var_dump(cita::revisarDiasInhabiles($request->all()));
        if ( $val) {
            cita::crear($request->all(),$this->generarCodigoCita());
        } else {
            return response()->json(array(
                                            'success' => false,
                                            'errors' => 'no se puede agendar esa fecha'
                                            ), 404);
        }
    }
    /**
     * Display the specified resource.
     * @param  \App\cita  $cita
     * @return \Illuminate\Http\Response
     */
    public function show(cita $cita)
    {
        //
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\cita  $cita
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = array(
                        'calendario_id' => 'required|numeric|max:255',
                        'tipo_id' => 'required|numeric',
                        'fecha_inicio' => 'required|date',
                        'fecha_final' => 'required|date',
                        'cliente_nombre' => 'required|',
                        'cliente_telefono' => 'required',
                        'cliente_email' => 'required|email',
                );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(array(
                                            'success' => false,
                                            'errors' => $validator->getMessageBag()->toArray()
                                            ),
                                400); // 400 being the HTTP code for an invalid request.
        }
        cita::editar($request->all(), $id);
    }

    public function reagendar(Request $request)
    {

        $rules = array(

                        'tipo_id' => 'required',
                        'fecha_inicio' => 'required|date',
                );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(array(
                                            'success' => false,
                                            'errors' => $validator->getMessageBag()->toArray()
                                            ),
                                400); // 400 being the HTTP code for an invalid request.
        } else {

            if (cita::fechaDisponible($request->all() )&&cita::revisarDiasInhabiles($request->all() ) ) {
               return cita::reagendar($request->all());
            } else {
                
                                return response()->json(array(
                                            'success' => false,
                                            'errors' => 'no se puede agendar esa fecha'
                                            ),
                                404);
            }
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        return cita::eliminar($request->all());
    }
    
    public function horasDisponibles(Request $request)
    {
        $dia =$request['dia'];
        $tipo=$request['tipo_id'];
        $calendario_id=$request['calendario_id'];
        $horasDisponibles= cita::timeslot($dia, $tipo, $calendario_id);
        return \Response::json($horasDisponibles, 200);
    }
    public function disponibilidadCalendario(Request $request)
    {
        $tipo=intval($request['tipo_id']);
        $calendario_id=$request['calendario_id'];
        //dias que no hay ninguna horahabil
        $diasNoHabiles= cita::diasNoHabiles($calendario_id);
        //disponibilidad del dia en base al numero de huecos vacios
        $disponibilidad= cita::disponibilidadCal($tipo, $calendario_id);
      
        return response()->json(['disponibilidades' => $disponibilidad, 'no_laborales' => $diasNoHabiles], 200);
    }
    //ruta de prueba, eliminar
    public function filtrarHoras(Request $request)
    {
        //$request[dia]
       //request[duracion]
       $dia ='2017-01-05';
        $calendario_id=1;
        cita::filtrarHoras($dia, $calendario_id);
    }
    //ruta de prueba, eliminar
    public function inhabil(Request $request)
    {
        $dia ='2017-02-14';
        $calendario_id=1;
        $valor=cita::revisarDiasInhabiles($dia,$calendario_id);

        
    }

    public function generarCodigoCita()
    {
        $flag = true;
        $codigo = '';
        do{
            $codigo = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 5);
            $flag = cita::where('codigo',$codigo)->count() > 0;

        }
        while ($flag);

        return $codigo;
    }

    public function comprobar(Request $request)
    {   
        $cita =  cita::where('codigo',$request->get('codigo'))
                     ->where(function($query) use ($request) {
                            /** @var $query Illuminate\Database\Query\Builder  */
                            return $query->where('cliente_telefono',$request->get('numeromail'))
                                ->orWhere('cliente_email',$request->get('numeromail'));
                        })->first();
        $existe_cita = $cita != null ? true : false;
            
            /*
                    vm.cita_codigo_seleccionado = response.data.cita.codigo;
                    vm.cita_cliente_nombre = response.data.cita.cliente_nombre; 
                    vm.cita_cliente_servicio = response.data.servicio;
                    vm.cita_cliente_fecha = response.data.fecha; 
                                                
            */
        


        if($existe_cita)
        {
            $cita_arr = [
                        'codigo' => $cita->codigo, 
                        'cliente_nombre' => $cita->cliente_nombre,
                        'servicioid' => $cita->tipo->id,
                        'fecha' => $cita->fecha_inicio,
                        'id' => $cita->id
                        ];

            return response()->json(['success' => true, 'cita' => $cita_arr], 200);
        } 
        else return response()->json(['success' => false, 'errors' => ['No existe una cita agendada con estos datos']],404);    
    }
}
