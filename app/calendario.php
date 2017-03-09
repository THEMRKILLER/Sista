<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\dia_habil;
use App\hora_habil;
use App\User;
use Exeption;
class calendario extends Model
{
	 protected $table = 'calendario';
     protected $fillable = [
        'user_id','id'
        ];
        //relaciones
     public function tipos()
     {
        return $this->hasMany('App\tipo');
     }
     public function user()
     {
        return $this->belongsTo('App\User');
     }
     public function Admin()
    	{
       return $this->hasOne('App\Admin');
    	}

     public function fechasInhabiles()
    	{
        return $this->hasMany('App\fecha_inhabil');
    	}

     public function citas()
    	{
        return $this->hasMany('App\cita');
    	}

     public function diasHabiles()
    	{
        return $this->hasMany('App\dia_habil');
    	}
      public function cupones()
      {
        return $this->hasManyThrough('App\Cupon', 'App\tipo','calendario_id','servicio_id','id');
      }
    	//metodos de clase
      public function crear()
    	{
      
    	}
      public function eliminar()
    	{
      
    	}
      public function editar()
    	{
      
    	}

      /**
          * Function asignar_horario
          * crea o actualiza los el horario del calendario del calendario
          * @param (string[*]) $dias_habiles
          * ejemplo de arreglo dias_habiles ['dia' => '1','laboral' => true,'horas' => [['hora' => 1,'disponible' => true],['hora' => 2,'disponible' => false , ...] ] ]
          * las horas siempre tienen que venir las 7 del cliente aun que no haya modificado todas,
          * lo único que cambia es su valor booleano que lo acompaña en dicho día, e indica si el día está disponible o no disponible (laboral|nolaboral)
          * @return json con código de estado 200 cuando el proceso se llevó acabo de manera exitosa
    */
      public function asignar_horario($dias_habiles)
    	{
          if(! $this->verificarHoras($dias_habiles))         
            return response()->json(['errors' => ['horas_no_especificadas' => ['Las horas de algún día habil no se especificaron']]],404);

        foreach ($dias_habiles as $dia_habil) {
                
                $dia_habil_model = dia_habil::firstOrNew(['dia' => $dia_habil['dia'],'calendario_id' => $this->id]);
                $this->diasHabiles()->save($dia_habil_model);
                $dia_habil_model->save();
                if($dia_habil['laboral'])$dia_habil_model->asignar_horas($dia_habil['horas']);
                else $dia_habil_model->asignar_horas(null);
                
        }
      
      return response()->json(null,200);
    	}

         /**
          * Function verificarHoras
          * Verifica si un día contiene las horas habilitadas
          * @param (string[*]) $dias_habiles Json que especifica el día y las horas validas
          * @return Boolean, True si no existió ningún problema y false cuando si. 
    */
      public function verificarHoras($dias_habiles)
      {
        try{
        foreach ($dias_habiles as $dia_habil) {
                
             if($dia_habil['laboral']) {
              if( $dia_habil['horas'] == [] ||  $dia_habil['horas'] == null){
                throw new Exception("No se especificaron las horas", 1);
              }
             }
        }
        return true;
        }
        catch(Exeption $e)
        {
          return false;
        }
      }

         /**
          * Function inhabilitar_fecha
          * Guarda una fecha invalida en el calendario para que no se puedan agendar citas para ese día
          * @param (string[*]) $fechas Json que especifica el día y las horas invalidas en caso que se
          * desactive todo el día (marcado como completo)
          * ejemplo : ['fecha' => '2017-02-01', 'completo' => false, 'horas' => [1,2,3,4]]
          * @return json con código de estado 200 cuando el proceso se llevó acabo de manera exitosa
    */
      public function inhabilitar_fecha($fechas)
    	{
            
            if($fechas['fecha'] == null || $fechas['fecha'] == [] )
              return response()->json(['errors' => ['fechas_not_found' => 'No se especificaron fechas']],404);
            
            if(!$this->verificarHorasValidasdeFechasInhabiles($fechas))
              return response()->json(['errors' => ['horas_not_found' => ['No se especifico las horas de la fecha invalida']]],404);
            
            foreach ($fechas as $fecha) {
                    $fecha_inhabilitada = new fecha_inhabil();
                    $fecha_inhabilitada->fecha = $fecha['fecha'];
                    $fecha_inhabilitada->completo = $fecha['completo'];
                    $this->fechasInhabiles()->save($fecha_inhabilitada);
                    if(!$fecha['completo'])
                    {
                        foreach ($fecha['horas'] as $hora) {
                            $hora_inhabil = new fechahora_inhabil();
                            $hora_inhabil->hora = $hora;
                            $fecha_inhabilitada->horasInhabiles()->save($hora_inhabil);

                        }
                    }
            }

            return response()->json(null,200);
    	}


        /**
          * Function verificarHorasValidasdeFechasInhabiles
          * Verifica que si una fecha está marcada como no completo deba contener las horas habilitadas
          * @param (string[*]) $fechas Json que especifica el día y las horas invalidas 
          * @return Boolean, True si no existió ningún problema y false cuando si. 
    */
      public function verificarHorasValidasdeFechasInhabiles($fechas)
      {
        try
        {
          foreach ($fechas as $fecha)
          {
            if(!$fecha['completo'])
            {
              if($fecha['horas'] == null || $fecha['horas'] == [])
                throw new Exception("Error Processing Request", 1);  
            }
          }
          return true;
        }
        catch(Exception $e)
        {
          return false;
        }

      }
    

}
