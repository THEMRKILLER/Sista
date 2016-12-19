<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tipo extends Model
{
     protected $table = 'tipo';
     protected $fillable = [
        'nombre', 'duracion',
    ];
         public function Citas()
    	{
        return $this->hasMany('App\cita');
    	}
    	//metodos de clase
      public function agregar()
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
}
