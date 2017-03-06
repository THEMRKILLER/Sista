<?php

namespace App\Http\Controllers;

use App\tipo;
use App\calendario;
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
    public function index(Request $request)
    {   
        $calendario_id = $request->get('calendario_id');
        $calendario = calendario::find($calendario_id);
        $servicios = $calendario->tipos;

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
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);

        $rules = array(
            'nombre' => 'required|max:255',
            'duracion' => 'required|integer|max:4',
            'costo'     => 'required|numeric|min:0|'
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

        $nombre = $request->get('nombre');
        if(tipo::where('calendario_id',$user->calendario->id)->where('nombre',$nombre)->count() > 0)
        {
             return response()->json(array(
                                            'success' => false,
                                            'errors' => array(['Ya existe un servicio con el mismo nombre, intente con otro'])
                                            ), 
                                400); // 400 being the HTTP code for an invalid request.
        }
         

 

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
    public function update(Request $request)
    {
                $rules = array(
            'nombre' => 'required|max:255',
            'duracion' => 'required',
            'costo'     => 'required|min:0',
            'denominacion' => 'required|string|max:255'
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

        $id  = $request->get('id');
        $tipo = $user->calendario->tipos()->where('id',$id)->first();
        if(!$tipo) return response()->json(['errors'=> ['Acceso denegado'],403]);
        $tipo->editar($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\tipo  $tipo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $id = $request->get('id');
        
        $tipo = $user->calendario->tipos()->where('id',$id)->first();
        if(!$tipo) return response()->json(['errors'=> [ 'cupon_not_found' => ['Cupon no encontrado']]],404);
        //dd($tipo);
        $tipo->delete();
        return response()->json(null,200);

    }
}
