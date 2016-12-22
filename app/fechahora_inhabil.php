<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class fechahora_inhabil extends Model
{
   	 protected $table = 'fechahora_inhabil';
     protected $fillable = [
        'fechainhabil_id', 'hora',
    ];
          public function fechaInhabil()
      {
       return $this->belongsTo('App\fecha_inhabil');
      }
    //metodos de clase
          /* 
    * @param Dato variable con la hora que se inhabilitara en el calendario
        */
      public function agregar($Dato,$fechainhabil_id)
    	{
            $HoraInhabil = new App\fecha_inhabil;
            $HoraInhabil->calendario_id =$calendario_id;
            $HoraInhabil->fecha =$ArrayDatos->fecha;
            $HoraInhabil->completo =$ArrayDatos->completo;
            $HoraInhabil->save();
    	}
      public function eliminar($id)
    	{
            $HoraInhabil = App\fecha_inhabil::find($id);
            if ($HoraInhabil === null) 
              {
              return "el dia no esta registrado,verificar bien la codificacion";
              }
            else
                {
                $HoraInhabil->delete();
                }
      
    	}    	
}
