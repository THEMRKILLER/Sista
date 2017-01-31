<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cupon;
use App\tipo;
use Validator;
use App\calendario;
class CuponController extends Controller
{
    public function create(Request $request)
    {
    	$rules = array(
                    
                        'porcentaje' => 'required|numeric|max:100|min:0',
                        'fecha_inicial' => 'required|date_format:Y-m-d|before_or_equal:fecha_final',
                        'fecha_final' => 'required|date_format:Y-m-d|after_or_equal:fecha_inicial',
                    
                );

            $validator = Validator::make($request->all(), $rules);

  

            if ($validator->fails()) {
                return response()->json(array(
                                            'success' => false,
                                            'errors' => $validator->getMessageBag()->toArray()
                                            ),
                                400); // 400 being the HTTP code for an invalid request.
            }


            $servicio = tipo::find($request->get('servicio_id'));
            if(!$servicio) return response()->json(null,404);
            $cupon = new Cupon();
            $codigo  = null;
            $repeated = true;

            do{
                $codigo = uniqid();
                $repeated = Cupon::where('codigo',$codigo)->count() > 0;
            }
            while($repeated);
            $cupon->codigo = $codigo;
            $cupon->porcentaje = $request->get('porcentaje');
            $cupon->fecha_inicial = $request->get('fecha_inicial');
            $cupon->fecha_final = $request->get('fecha_final');
            $servicio->cupones()->save($cupon);

            return response()->json(['codigo' => $cupon->codigo],200);

    }

    public function index(Request $request)
    {
        $calendario_id = $request->get('calendario_id');
        $calendario = calendario::find($calendario_id);
        $cupones = $calendario->cupones;
        $servicios = $calendario->tipos;

        return response()->json(['cupones' => $cupones,'servicios' => $servicios],200);  

    }
}
