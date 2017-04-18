<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\cita;
use JWTAuth;
use Carbon\Carbon;
class PdfController extends Controller
{
    public function CitasPorMes(Request $request)
    {	
    	
    	
    	$fecha1=$request->get('fecha1');
    	$fecha2=$request->get('fecha2');
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
    	$data= cita::CitasXLapso($fecha1,$fecha2,$user->calendario);
        $total = 0;
        foreach($data as $cita) $total+=$cita->costo;
        setlocale(LC_TIME, config('app.locale'));
        Carbon::setLocale(config('app.locale'));
        $carbon = new Carbon($fecha1);
        $fecha1 = $carbon->formatLocalized('%A %d %B %Y');
        $carbon = new Carbon($fecha2);
        $fecha2 = $carbon->formatLocalized('%A %d %B %Y');
        $view = view('pdf_plantilla.CitasTemplate')->with('citas',$data)
                                                    ->with('total',$total)
                                                    ->with('inicio',utf8_encode($fecha1))
                                                    ->with('final',utf8_encode($fecha2))
                                                    ->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
       // return $pdf->stream();
       return $pdf->download('informe_'.$fecha1.'-'.$fecha2.'.pdf');
    }
}
