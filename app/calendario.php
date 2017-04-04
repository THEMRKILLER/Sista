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
                  if($dia_habil['laboral'] == false)
                  {
                    $d_l = $this->diasHabiles()->where('dia',$dia_habil['dia'])->first();
                    if($d_l)$d_l->delete();  
                  }
                  else
                  {
                    $dia_habil_model = dia_habil::firstOrNew(['dia' => $dia_habil['dia'],'calendario_id' => $this->id]);
                    $this->diasHabiles()->save($dia_habil_model);
                    $dia_habil_model->save();
                    $dia_habil_model->asignar_horas($dia_habil['horas']);
                  }
                
                
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
