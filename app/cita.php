<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\tipo;
use App\calendario;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use DateTime;
use DatePeriod;
use DB;

class cita extends Model
{
    use Notifiable;
    protected $table = 'cita';
    protected $fillable = [
        'id', 'tipo_id', 'calendario_id', 'fecha_inicio', 'fecha_final', 'cliente_nombre', 'cliente_telefono', 'cliente_email',
    ];

    //relaciones

    public function calendario()
    {
        return $this->belongsTo('App\calendario');
    }

    public function tipo()
    {
        return $this->belongsTo('App\tipo');
    }

    //metodos de clase

    /*
     * @param arrayDatos estructura con nombre,telefono,email,fecha y hora de la cita
     */
    public static function crear($arrayDatos)
    {
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

    public function eliminar($cita)
    {
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

    public function editar($arrayDatos, $id)
    {
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

    public function reagendar($arrayDatos)
    {
        $Cita = cita::find($id);
        if ($Cita === null) return response()->json(['error'=>true,'message' = > 'La cita no existe'],404);
        
        $tipo= $Cita->tipo()->duracion;
        $fecha_final = carbon::parse($arrayDatos['fecha_inicial'])->addMinutes($tipo);
        $Cita->fecha_inicio = $arrayDatos['fecha_inicio'];
        $Cita->fecha_final = $fecha_final;
        $Cita->tipo_id = $arrayDatos['id_servicio'];
        $Cita->save();
        
    }
    public function asignarTipo()
    {
    }
    /*
     * @param Dato variable numerica que hace relacion a un id de la tabla Tipo
     */
    public function cambiarTipo($arrayDatos)
    {
        $Cita = cita::find($arrayDatos['id']);
        if ($Cita === null) {
            return "cita no existe,no se pudo realizar la modificacion";
        } else {
            $Cita->tipo_id = $arrayDatos['tipo_id'];
            $Cita->save();
        }
    }
    /*
 * Function dateTimeExist
 *
 * verifica que no existan registros de una fecha a la misma hora que en la base de datos
 *
 * @param (2 fechas en un arreglo) inicial,final de una cita
 * @return (bool) verdadero en caso de que no exista ninguna cita a esa hora
 */
    public static function dateTimeExist($arrayDatos)
    {
        $finalt=carbon::parse($arrayDatos['fecha_final'])->subMinute()->toDateTimeString();
        $di = new DateTime($arrayDatos['fecha_inicio']);
        $dt = new DateTime($finalt);
        $Dates = cita::whereBetween('fecha_inicio', [$di, $dt])->orwhereBetween('fecha_final', [$di, $dt])->first();
        if ($Dates == null) {
            return true;
        } else {
            return false;
        }
    }

     /*
 * Function citasdeldia
 *
 * verifica que no existan registros de una fecha a la misma hora que en la base de datos
 *
 * @param (datetime)($dt) fecha inicial
 * @param (datetime)($di) fecha final
 * @return ([])($events) contiene todos los eventos registrados tomando los parametros como rango
 */
    public static function citasdeldia($dt, $di)
    {
        $Dates = cita::where('fecha_inicio', '>=', $dt)->where('fecha_final', '<=', $di)->get();
        $events = array();
        foreach ($Dates as $date) {
            $fecha_inicio = $date->fecha_inicio;
            $fecha_final = $date->fecha_final;
            array_push($events, ['fecha_inicio' => $fecha_inicio, 'fecha_final' => $fecha_final]);
        }
        return $events;
    }

        /*
 * Function HorasLibres
 *
 *  verifica que el rango de fechas este dentro de las horas habilies disponibles
 *
 * @param (datetime[])($rango) fecha inicial,fecha final
 * @param (datetime[])($horas) horas disponibles del dia
 * @return (bool) verdadero cuando el rango esta entre las horas disponibles
 */
    public static function HorasLibres($rango, $horas)
    {
        $inicial = carbon::parse($rango['inicial']);
        $final = carbon::parse($rango['final'])->subsecond();
        for ($i=0; $i <count($horas) ; $i++) {
            $hora_habil1 =carbon::parse($horas[$i]);
            $hora_habil2 =carbon::parse($horas[$i])->addMinutes(59)->addSeconds(59);
            if ($inicial->between($hora_habil1, $hora_habil2) and $final->between($hora_habil1, $hora_habil2)) {
                return true;
            } else {
            }
        }
        return false;
    }
       /*
 * Function notInCitas
 *
 * busca si hay una cita en el rango de fechas del parametro
 *
 * @param (datetime[])($rango) fecha inicial,fecha final
 * @return 1 en  caso de que la hora sea libre, fechafinal de la cita cuando exista una en ese rango
 */
       public static function notInCitas($inicial, $final)
       {
           $finalt=carbon::parse($final)->subMinute()->toDateTimeString();
           $di = new DateTime($inicial);
           $dt = new DateTime($finalt);
           $Dates = cita::whereBetween('fecha_inicio', [$di, $dt])->orwhereBetween('fecha_final', [$di, $dt])->get();
           if (count($Dates) <= 0) {
               return 1;
           } else {
               return $Dates->first()['fecha_final'];
           }
       }

    public static function disponibilidadCal($tipo_id, $calendario_id)
    {
        $disponibilidad=0;
        $inicial = carbon::now();
        
        $Citas=cita::distinct()->select(DB::raw('DATE_FORMAT(fecha_inicio, \'%Y-%m-%d\') AS fecha_inicio'))
            ->where('fecha_inicio', '>=', $inicial->toDateTimeString())
            ->get();
        $ocupado=array();
        foreach ($Citas as $fecha) {
            $espacios= cita::timeslot($fecha['fecha_inicio'], $tipo_id, $calendario_id);
           //disponibilidad baja
         if (count($espacios)>=0 and count($espacios)<=2) {
             $disponibilidad=3;
         }
            //disponibilidad media
         if (count($espacios)>2 and count($espacios)<=5) {
             $disponibilidad=2;
         }
         //disponibilidad alta
         if (count($espacios)>5) {
             $disponibilidad=1;
         }
            array_push($ocupado, ['fecha' => $fecha['fecha_inicio'], 'disponibilidad' => $disponibilidad]);
        }
        return $ocupado;
    }
    public static function filtrarHoras($fecha, $calendario_id)
    {
        $dia=carbon::parse($fecha)->dayOfWeek;
        $calendario = calendario::find($calendario_id);
        $diasHabiles=$calendario->diasHabiles()->where('dia', $dia)->first();
        $horasHabiles = array();
        if ($diasHabiles!=null) {
            $horas=$diasHabiles->horasHabiles;
            foreach ($horas as $hora) {
                array_push($horasHabiles, $hora['hora']);
            }
        } else {
        }
        return $horasHabiles;
    }
    public static function filtroHorasInhabiles($fecha, $calendario_id)
    {
        $calendario = calendario::find($calendario_id);
        $diaInhabil=$calendario->fechasInhabiles()->where('fecha', $fecha)->first();
        $horasInhabiles =array();
        if ($diaInhabil!=null) {
            if ($diaInhabil->completo==1) {
                //todo el dia es inhabil
            return array();
            } else {
                //regresa un arreglo con las horas inhabiles del dia
          $horas=$diaInhabil->horasInhabiles()->get(['hora'])->toArray();
                foreach ($horas as $hora) {
                    array_push($horasInhabiles, $hora['hora']);
                }
                return $horasInhabiles;
            }
        } else {
        }
        return array();
    }
    public static function rellenarHoras($fecha, $horas_filtrado, $duracion_servicio, $hora_inicial, $horas_propuestas, $hora_final_dia)
    {
        $h_p = $horas_propuestas;
        //validacion extra
        //$lastMinute=carbon::parse($hora_final_dia)->addHour()->subSecond()->toDateTimeString();
        //$hfd=carbon::parse($hora_inicial)->addMinutes($duracion_servicio)->toDateTimeString();
        // && $hfd>=$lastMinute
        if ($hora_inicial >= $hora_final_dia) {
            return $h_p;
        }
        $d_s = $duracion_servicio;
        $h_f_d  = $hora_final_dia;
        $hora_final = carbon::parse($hora_inicial)->addMinutes($duracion_servicio)->toDateTimeString();
        
        $cita = cita::notInCitas($hora_inicial, $hora_final);
        //echo "cita ".$cita."\n";
        if ($cita != 1) {
            //cita sumar 1 segundo aca ?
            return cita::rellenarHoras($fecha, $horas_filtrado, $d_s, carbon::parse($cita)->addMinute()->toDateTimeString(), $horas_propuestas, $hora_final_dia);
        } else {
            //echo "hora inicial :".$hora_inicial."\n";
            $hora_inicial_next = cita::nextDisponible($hora_inicial, $horas_filtrado);
            //echo "hora inicial next :".$hora_inicial_next."\n";
            if ($hora_inicial_next == $hora_inicial) {
                array_push($horas_propuestas, $hora_inicial);

                $hora_final_tmp = carbon::parse($hora_inicial_next)->addMinutes($duracion_servicio)->toDateTimeString();
                //echo "duracion_servicio".$duracion_servicio;
                //echo "\n";
                //echo "Next devolvio : ".$hora_inicial_next;
                //echo "\n";
                //echo "Se hizo push de : ".$hora_inicial;
                //echo "Se calcula ahora : ".$hora_final_tmp;
                //echo "\n";
                //echo "hora final dia: ".$h_f_d;
                return cita::rellenarHoras($fecha, $horas_filtrado, $d_s, $hora_final_tmp, $horas_propuestas, $h_f_d);
            } else {
                return cita::rellenarHoras($fecha, $horas_filtrado, $d_s, $hora_inicial_next, $horas_propuestas, $h_f_d);
            }
        }
    }

    public static function hourtoDateTime($fecha, $horas_habiles)
    {
        $datehours=array();
        foreach ($horas_habiles as $hora) {
            $hour = new Carbon($fecha);
            $hour->hour = $hora;
            $hour->minute = 0;
            $time=$hour->toDateTimeString();
            array_push($datehours, $time);
        }
        return $datehours;
    }
    public static function nextDisponible($hora, $horas_filtrado)
    {
        $temp_h=carbon::parse($hora);
        $y=$temp_h->year;
        $m=$temp_h->month;
        $d=$temp_h->day;
        $h=$temp_h->hour;
        $_h = Carbon::create($y, $m, $d, $h, 0, 0);
        if (end($horas_filtrado) < $_h) {
            return $hora;
        }
        $flag = false;
        foreach ($horas_filtrado as $h_f) {
            if ($h_f == $_h) {
                $flag = true;
                return $hora;
            }
        }
        if ($flag == false) {
            return cita::nextDisponible(carbon::parse($hora)->addHour()->toDateTimeString(), $horas_filtrado);
        }
    }
    public static function timeslot($fecha, $tipo_id, $calendario_id)
    {
        // $fecha = '2016-11-05 0:00:00';
       // $duracion_servicio = 30;
        //se declara arreglo para llenarlo mas adelante
        $horas_propuestas = array();
        $duracion_servicio= tipo::find($tipo_id)->duracion;
        
        $horas_habiles = cita::filtrarHoras($fecha, $calendario_id);
        $horas_inhabiles = cita::filtroHorasInhabiles($fecha, $calendario_id);
        //el producto final, despues de haber pasado por todos los filtros
        $horas_filtrado=array_diff($horas_habiles, $horas_inhabiles);
       
        if ($horas_filtrado==null) {
            return array();
        } else {
            $horas_filtrado=cita::hourtoDateTime($fecha, $horas_filtrado);
            $hora_inicial = reset($horas_filtrado);
            $hora_final_dia = end($horas_filtrado);
            $horas_propuestas = cita::rellenarHoras($fecha, $horas_filtrado, $duracion_servicio, $hora_inicial, $horas_propuestas, $hora_final_dia);
           
            return cita::ConversionArray($horas_propuestas);
        }
    }
    public static function ConversionArray($array)
    {
        $arreglo=array();
        foreach ($array as $key) {
            array_push($arreglo, ['text' => Carbon::parse($key)->toTimeString(), 'value' => $key]);
        }
        return $arreglo;
    }
    public static function diasNoHabiles($calendario_id)
    {
        $calendario = calendario::find($calendario_id);
        $diasHabiles=$calendario->diasHabiles()->get();
        $diasNohabiles= array();
        $dias_semana= [1,2,3,4,5,6,7];
               // dd($diasHabiles);
                foreach ($diasHabiles as $dia) {
                    $dia_id= $dia->horasHabiles()->distinct()->select('diahabil_id')->get(); 
                    if ( count($dia_id)>0) {                
                    array_push($diasNohabiles, $dia_id->first()->diahabil_id);
                    }
                }
            
       return array_values( array_diff($dias_semana, $diasNohabiles));
      
    }
    public static function enviarcorreo(cita $cita)
    {
        
          
                $cita->notify($cita);
    }
}
