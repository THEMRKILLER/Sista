<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\tipo;
use App\calendario;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use DateTime;
use DatePeriod;

class cita extends Model {

    protected $table = 'cita';
    protected $fillable = [
        'id', 'tipo_id', 'calendario_id', 'fecha_inicio', 'fecha_final', 'cliente_nombre', 'cliente_telefono', 'cliente_email',
    ];

    //relaciones

    public function calendario() {
        return $this->belongsTo('App\calendario');
    }

    public function tipo() {
        return $this->belongsTo('App\tipo');
    }

    //metodos de clase

    /*
     * @param arrayDatos estructura con nombre,telefono,email,fecha y hora de la cita
     */
    public static function crear($arrayDatos) {

        $calendario = calendario::find($arrayDatos['calendario_id']);
        $tipo = tipo::find($arrayDatos['tipo_id']);
        $NuevaCita = new cita();
        $NuevaCita->fecha_inicio = $arrayDatos['fecha_inicio'];
        $NuevaCita->fecha_final = Carbon::parse($arrayDatos['fecha_final'])->subMinute()->toDateTimeString();
        $NuevaCita->cliente_nombre = $arrayDatos['cliente_nombre'];
        $NuevaCita->cliente_telefono = $arrayDatos['cliente_telefono'];
        $NuevaCita->cliente_email = $arrayDatos['cliente_email'];
        $NuevaCita->tipo()->associate($tipo);
        //    $tipo->citas()->save($NuevaCita);
        $calendario->citas()->save($NuevaCita);
    }

    public function eliminar($cita) {
        $Cita = cita::find($cita['id']);
        if ($Cita === null) {
            return "cita no existe,verificar bien la codificacion";
        } else {
            $Cita->destroy();
        }
    }

    /*
     * @param arrayDatos estructura con nombre,telefono,email
     */

    public function editar($arrayDatos, $id) {
        $Cita = cita::find($id);
        if ($Cita === null) {
            return "cita no existe,no se pudo realizar la modificacion";
        } else {
            $Cita->cliente_nombre = $arrayDatos['cliente_nombre'];
            $Cita->cliente_telefono = $arrayDatos['cliente_telefono'];
            $Cita->cliente_email = $arrayDatos['cliente_email'];
            $Cita->save();
        }
    }

    /*
     * @param arrayDatos estructura con fecha y hora 
     */

    public function reagendar($arrayDatos) {
        $Cita = cita::find($id);
       $tipo=$Cita->tipo()->duracion;
       
       $fecha_final = carbon::parse($arrayDatos['fecha_final'])->addMinutes($tipo);
        if ($Cita === null) {
            return "cita no existe,no se pudo realizar la modificacion";
        } else {
            $Cita->fecha_inicio = $arrayDatos['fecha_inicio'];
            $Cita->fecha_final = $fecha_final;
            $Cita->tipo_id = $arrayDatos['id_servicio'];
            $Cita->save();
        }
    }

    public function asignarTipo() {
        
    }

    /*
     * @param Dato variable numerica que hace relacion a un id de la tabla Tipo
     */

    public function cambiarTipo($arrayDatos) {
        $Cita = cita::find($arrayDatos['id']);

        if ($Cita === null) {
            return "cita no existe,no se pudo realizar la modificacion";
        } else {
            $Cita->tipo_id = $arrayDatos['tipo_id'];
            $Cita->save();
        }
    }

    //usar  carbon para mas consistencia
    public static function dateTimeExist($arrayDatos) {
        $di = new DateTime($arrayDatos['fecha_inicio']);
        $dt = new DateTime($arrayDatos['fecha_final']);
        $Dates = cita::whereBetween('fecha_inicio', [$di, $dt])->orwhereBetween('fecha_final', [$di, $dt])->first();
        if ($Dates == null) {
            return true;
        } else {
            return false;
        }
    }

    public static function citasdeldia($dt, $di) {
        $Dates = cita::where('fecha_inicio', '>=', $dt)->where('fecha_final', '<=', $di)->get();
        $events = array();
        foreach ($Dates as $date) {
            $fecha_inicio = $date->fecha_inicio;
            $fecha_final = $date->fecha_final;
            array_push($events, ['fecha_inicio' => $fecha_inicio, 'fecha_final' => $fecha_final]);
        }
        return $events;
    }

    public static function timeslota($arrayDatos) {
        // var_dump($arrayDatos);
        //$DuracionMins = tipo::find($arrayDatos['tipo_id'])->duracion;
        //$di =new carbon($arrayDatos['dia']);  
        //$dt=$di->toDateTimeString(); //fecha inicial convertida a string
        //$di = $di->addDay()->toDateTimeString(); //añade 1 dia y convierte a string
        //$schedule = [
        //  'start' => $dt,
        //'end' => $di,
        //];
        $schedule = [
            'start' => '2016-11-05 09:00:00',
            'end' => '2016-11-05 17:00:00',
        ];
        $events = cita::freeHours($schedule['start'], $schedule['end']);
        var_dump($events);
        $start = Carbon::instance(new DateTime($schedule['start']));
        $end = Carbon::instance(new DateTime($schedule['end']));
        //$minSlotHours = intval($DuracionMins/60);
        //$minSlotMinutes = $DuracionMins%60;
        $minSlotHours = 1;
        $minSlotMinutes = 0;
        $minInterval = CarbonInterval::hour($minSlotHours)->minutes($minSlotMinutes);
        $reqSlotHours = 1;
        $reqSlotMinutes = 0;
        $reqInterval = CarbonInterval::hour($reqSlotHours)->minutes($reqSlotMinutes);

        function slotAvailable($from, $to, $events) {
            foreach ($events as $event) {
                $eventStart = Carbon::instance(new DateTime($event['fecha_inicio']));
                $eventEnd = Carbon::instance(new DateTime($event['fecha_final']));
                if ($from->between($eventStart, $eventEnd) && $to->between($eventStart, $eventEnd)) {
                    return false;
                }
            }
            return true;
        }

        $disponible = array();
        foreach (new DatePeriod($start, $minInterval, $end) as $slot) {
            $to = $slot->copy()->add($reqInterval);
            //parte comentada, de inicio a final del horario disponible
            //echo $slot->toDateTimeString() . ' to ' . $to->toDateTimeString();

            if (slotAvailable($slot, $to, $events)) {
                // echo $slot->toDateTimeString(). ' is available';
                array_push($disponible, ['text' => Carbon::parse($slot)->toTimeString(), 'value' => $slot->toDateTimeString()]);
            }

            /// echo '<br />';
        }
        //var_dump($disponible);
        return $disponible;
    }

