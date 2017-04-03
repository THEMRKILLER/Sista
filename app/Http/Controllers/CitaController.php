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
        $idCalendario=intval($request->get('calendario_id'));
        $idTipo=intval($request->get('tipo_id'));
        $fechaActual=carbon::now();
        $verificarId=$this->verificarIdentificadoresA($idCalendario, $idTipo);
        if ($verificarId) {
            $VerificarCalendario= $this->verificarCalendario($idCalendario, $idTipo);
            if ($VerificarCalendario) {
                $calendario = calendario::find($idCalendario);
                $servicio = tipo::find($idTipo);
                $rules = array(
                        'fecha_inicio' => 'required|date_format:Y-m-d H:i:s|after:'.$fechaActual,
                        'cliente_nombre' => 'required|max:255',
                        'cliente_telefono' => 'required|digits:10',
                        'cliente_email' => 'email|max:255',
            );
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(array(
                                            'success' => false,
                                            'errors' => $validator->getMessageBag()->toArray()
                                            ),
                                400); // 400 being the HTTP code for an invalid request.
                }

            $fecha=$request->get('fecha_inicio');
                $val = cita::fechaDisponible($fecha,$servicio)&&cita::revisarDiasInhabiles($fecha,$calendario);
               echo cita::revisarDiasInhabiles($fecha,$calendario);
                if ($val) {
                    $cupon_descuento = $request->get('cupon_descuento');
                    $costo_total = $request->get('costo_total');
                    
                    if (!$this->validar_costo($cupon_descuento, $costo_total, $servicio)) {
                        return response()->json(['errors' => ['No es posible agendar la cita por que los datos que se proporcionaron no son los correctos, verifiquelos y vuelva a intentar'] ], 400);
                    } else {
                        return cita::crear($request->all(), $this->generarCodigoCita(), $calendario, $servicio);
                    }
                } else {
                    return response()->json(array(
                                            'success' => false,
                                            'errors' => 'no se puede agendar esa fecha'
                                            ), 404);
                }
            } else {
                return response()->json(array(
                                            'success' => false,
                                            'errors' => 'no se encontro el recurso calendario'
                                            ), 404);
            }
        } else {
            return response()->json([
                    'error' => true,
                    'message' => 'no se encontro el recurso calendario'
                ], 404);
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
        $idCalendario=intval($request->get('calendario_id'));
        $idTipo=intval($request->get('tipo_id'));
        $idCita=intval($request->get('id_cita'));
        $verificarId=$this->verificarIdentificadoresR($idCalendario, $idTipo, $idCita);
        if ($verificarId) {
            $revisarServicio= $this->verificarServicio($idCalendario, $idTipo, $idCita);
            if ($revisarServicio) {
                $rules = array('fecha_inicio' => 'required|date_format:Y-m-d H:i:s',);
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(array(
                                            'success' => false,
                                            'errors' => $validator->getMessageBag()->toArray()
                                            ),
                                400); // 400 being the HTTP code for an invalid request.
                } else {
                     $calendario = calendario::find($idCalendario);
                    $cita = cita::find($idCita);
                    $servicio = tipo::find($idTipo);
                     $fecha=$request->get('fecha_inicio');
                    if (cita::fechaDisponible($fecha,$servicio)&&cita::revisarDiasInhabiles($fecha,$calendario)) {
                        return cita::reagendar($request->all(), $cita, $servicio);
                    } else {
                        return response()->json(array(
                                            'success' => false,
                                            'errors' => 'no se puede agendar esa fecha'
                                            ),
                                404);
                    }
                }
            } else {
                return response()->json(array(
                                            'success' => false,
                                            'errors' => 'el calendario,servicio o cita al que se esta tratando de acceder no existe'
                                            ), 404);
            }
        } else {
            return response()->json(array(
                                            'success' => false,
                                            'errors' => 'el calendario,servicio o cita al que se esta tratando de acceder no existe'
                                            ), 404);
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
        $idTipo=intval($request['tipo_id']);
        $idCalendario=intval($request['calendario_id']);
        $verificarId=$this->verificarIdentificadoresA($idCalendario, $idTipo);
        if ($verificarId) {
            $VerificarCalendario= $this->verificarCalendario($idCalendario, $idTipo);
           
            if ($VerificarCalendario) {

                //una vez verificado el calendario y el tipo se procede a encontrar sus valores
                $calendario=calendario::find($idCalendario);
                $tipo=tipo::find($idTipo);
                $diasHabiles=$calendario->diasHabiles()->with('horasHabiles')->get();
                $diaInhabil=$calendario->fechasInhabiles()->with('horasInhabiles')->get();
               
                $horasDisponibles= cita::timeslot($dia, $tipo, $calendario, $diasHabiles, $diaInhabil);
                $fechaActual=carbon::now();
                foreach ($horasDisponibles as $key => $hora) {
                    if ($fechaActual->toDateTimeString() > $hora['value']) {
                        unset($horasDisponibles[$key]);
                    }
                }
                $horasDisponibles=array_values($horasDisponibles);
                return \Response::json($horasDisponibles, 200);
            } else {
                return \Response::json('acceso restringido a calendario', 403);
            }
        } else {
            return \Response::json('el recurso al que se esta tratando de acceder no esta disponible', 404);
        }
    }
    public function disponibilidadCalendario(Request $request)
    {
        $idTipo=intval($request['tipo_id']);
        $idCalendario=intval($request['calendario_id']);
        $mes=$request['mes'];
        $verificarId=$this->verificarIdentificadoresA($idCalendario, $idTipo);
        if ($verificarId) {
            $VerificarCalendario= $this->verificarCalendario($idCalendario, $idTipo);
            if ($VerificarCalendario) {
                //una vez verificado el calendario y el tipo se procede a encontrar sus valores
                $calendario=calendario::find($idCalendario);
                $tipo=tipo::find($idTipo);
                //dias que no hay ninguna horahabil

                $diasNoHabiles= cita::diasNoHabiles($calendario);
                //disponibilidad del dia en base al numero de huecos vacios
                
                $disponibilidad=cita::disponibilidadCal($tipo, $calendario, $mes);
      

                return response()->json(['disponibilidades' => $disponibilidad, 'no_laborales' => $diasNoHabiles], 200);
            } else {
                return \Response::json('acceso restringido a calendario', 403);
            }
        } else {
            return \Response::json('el recurso al que se esta tratando de acceder no esta disponible', 404);
        }
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

    private function validar_costo($cupon_descuento, $costo_total, $servicio)
    {
        $porcentaje_descuento = 0;
     
        $costo_original_servicio = $servicio->costo;

        if ($cupon_descuento === null || $cupon_descuento==='' || $cupon_descuento === 0) {
            if ($costo_total == $costo_original_servicio) {
                return true;
            } else {
                return false;
            }
        } else {
            $cupon = Cupon::where('codigo', $cupon_descuento)->first();
            if (!$cupon) {
                return false;
            } else {
                $porcentaje_descuento = $cupon->porcentaje;
                $precio_con_descuento = $costo_original_servicio - ($costo_original_servicio * ($porcentaje_descuento/100));

                if ($costo_total == $precio_con_descuento) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }
    public function verificarCalendario($idCalendario, $idServicio)
    {
        $idCalendario_desdeTipo = tipo::find($idServicio)->calendario['id'];
    
        if ($idCalendario===$idCalendario_desdeTipo) {
            return true;
        } else {
            return false;
        }
    }
    public function verificarServicio($idCalendario, $idServicio, $idCita)
    {
        $idCalendario_desdeCita = cita::find($idCita)->calendario['id'];
        $idCalendario_desdeTipo = tipo::find($idServicio)->calendario['id'];
               
        if ($idCalendario===$idCalendario_desdeTipo&&$idCalendario===$idCalendario_desdeCita) {
            return true;
        } else {
            return false;
        }
    }
    public function verificarIdentificadoresA($idCalendario, $idServicio)
    {
        if (calendario::find($idCalendario)===null||tipo::find($idServicio)===null) {
            return false;
        } else {
            return true;
        }
    }
    public function verificarIdentificadoresR($idCalendario, $idServicio, $idCita)
    {
        if (calendario::find($idCalendario)===null||tipo::find($idServicio)===null||cita::find($idCita)===null) {
            return false;
        } else {
            return true;
        }
    }
}
