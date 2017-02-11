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
use App\Mail\NotificacionNCita;

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

    /**
     *crea un nuevo registro en la base de datos de la tabla cita
     *notifica al usuario por sms y email
     * @param datosCita estructura con nombre,telefono,email,fecha y hora de la cita
     */
    public static function crear($datosCita, $codigo)
    {

        $calendario = calendario::find($datosCita['calendario_id']);
        $tipo = tipo::find($datosCita['tipo_id']);
        $nuevaCita = new cita();
        $nuevaCita->fecha_inicio = $datosCita['fecha_inicio'];
        $nuevaCita->fecha_final = Carbon::parse($datosCita['fecha_final'])->subMinute()->toDateTimeString();
        $nuevaCita->cliente_nombre = $datosCita['cliente_nombre'];
        $nuevaCita->cliente_telefono = $datosCita['cliente_telefono'];
        $nuevaCita->cliente_email = $datosCita['cliente_email'];
        $nuevaCita->codigo = $codigo;
        $nuevaCita->costo = $datosCita['costo_total'];
        $nuevaCita->tipo()->associate($tipo);
        $citaGuardada=$calendario->citas()->save($nuevaCita);
        //si la cita es guardada correctamente se manda una notificacion al usuario
        //en caso de que no se manda un codigo de error al cliente
        if ($citaGuardada) {
            /* descomentar esta parte para activar notificaciones
            $medico= $calendario->user->name;
            cita::sms($nuevaCita,$medico);
            cita::mail($nuevaCita, $medico);
            */
             $medico= $calendario->user->name;

         //   cita::mail($nuevaCita, $medico, "agendada");
        } else {
            return response()->json([
                    'error' => true,
                    'message' => 'Ocurrio un error inesperado'
                ], 500);
        }
    }
    /*
    *  busca y elimina la cita por id, si no se encuentra se manda un error
    */
    public static function eliminar($datas)
    {
        $Cita = cita::where('codigo', $datas['codigo'])
                     ->where(function ($query) use ($datas) {
                         /** @var $query Illuminate\Database\Query\Builder  */
                            return $query->where('cliente_telefono', $datas['numeromail'])
                                ->orWhere('cliente_email', $datas['numeromail']);
                     })
                      ->first();
        if ($Cita === null) {
            return response()->json([
                    'errors' => ['Los datos de entrada son incorrectos o la cita ya no existe']
                ], 404);
        } else {
            $Cita->delete();
            return response()->json(['success' => true,'codigo' => $datas['codigo']], 200);
        }
    }

    /**
     * @param datosCita estructura con nombre,telefono,email
     */

    public function editar($datosCita, $id)
    {
        $Cita = cita::find($id);
        if ($Cita === null) {
            return response()->json([
                    'error' => true,
                    'message' => 'No se encuentra una cita con el identificador que selecciono'
                ], 404);
        } else {
            $Cita->cliente_nombre = $datosCita['cliente_nombre'];
            $Cita->cliente_telefono = $datosCita['cliente_telefono'];
            $Cita->cliente_email = $datosCita['cliente_email'];
            $Cita->save();
        }
    }

    /**
     * @param datosCita estructura con fecha y hora
     */

    public static function reagendar($datosCita)
    {
        $Cita = cita::find($datosCita['id_cita']);
        if ($Cita === null) {
            $Cita = cita::find($arrayDatos['id_cita']);
            if ($Cita === null) {
                return response()->json([
                'error' => true,
                'message' => 'La cita no existe'
                ], 404);
            }
        }
        $tipo= tipo::find($datosCita['tipo_id'])->duracion;
        $fecha_final = carbon::parse($datosCita['fecha_inicio'])->addMinutes($tipo);
        $Cita->fecha_inicio = $datosCita['fecha_inicio'];
        $Cita->fecha_final = $fecha_final;
        $Cita->tipo_id = $datosCita['tipo_id'];
        $citaGuardada=$Cita->save();
        //si la cita es guardada correctamente se manda una notificacion al usuario
        //en caso de que no se manda un codigo de error al cliente
        if ($citaGuardada) {
            /* descomentar esta parte para activar notificaciones
            $medico= $calendario->user->name;
            cita::sms($nuevaCita,$medico);
            cita::mail($nuevaCita, $medico);
            */
            /*
            DATOS QUE NECESITA EL CLIENTE : 
            {'id_user' : vm.$store.state.calendario_id,
           'codigo' : response.data.codigo,
           'cliente_nombre' : response.data.cliente_nombre,
           'fecha' : moment(vm.nueva_fecha).format('LLLL'),
           'servicio' : response.data.servicio
            */

          return response()->json([
                                  'codigo' => $Cita->codigo,
                                  'cliente_nombre' => $Cita->cliente_nombre,
                                  'fecha' => $Cita->fecha_inicio,
                                  'servicio' => $Cita->tipo->nombre ],
                                  200);
        } else {
            return response()->json([
                    'error' => true,
                    'message' => 'Ocurrio un error inesperado'
                ], 500);
        }
    }
 /**
  * Function fechaDisponible
  * verifica que no existan registros de una fecha a la misma hora que en la base de datos
  * @param (2 fechas en un arreglo) inicial,final de una cita
  * @return (bool) verdadero cuando la fecha este disponible
  */
    public static function fechaDisponible($datosCita)
    {
        $duracionServicio= tipo::find(intval($datosCita['tipo_id']))->duracion;
        //añade la duracion del servicio a la fecha inicial para calcular la fecha final
        $FechaProcesada=carbon::parse($datosCita['fecha_inicio'])->addMinutes($duracionServicio)->subMinute()->toDateTimeString();
        $fechaInicial = new DateTime($datosCita['fecha_inicio']);
        $fechaFinal = new DateTime($FechaProcesada);
        //se hace una consulta que regresa la cita que esten en el rango de horas propuesto
        $Dates = cita::whereBetween('fecha_inicio', [$fechaInicial, $fechaFinal])->orwhereBetween('fecha_final', [$fechaInicial, $fechaFinal])->first();
        if (count($Dates)<=0) {
            return true;
        } else {
            return false;
        }
    }
 /**
  * Function notInCitas
  * busca si hay una cita en el rango de fechas del parametro
  * @param (datetime[])($rango) fecha inicial,fecha final
  * @return 1 en  caso de que la hora sea libre, fechafinal de la cita cuando exista una en ese rango
 */
       public static function notInCitas($inicial, $final)
       {
           $FechaProcesada=carbon::parse($final)->subMinute()->toDateTimeString();
           $fechaInicial = new DateTime($inicial);
           $fechaFinal = new DateTime($FechaProcesada);
           $Dates = cita::whereBetween('fecha_inicio', [$fechaInicial, $fechaFinal])->orwhereBetween('fecha_final', [$fechaInicial, $fechaFinal])->get();
           if (count($Dates) <= 0) {
               return 1;
           } else {
               return $Dates->first()['fecha_final'];
           }
       }
 /**
  * Function disponibilidadCal
  * revisa las citas del sistema para ver que dias estan mas vacios o llenos
  * @param (int)($tipo_id)
  * @param (int)($calendario_id)
  * @return objeto json con fechas y su disponibilidad alta=1,media=2,baja=3
 */
    public static function disponibilidadCal($tipo_id, $calendario_id)
    {
        $disponibilidad=0;
        $inicial = carbon::now();
        $ocupado=array();
        $Citas=cita::distinct()->select(DB::raw('DATE_FORMAT(fecha_inicio, \'%Y-%m-%d\') AS fecha_inicio'))
            ->where('fecha_inicio', '>=', $inicial->toDateTimeString())
            ->get();
        foreach ($Citas as $fecha) {
            $espacios= cita::timeslot($fecha['fecha_inicio'], $tipo_id, $calendario_id);
            $disponibilidad=cita::espaciosPorFecha(count($espacios));
            array_push($ocupado, ['fecha' => $fecha['fecha_inicio'], 'disponibilidad' => $disponibilidad]);
        }
        return $ocupado;
    }
     /**
  * Function espaciosPorFecha
  *
  * @param (int)($numEspacios) numero de espacios sin agendar en el dia
  * @return int disponibilidad : alta=1,media=2,baja=3
 */
    public static function espaciosPorFecha($numEspacios)
    {
        //disponibilidad baja
         if ($numEspacios>=0 and $numEspacios<=2) {
             return 3;
         }
            //disponibilidad media
         if ($numEspacios>2 and $numEspacios<=5) {
             return 2;
         }
         //disponibilidad alta
         if ($numEspacios>5) {
             return 1;
         }
    }
  /**
   * Function horasDelDia
   * obtiene las horas habiles del dia q
   * @param (Datetime)($fecha)
   * @param (int)($calendario_id)
   * @return arreglo con todas las horas habiles del dia
   */
    public static function horasDelDia($fecha, $calendario_id)
    {
        $calendario = calendario::find($calendario_id);
        $dia=carbon::parse($fecha)->dayOfWeek;
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
  /**
   * Function filtroHorasInhabiles
   * obtiene las horas inhabiles del dia
   * @param (Datetime)($fecha)
   * @param (int)($calendario_id)
   * @return arreglo con todas las horas habiles del dia
   */
    public static function filtroHorasInhabiles($fecha, $calendario_id)
    {
        $calendario = calendario::find($calendario_id);
        $diaInhabil=$calendario->fechasInhabiles()->where('fecha', $fecha)->first();
        $horasInhabiles =array();
        if ($diaInhabil!=null) {
            if ($diaInhabil->completo==1) {
                //todo el dia es inhabil regresa un arreglo con todas las horas
            return range(0, 23);
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
  /**
   * Function rellenarHoras
   * obtiene las horas inhabiles del dia
   * @param (Datetime)($fecha)
   * @param (int)($horas_filtrado)
   * @param (int)($duracion_servicio)
   * @param (Datetime)($hora_inicial)
   * @param (Datetime)($horas_propuestas)
   * @param (int)($hora_final_dia)
   * @return arreglo con los huecos libres del dia
   */
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
        if ($cita != 1) {
            return cita::rellenarHoras($fecha, $horas_filtrado, $d_s, carbon::parse($cita)->addMinute()->toDateTimeString(), $horas_propuestas, $hora_final_dia);
        } else {
            $hora_inicial_next = cita::nextDisponible($hora_inicial, $horas_filtrado);
            if ($hora_inicial_next == $hora_inicial) {
                array_push($horas_propuestas, $hora_inicial);
                $hora_final_tmp = carbon::parse($hora_inicial_next)->addMinutes($duracion_servicio)->toDateTimeString();
                return cita::rellenarHoras($fecha, $horas_filtrado, $d_s, $hora_final_tmp, $horas_propuestas, $h_f_d);
            } else {
                return cita::rellenarHoras($fecha, $horas_filtrado, $d_s, $hora_inicial_next, $horas_propuestas, $h_f_d);
            }
        }
    }

  /**
   * Function hourtoDateTime
   * convierte enteros a su equivalente en datetime, solo acepta rangos de 0 a 23
   * @param (Datetime)($fecha)
   * @param ([])(int)($horas_habiles)
   * @return arreglo con todas las horas habiles del dia convertido a datetime
   */
    public static function hourtoDateTime($fecha, $horas_habiles)
    {
        $datehours=array();
        foreach ($horas_habiles as $hora) {
            if ($hora >=0&&$hora<=23) {
                $hour = new Carbon($fecha);
                $hour->hour = $hora;
                $hour->minute = 0;
                $time=$hour->toDateTimeString();
                array_push($datehours, $time);
            } else {
                return response()->json([
                    'error' => true,
                    'message' => 'el rango de horas habiles esta fuera del rango de 0 a 23'
                ], 500);
            }
        }
        return $datehours;
    }

  /**
   * Function nextDisponible
   * busca la siguiente hora disponible en el arreglo de horas habiles
   * @param (Datetime)($hora)
   * @param ([])(int)($horas_filtrado)
   * @return arreglo con la siguiente hora disponible
   */
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

  /**
   * Function timeslot
   * busca los huecos libres del dia
   * @param (Datetime)($fecha)
   * @param ([])(int)($tipo_id)
   * @param ([])(int)($calendario_id)
   * @return arreglo con todos los huecos libres del dia
   */
    public static function timeslot($fecha, $tipo_id, $calendario_id)
    {
        //se declara arreglo para llenarlo mas adelante
        $horas_propuestas = array();
        $duracion_servicio= tipo::find($tipo_id)->duracion;
        $horas_habiles = cita::horasDelDia($fecha, $calendario_id);
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
  /**
   * Function ConversionArray
   * transforma un arreglo a estructura json con texto y valores
   * @param (Datetime)($fecha)
   * @param ([])($array)
   * @return json
   */
    public static function ConversionArray($array)
    {
        $arreglo=array();
        foreach ($array as $key) {
            array_push($arreglo, ['text' => Carbon::parse($key)->toTimeString(), 'value' => $key]);
        }
        return $arreglo;
    }
  /**
   * Function diasNoHabiles
   * busca en la base de datos los dias que no estan habilitados
   * @param (int)($calendario_id)
   * @return arreglo con dias no habiles
   */
    public static function diasNoHabiles($calendario_id)
    {
        $calendario = calendario::find($calendario_id);
        $diasHabiles=$calendario->diasHabiles()->get();
        $diasNohabiles= array();
        $dias_semana= [1,2,3,4,5,6,7];
               
        foreach ($diasHabiles as $dia) {
            $dia_id= $dia->horasHabiles()->distinct()->select('diahabil_id')->get();
            if (count($dia_id)>0) {
                array_push($diasNohabiles, $dia_id->first()->diahabil_id);
            }
        }
            
        return array_values(array_diff($dias_semana, $diasNohabiles));
    }


      /**
   * Function filtroHorasInhabiles
   * obtiene las horas inhabiles del dia
   * @param (Datetime)($fecha)
   * @param (int)($calendario_id)
   * @return arreglo con todas las horas habiles del dia
   */
    public static function revisarDiasInhabiles($datosCita)
    {
        $calendario = calendario::find($datosCita['calendario_id']);
        $diasInhabiles=$calendario->fechasInhabiles()->pluck('fecha')->toArray();
        $fechaAgendar = $fecha= carbon::parse($datosCita['fecha_inicio']);
        //se hace una consulta que regresa la cita que esten en el rango de horas propuesto
        //saco el numero de elementos
        $longitud = count($diasInhabiles);
        
        //Recorro todos los elementos
        if ($longitud>0) {
            for ($i=0; $i<$longitud; $i++) {
                $inicioDiaInhabil=carbon::parse($diasInhabiles[$i]);
                $finDiaInhabil=carbon::parse($diasInhabiles[$i])->addDay()->subSecond();
                $disponibilidad= $fechaAgendar->between($inicioDiaInhabil, $finDiaInhabil);
                if ($disponibilidad) {
                    return false;
                }
            }
        } else {
            return true;
        }
        return true;
    }
  /**
   * Function sms
   * manda un sms al agendar/reagendar una cita
   * @param (cita)($cita)
   * @param (String)($medico) nombre del medico
   * @param (String)($opcionMensaje)
   */
    public static function sms($cita, $medico, $opcionMensaje)
    {
        $Codigo_Area ='52';
        $telefono=$Codigo_Area.'9612973079';//$cita->cliente_telefono;
        $from='16105552344';
        $mensaje='El medico'.$medico.'le indica a '.$cita->cliente_nombre.'que su cita ah sido'.$opcionMensaje.'el'.$cita->fecha_inicio;
        $nexmo = app('Nexmo\Client');
        $nexmo->message()->send([
            'to' => $telefono,
            'from' => $from,
            'text' => $mensaje
]);
    }
  /**
   * Function mail
   * manda un e-mail al agendar/reagendar una cita
   * @param (cita)($cita)
   * @param (String)($medico) nombre del medico
   * @param (String)($opcionMensaje)
   */
    public static function mail($cita, $medico, $opcionMensaje)
    {
        $destinatario=$cita->cliente_email;
        \Mail::to($destinatario)->send(new NotificacionNCita($cita, $medico, $opcionMensaje));
    }
}
