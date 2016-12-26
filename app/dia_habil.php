<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\hora_habil;
class dia_habil extends Model
{
   	 protected $table = 'dia_habil';
     protected $fillable = [
        'calendario_id', 'dia',
    ];

         public function horasHabiles()
    	{
       return $this->hasMany('App\hora_habil','diahabil_id');
    	}
    	//metodos de clase
          /* 
    * @param Dato String con el dia de trabajo
    */
    	      public function agregar($Dato,$calendario_id)
    	{
            $DiaHabil = new App\dia_habil;
            $DiaHabil->calendario_id =$calendario_id;
            $DiaHabil->dia =$Dato;
            $DiaHabil->save();
    	}
      public function eliminar($id)
    	{
            $DiaHabil = App\dia_habil::find($id);
            if ($DiaHabil === null) 
              {
              return "el dia no esta registrado,verificar bien la codificacion";
              }
            else
                {
                $DiaHabil->delete();
                }
      
    	}
      public function asignar_horas($horas)
      {
        foreach ($horas as $hora) {
            $hora_habil = hora_habil::firstOrNew(['diahabil_id'=> $this->id, 'hora' => $hora]);
            $this->horasHabiles()->save($hora_habil);
        }

      }

}
