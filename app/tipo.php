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
            $NuevoTipo = new App\tipo;
            $NuevaCita->nombre =$arrayDatos['fecha'];
            $NuevaCita->duracion =$arrayDatos['hora'];
            $NuevaCita->save();
    	}
      public function eliminar($id)
    	{
            $Tipo = App\tipo::find($id);
            if ($Tipo === null) 
              {
              return "cita no existe,verificar bien la codificacion";
              }
            else
                {
                $Tipo->delete();
                }
      
    	}
    /* 
    * @param arrayDatos estructura con el nombre y la duracion del tipo de cita 
    */
      public function editar($arrayDatos,$tipo_id)
    	{
        $Tipo = App\cita::tipo($tipo_id);
          if ($Tipo === null) 
            {
            return "cita no existe,no se pudo realizar la modificacion";
            }
          else
            {
              $Tipo->nombre =   $arrayDatos['nombre'];
              $Tipo->duracion = $arrayDatos['duracion'];
              $Cita->save();
            }
    	}

}
