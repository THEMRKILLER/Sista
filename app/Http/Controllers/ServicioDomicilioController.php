<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\calendario;
class ServicioDomicilioController extends Controller
{
	public function index(Request $request){
		if(!$request->has('calendario_id')) return response()->json(null,404);
		$calendario_id = $request->get('calendario_id');
		$calendario = calendario::find($calendario_id);
		$servicios_a_domicilio = $calendario->tipos()->where('servicio_domicilio',true)->get();
		return response()->json(['servicios' => $servicios_a_domicilio],200);

	}
    
}
