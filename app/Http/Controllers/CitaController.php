<?php

namespace App\Http\Controllers;

use App\cita;
use Illuminate\Http\Request;

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
                  $this->validate($request, [
        'calendario_id' => 'required|numeric',
        'tipo_id' => 'required|numeric',
        'fecha_inicio' => 'required|date',
        'fecha_final' => 'required|date',
        'cliente_nombre' => 'required|max:255',
        'cliente_telefono' => 'required|max:255',
        'cliente_email' => 'required|email',
    ]);
                   ///agregar parametros
        new cita->crear($request);
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
    public function update(Request $request, cita $cita)
    {
                          $this->validate($request, [
        'calendario_id' => 'required|numeric',
        'tipo_id' => 'required|numeric',
        'fecha_inicio' => 'required|date',
        'fecha_final' => 'required|date',
        'cliente_nombre' => 'required|max:255',
        'cliente_telefono' => 'required|max:255',
        'cliente_email' => 'required|email',
    ]);
        new cita->editar($request,$cita);    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\cita  $cita
     * @return \Illuminate\Http\Response
     */
    public function destroy(cita $cita)
    {
        new cita->eliminar($cita);
    }
}
