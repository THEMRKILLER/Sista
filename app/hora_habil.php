<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class hora_habil extends Model
{
    	 protected $table = 'hora_habil';
    	 protected $fillable = [
        'diahabil_id', 'hora',
    ];

      public function agregar($Dato,$diahabil_id)
    	{
            $HoraHabil = new App\hora_habil;
            $HoraHabil->diahabil_id =$diahabil_id;
            $HoraHabil->hora =$Dato;
            $HoraHabil->save();
    	}
      public function eliminar($id)
    	{
            $HoraHabil = App\hora_habil::find($id);
            if ($HoraHabil === null) 
              {
              return "el dia no esta registrado,verificar bien la codificacion";
              }
            else
                {
                $HoraHabil->delete();
                }
      
    	}
}
