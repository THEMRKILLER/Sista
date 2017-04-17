<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\cita;
class PdfController extends Controller
{
    public function CitasPorMes(Request $request)
    {	
    	
    	
    	$fecha1=$request->get('fecha1');
    	$fecha2=$request->get('fecha2');
    	$data= cita::CitasXLapso($fecha1,$fecha2);
    	$view =  \View::make('PDF.CitasTemplate', compact('data'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        return $pdf->stream('invoice');
    }
}
