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
      public function reagendar()
    	{
      
    	}
      public function asignarTipo()
    	{
      
    	}
      public function CambiarTipo()
    	{
      
    	}
}
