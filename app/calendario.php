<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\dia_habil;
use App\hora_habil;
use App\User;
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
      public function agregar_cita()
    	{
            $ArrayDatos=array("calendario_id"=>1,"tipo_id"=>1,"fecha_inicio"=>"9/9/16","fecha_final"=>"12,12,16","cliente_nombre"=>"german","cliente_telefono"=>"512341","cliente_email"=>"dudg@gmail.com");
            $Cita = new cita;
            $Cita->crear($ArrayDatos);
    	}

      public function asignar_horario($dias_habiles)
    	{
         
        foreach ($dias_habiles as $dia_habil) {
                
                $dia_habil_model = dia_habil::firstOrNew(['dia' => $dia_habil['dia'],'calendario_id' => $this->id]);
                $this->diasHabiles()->save($dia_habil_model);
                $dia_habil_model->save();
                if($dia_habil['laboral'])$dia_habil_model->asignar_horas($dia_habil['horas']);
                else $dia_habil_model->asignar_horas(null);
        }
      
    	}

      public function inhabilitar_fecha($fechas)
    	{
            
            
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
    	}
    

}
