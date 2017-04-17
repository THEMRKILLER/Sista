<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\cita;
class PdfController extends Controller
{
    public function CitasPorMes()
    {	
    	$fecha1='2017-04-00';
    	$fecha2='2017-12-00';
    	$data= cita::CitasXLapso($fecha1,$fecha2);
    	$view =  \View::make('PDF.CitasTemplate', compact('data'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        return $pdf->stream('invoice');
    }
}
