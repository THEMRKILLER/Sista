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
