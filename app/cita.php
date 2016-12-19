<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class cita extends Model
{
   	 protected $table = 'cita';
     protected $fillable = [
         'tipo_id','fecha', 'hora', 'cliente_nombre','cliente_telefono','cliente_email',
    ];
    //relaciones
      public function Tipo()
    	{
       return $this->hasOne('App\tipo');
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
