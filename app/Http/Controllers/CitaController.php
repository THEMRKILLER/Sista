<?php

namespace App\Http\Controllers;

use App\cita;
use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;
use Nexmo\Client;
use App\Mail\NotificacionNCita;
use App\tipo;
use App\calendario;
use App\Cupon;

class CitaController extends Controller
{
 
    /**
     * Store a newly crea6ted resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
  
       
        $rules = array(
                        'fecha_inicio' => 'required|date_format:Y-m-d H:i:s',
                        'cliente_nombre' => 'required',
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
        if ( $val) {

                $cupon_descuento = $request->get('cupon_descuento');
                $costo_total = $request->get('costo_total');
                  
                   if (tipo::find($request->get('tipo_id'))===null) {
            return response()->json([
                    'error' => true,
                    'message' => 'se ah tratado de acceder a un recurso que no existe'
                ], 404);
        } else { 
            
            $servicio = tipo::find($request->get('tipo_id'));
            if(!$this->validar_costo($cupon_descuento,$costo_total,$servicio)) 
                return response()->json(['errors' => ['No es posible agendar la cita por que los datos que se proporcionaron no son los correctos, verifiquelos y vuelva a intentar'] ],400);
            
            cita::crear($request->all(),$this->generarCodigoCita());
        }



        } else {
             

            $val=cita::fechaDisponible($request->all())&&cita::revisarDiasInhabiles($request->all());
                  
            if ($val) {

                cita::crear($request->all(), $this->generarCodigoCita());

            } else {
                return response()->json(array(
                                            'success' => false,
                                            'errors' => 'no se puede agendar esa fecha'
                                            ), 404);
            }
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\cita  $cita
     * @return \Illuminate\Http\Response
     */
    public function reagendar(Request $request)
    {
        $rules = array(
                        
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

            if (cita::fechaDisponible($request->all())&&cita::revisarDiasInhabiles($request->all())) {
                
                return cita::reagendar($request->all());
            } else {
                return response()->json(array(
                                            'success' => false,
                                            'errors' => 'no se puede agendar esa fecha'
                                            ),
                                409 );
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
    public function generarCodigoCita()
    {
        $flag = true;
        $codigo = '';
        do {
            $codigo = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 5);
            $flag = cita::where('codigo', $codigo)->count() > 0;
        } while ($flag);

        return $codigo;
    }

    public function comprobar(Request $request)
    {
        $cita =  cita::where('codigo', $request->get('codigo'))
                     ->where(function ($query) use ($request) {
                         /** @var $query Illuminate\Database\Query\Builder  */
                            return $query->where('cliente_telefono', $request->get('numeromail'))
                                ->orWhere('cliente_email', $request->get('numeromail'));
                     })->first();
        $existe_cita = $cita != null ? true : false;
            
            /*
                    vm.cita_codigo_seleccionado = response.data.cita.codigo;
                    vm.cita_cliente_nombre = response.data.cita.cliente_nombre;
                    vm.cita_cliente_servicio = response.data.servicio;
                    vm.cita_cliente_fecha = response.data.fecha;

            */
        if ($existe_cita) {
            $cita_arr = [
                        'codigo' => $cita->codigo,
                        'cliente_nombre' => $cita->cliente_nombre,
                        'servicioid' => $cita->tipo->id,
                        'fecha' => $cita->fecha_inicio,
                        'id' => $cita->id
                        ];
            return response()->json(['success' => true, 'cita' => $cita_arr], 200);
        } else {
            return response()->json(['success' => false, 'errors' => ['No existe una cita agendada con estos datos']], 404);
        }
    }

        private function validar_costo($cupon_descuento,$costo_total,$servicio)
    {
        $porcentaje_descuento = 0;
     
        $costo_original_servicio = $servicio->costo;

        if($cupon_descuento === null || $cupon_descuento==='' || $cupon_descuento === 0)
        {
            if($costo_total == $costo_original_servicio) return true;
            else return false;
        }
        else {
            $cupon = Cupon::where('codigo',$cupon_descuento)->first();
            if(!$cupon) return false;
            else {
                $porcentaje_descuento = $cupon->porcentaje;
                $precio_con_descuento = $costo_original_servicio - ($costo_original_servicio * ($porcentaje_descuento/100));

                if($costo_total == $precio_con_descuento) return true;
                else return false;
            }
        }

    }
    public function verificarCalendario($idCalendario,$idServicio)
    {
           if (calendario::find($idCalendario)===null||tipo::find($idServicio)===null) {
            return response()->json([
                    'error' => true,
                    'message' => 'se ah tratado de acceder a un recurso que no existe'
                ], 404);
        } else {
             $tipo = tipo::find($idServicio);
             $calendario_servicio=$tipo->calendario;
             if($idCalendario===$calendario_servicio){
                return true;
             }else{
                               return response()->json(array(
                                            'success' => false,
                                            'errors' => 'no se puede acceder a ese recurso'
                                            ),
                                403 );
             }

        }
    }
}
