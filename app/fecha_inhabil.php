<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class fecha_inhabil extends Model
{
   	 protected $table = 'fecha_inhabil';
     protected $fillable = [
        'calendario_id', 'fecha', 'completo',
    ];

     public function horasInhabiles()
    	{
        return $this->hasMany('App\fechahora_inhabil');
    	}    

    	}
    	//metodos de clase
          /* 
    * @param Arraydatos arreglo con los siguientes datoss :fecha(restriccion de agendacion),completo(culminacion del registro)
        */
      public function agregar($ArrayDatos,$calendario_id)
    	{
            $FechaInhabil = new App\fecha_inhabil;
            $FechaInhabil->calendario_id =$calendario_id;
            $FechaInhabil->fecha =$ArrayDatos->fecha;
            $FechaInhabil->completo =$ArrayDatos->completo;
            $FechaInhabil->save();
    	}
      public function eliminar($id)
    	{
            $FechaInhabil = App\fecha_inhabil::find($id);
            if ($FechaInhabil === null) 
              {
              return "el dia no esta registrado,verificar bien la codificacion";
              }
            else
                {
                $FechaInhabil->delete();
                }
      
    	}    	
}
