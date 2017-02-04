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
            $codigo = '';
            if(!$servicio) return response()->json(['errors' => 'El servicio no existe'],404);

            if($request->get('word_key') == null || $request->get('word_key') == '' || $request->get('word_key') == '')
            {
                $codigo = $this->generarCodigo(null);

            }
            else {
                $exist = Cupon::where('codigo',$request->get('word_key'))->count() > 0 ? true : false ;
                if($exist) return response()->json(['errors' => ['Ya existe un cupon con el mismo código, escoja otro código']],404);
                $codigo = $request->get('word_key');

            }
            $cupon = new Cupon();
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

    public function generarCodigo($key)
    {
            $codigo  = null;
            $repeated = true;
            $numero = 3; 
            $count = 0;        
            do{
                $count++;
                if($count > 100) {
                   $numero = $numero + 1;
                   $count = 0;    

                }
                if($key == '' || $key == null || $key == ' ' || $key == 'undefined') $codigo = uniqid();
                else $codigo = $key.rand(0, 10).substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", $numero)), 0, $numero);          
            
                $repeated = Cupon::where('codigo',$codigo)->count() > 0;
            }
            while($repeated);   

            return $codigo;    
    }
}
