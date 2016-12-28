<?php

namespace App\Http\Controllers;

use App\cita;
use Illuminate\Http\Request;
use Validator;
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
     * Store a newly created resource in storage.
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
        				'cliente_email' => 'required|email',
    
            	);

            $validator = Validator::make($request->all(), $rules);

  

      if ($validator->fails())
            {
                return response()->json(array(
                                            'success' => false,
                                            'errors' => $validator->getMessageBag()->toArray()

                                            ), 
                                400); // 400 being the HTTP code for an invalid request.
        
            }
                
       cita::crear($request->all());

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
    public function update(Request $request,$id)
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
      if ($validator->fails())
            {
                return response()->json(array(
                                            'success' => false,
                                            'errors' => $validator->getMessageBag()->toArray()
                                            ), 
                                400); // 400 being the HTTP code for an invalid request.
            }
    cita::editar($request->all(),$id);    
    }
    public function reagendar(Request $request,$id)
    {
                    $rules = array(

                        'fecha_inicio' => 'required|date',
                        'fecha_final' => 'required|date',
                );
            $validator = Validator::make($request->all(), $rules);
      if ($validator->fails())
            {
                return response()->json(array(
                                            'success' => false,
                                            'errors' => $validator->getMessageBag()->toArray()
                                            ), 
                                400); // 400 being the HTTP code for an invalid request.
            }
    cita::reagendar($request->all(),$id);    
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

}
