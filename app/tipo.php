<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tipo extends Model
{
     protected $table = 'tipo';
     protected $fillable = [
        'nombre', 'duracion','id'
    ];
         public function citas()
    	{
        return $this->hasMany('App\cita');
    	}
      public function calendario()
      {
        return $this->belongsTo('App\tipo');
      }
      

 
    	//metodos de clase
          /* 
    * @param arrayDatos estructura con el nombre y la duracion del tipo de cita 
    */
      public function agregar($arrayDatos)
    	{
            $NuevoTipo = new tipo;
            $NuevaCita->nombre =$arrayDatos['fecha'];
            $NuevaCita->duracion =$arrayDatos['hora'];
            $NuevaCita->save();
    	}
      public static function crear($datas,calendario $calendario)
      {
        $tipo = new tipo();
        $tipo->nombre = $datas['nombre'];
        $tipo->duracion = $datas['duracion'];
        $calendario->tipos()->save($tipo);



      }
      public function eliminar($tipo)
    	{
            $Tipo = tipo::find($tipo['id']);
            if ($Tipo === null) 
              {
              return "tipo no existe,verificar bien la codificacion";
              }
            else
                {
                $Tipo->destroy();
                }
      
    	}
    /* 
    * @param arrayDatos estructura con el nombre y la duracion del tipo de cita 
    */
      public function editar($ArrayDatos,$tipo)
    	{
        $Tipo = tipo($tipo['id']);
          if ($Tipo === null) 
            {
            return "tipo no existe,no se pudo realizar la modificacion";
            }
          else
            {
              $Tipo->nombre =   $tipo['nombre'];
              $Tipo->duracion = $tipo['duracion'];
              $Cita->save();
            }
    	}

}