    public static function timeslot() {

        /*
          |--------------------------------------------------------------------------
          | real
          |--------------------------------------------------------------------------
          $fecha=$arrayDatos['dia'];
          $duracion_servicio= tipo::find($arrayDatos['tipo_id'])->duracion;
          |
         */

        /*
          |--------------------------------------------------------------------------
          | Datos de prueba
          |--------------------------------------------------------------------------
          |

         */
        $fecha = '2016-11-05 0:00:00';
        $duracion_servicio = 30;
        $disponible = array();

        $fechaf = carbon::parse($fecha)->addDay()->toDateTimeString();
        $citas = cita::citasdeldia($fecha, $fechaf);

        $horas_habiles = [ 11,13];

        ///filtro de horas disponibles
        function inTime($rango, $horas) {
            $inicial = carbon::parse($rango['inicial']);
            $final = carbon::parse($rango['final'])->subsecond();
            for ($i=0; $i <count($horas) ; $i++) { 
               $hora_habil1 =carbon::parse($horas[$i]);
                $hora_habil2 =carbon::parse($horas[$i])->addMinutes(59)->addSeconds(59);
                            if ($inicial->between($hora_habil1, $hora_habil2) and $final->between($hora_habil1, $hora_habil2)) {
                   
                  return true;
               
             }else{


             }
             
            }
           
            // print_r($inicial." esta entre ".$hora_habil1." y ".$hora_habil2."    ");
            return false;
        }

        ///filtro de citas 
        function notInCitas($rango) {
            $di = new DateTime($rango['inicial']);
            $dt = new DateTime($rango['final']);
            $Dates = cita::whereBetween('fecha_inicio', [$di, $dt])->orwhereBetween('fecha_final', [$di, $dt])->get();
            if (count($Dates) <= 0) {
                return 1;
            } else {

                return $Dates->first()['fecha_final'];
            }
        }

        $datehours = array();
        foreach ($horas_habiles as $hora) {
            $hour = new Carbon($fecha);
            $hour->hour = $hora;
            $hour->minute = 0;
            $time=$hour->toDateTimeString();
            array_push($datehours, $time);
        }
        $hora_disponible_inicial = $datehours[0];
        $horas_disponibles = array();
         $fin_dia= carbon::parse(end($datehours))->addHour();
        while ($hora_disponible_inicial <$fin_dia) {
            $hora_propuesta_inicial = $hora_disponible_inicial;
            $hora_propuesta_final = carbon::parse($hora_disponible_inicial)->addMinutes($duracion_servicio)->toDateTimeString();
            $rango['inicial'] = $hora_propuesta_inicial;
            $rango['final'] = $hora_propuesta_final;

            $hora_disponible_inicial = carbon::parse($hora_disponible_inicial)->addMinutes($duracion_servicio)->toDateTimeString();
            if(inTime($rango,$datehours)){
                          $val = notInCitas($rango);
            if ($val == 1) {

                array_push($horas_disponibles, $hora_propuesta_inicial);
                array_push($disponible, ['text' => Carbon::parse($hora_propuesta_inicial)->toTimeString(), 'value' => $hora_propuesta_inicial]);
            } else {
                ///ver si hay una cita a la hora final de la cita pasada

                $n_rango['inicial'] = carbon::parse($val)->toDateTimeString();
                $n_rango['final'] = carbon::parse($val)->addMinutes($duracion_servicio)->toDateTimeString();
                $val2 = notInCitas($n_rango);
                if ($val2 == 1) {


                    //  array_push($horas_disponibles, $val2);
                    $hora_disponible_inicial = $n_rango['inicial'];
                }
            }
            }else{
               
            }
 


            //  dd(notInCitas($rango));
            // dd($hora_disponible_inicial,$hora_propuesta_final);
        }
        dd($disponible);
        return $disponible;
    }
    public static function prueba() {
            $inicial = carbon::parse('2016-11-05 11:00:00');
            $final = carbon::parse('2016-11-05 11:30:00');                
                $hora_habil1 =carbon::parse('2016-11-05 11:00:00');
                $hora_habil2 =carbon::parse('2016-11-05 11:00:00')->addMinutes(59)->addSeconds(59);
            if ($inicial->between($hora_habil1, $hora_habil2) and $final->between($hora_habil1, $hora_habil2)) {
                   
                  return true;
               
             }else{


             }
            
            // print_r($inicial." esta entre ".$hora_habil1." y ".$hora_habil2."    ");
            return false;
        }

}
