<?php

namespace App\Http\Controllers;

use App\tipo;
use Illuminate\Http\Request;
use JWTAuth;
use Validator;


class TipoController extends Controller
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
          $servicios = $user->calendario->tipos;

          return response()->json($servicios,200);

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
            'nombre' => 'required|unique:tipo|max:255',
            'duracion' => 'required',
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

        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);

        $calendario = $user->calendario;
        
        tipo::crear($request->all(),$calendario);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\tipo  $tipo
     * @return \Illuminate\Http\Response
     */
    public function show(tipo $tipo)
    {
       
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\tipo  $tipo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, tipo $tipo)
    {
                $rules = array(
            'nombre' => 'required|unique:posts|max:255',
            'duracion' => 'required',
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
       tipo::editar($request->all(),$tipo);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\tipo  $tipo
     * @return \Illuminate\Http\Response
     */
    public function destroy(tipo $tipo)
    {
        tipo::eliminar($tipo);
    }
}
