<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\tipo;
use App\calendario;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use DateTime;
use DatePeriod;
class cita extends Model
{
   	 protected $table = 'cita';
     protected $fillable = [
        'id', 'tipo_id','calendario_id','fecha_inicio', 'fecha_final', 'cliente_nombre','cliente_telefono','cliente_email',
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
          
            $calendario  = calendario::find($arrayDatos['calendario_id']);
            $tipo  = tipo::find($arrayDatos['tipo_id']);
            $NuevaCita = new cita();
            $NuevaCita->fecha_inicio =$arrayDatos['fecha_inicio'];
            $NuevaCita->fecha_final =$arrayDatos['fecha_final'];
            $NuevaCita->cliente_nombre =$arrayDatos['cliente_nombre'];
            $NuevaCita->cliente_telefono =$arrayDatos['cliente_telefono'];
            $NuevaCita->cliente_email =$arrayDatos['cliente_email'];
            $NuevaCita->tipo()->associate($tipo);
        //    $tipo->citas()->save($NuevaCita);
            $calendario->citas()->save($NuevaCita);
        
    	}
      public function eliminar($cita)
    	{
      		$Cita = cita::find($cita['id']);
			if ($Cita === null) 
				{
   					return "cita no existe,verificar bien la codificacion";
				}
				else
					{
						$Cita->destroy();
					}
			
    	}
         /* 
    * @param arrayDatos estructura con nombre,telefono,email
    */
      public function editar($arrayDatos,$id)
    	{
              $Cita = cita::find($id);
        if ($Cita === null) 
        {
            return "cita no existe,no se pudo realizar la modificacion";
        }
        else
          {
            $Cita->cliente_nombre = $arrayDatos['cliente_nombre'];
            $Cita->cliente_telefono =  $arrayDatos['cliente_telefono'];
            $Cita->cliente_email =  $arrayDatos['cliente_email'];
            $Cita->save();
          }
    	}
   /* 
    * @param arrayDatos estructura con fecha y hora 
    */
      public function reagendar($arrayDatos,$id)
    	{
        $Cita = cita::find($id);
        if ($Cita === null) 
        {
            return "cita no existe,no se pudo realizar la modificacion";
        }
        else
          {
            $Cita->fecha_inicio = $arrayDatos['fecha_inicio'];
            $Cita->fecha_final =  $arrayDatos['fecha_final'];
            $Cita->save();
          }
       
      
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
        if ($Cita === null) 
        {
            return "cita no existe,no se pudo realizar la modificacion";
        }
        else
          {
            $Cita->tipo_id = $arrayDatos['tipo_id'];
            $Cita->save();
          }
    	} 
      //usar  carbon para mas consistencia
      public static function dateTimeExist($arrayDatos){
        $di = new DateTime( $arrayDatos['fecha_inicio']);
        $dt = new DateTime( $arrayDatos['fecha_final']);
        $Dates = cita::whereBetween('fecha_inicio', [$di, $dt])->orwhereBetween('fecha_final', [$di, $dt])->first();
        if($Dates==null){
              return true;
        }	else{
          return false;
        }
        }
      public static function freeHours(){
        $di =Carbon::create(2016, 11, 05, 0, 0, 0);
       // $di = new Carbon($arrayDatos['fecha_inicio']);
        $dt=$di->toDateTimeString(); //fecha inicial convertida a string
        $di = $di->addDay()->toDateTimeString(); //añade 1 dia y convierte a string

        $Dates = cita::where('fecha_inicio', '>=',$dt)->where('fecha_final', '<=',$di)->get();
        $events=array();
       foreach ($Dates as $date) {
          $fecha_inicio=$date->fecha_inicio;
          $fecha_final=$date->fecha_final;
          array_push($events, ['fecha_inicio' => $fecha_inicio, 'fecha_final' => $fecha_final]);
       }
       return $events;
        }
        public static function timeslot($arrayDatos){
        $schedule = [
            'start' => '2016-11-05 00:00:00',
            'end' => '2016-11-05 24:00:00',
        ];
        $events= cita::freeHours();

        $start = Carbon::instance(new DateTime($schedule['start']));
        $end = Carbon::instance(new DateTime($schedule['end']));
        $minSlotHours = 2;
        $minSlotMinutes = 0;
        $minInterval = CarbonInterval::hour($minSlotHours)->minutes($minSlotMinutes);

        $reqSlotHours = 2;
        $reqSlotMinutes =0;
        $reqInterval = CarbonInterval::hour($reqSlotHours)->minutes($reqSlotMinutes);

        function slotAvailable($from, $to, $events){
            foreach($events as $event){
                $eventStart = Carbon::instance(new DateTime($event['fecha_inicio']));
                $eventEnd = Carbon::instance(new DateTime($event['fecha_final']));
                if($from->between($eventStart, $eventEnd) && $to->between($eventStart, $eventEnd)){
                    return false;
                }
            }
            return true;
        }
         $disponible=array();
        foreach(new DatePeriod($start, $minInterval, $end) as $slot){
            $to = $slot->copy()->add($reqInterval);
              //parte comentada, de inicio a final del horario disponible
            //echo $slot->toDateTimeString() . ' to ' . $to->toDateTimeString();

            if(slotAvailable($slot, $to, $events)){
             //echo $slot->toDateTimeString(). ' is available';
              array_push($disponible, ['text' => Carbon::parse($slot)->toTimeString(), 'value' => $slot->toDateTimeString()]);
            }

          /// echo '<br />';
        }
        return $disponible;
        }
}
