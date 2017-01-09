<?php

namespace App\Http\Controllers;

use App\cita;
use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;

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
        $val =cita::dateTimeExist($request->all());
        if ($val) {
            $rules = array(
                    
                        'calendario_id' => 'required|numeric|max:255',
                        'tipo_id' => 'required|numeric',
                        'fecha_inicio' => 'required|date_format:Y-m-d H:i:s',
                        'fecha_final' => 'required|date_format:Y-m-d H:i:s',
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
                
            cita::crear($request->all());
        } else {
            return response()->json(array(
                                            'success' => false,
                                            'errors' => 'no se puede agendar esa hora'
                                            ),
                                404);
        }
    }

    /**
     * Display the specified resource.
     *
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
    public function reagendar(Request $request, $id)
    {
        $rules = array(
                        'id_servicio' => 'required',
                        'fecha_inicio' => 'required|date',
                        'fecha_final' => 'required|date',
                );
        $validator = Validator::make($request->all(), $rules);
        if (cita::dateTimeExist($request->all)) {
            return response()->json(array(
                                            'success' => false,
                                            'errors' => 'no se puede agendar esa fecha'
                                            ),
                                404);
        } else {
            if ($validator->fails()) {
                return response()->json(array(
                                            'success' => false,
                                            'errors' => $validator->getMessageBag()->toArray()
                                            ),
                                400); // 400 being the HTTP code for an invalid request.
            } else {
                cita::reagendar($request->all());
            }
        }

   
        cita::reagendar($request->all(), $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // new cita()->eliminar($id);
    }
    
    public function horasDisponibles(Request $request)
    {
        //$request[dia]
       //request[duracion]
       $dia =$request['dia'];//'2016-11-11';
        $tipo=$request['tipo_id'];//30;

        $horasDisponibles= cita::timeslot($dia,$tipo);
        //dd($horasDisponibles);
       return \Response::json($horasDisponibles, 200);
    }
    public function disponibilidadCalendario(Request $request)
    {
        $tipo=intval($request['tipo_id']);
        $horasDisponibles= cita::disponibilidadCal($tipo);
        return \Response::json($horasDisponibles, 200);
    }
}
