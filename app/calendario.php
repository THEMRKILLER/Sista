<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class calendario extends Model
{
	 protected $table = 'calendario';
     protected $fillable = [
        'usuario',
        ];
        //relaciones

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
      public function asignar_horario()
    	{
      
    	}
      public function inhabilitar_fecha()
    	{
      
    	}
}
