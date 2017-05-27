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
      public function cupones()
      {
        return $this->hasMany('App\Cupon','servicio_id');
      }
      public function calendario()
      {
        return $this->belongsTo('App\calendario');
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
        $tipo->costo  = $datas['costo'];
        $tipo->denominacion = $datas['denominacion'];
        $tipo->servicio_domicilio = $datas['servicio'];
        $calendario->tipos()->save($tipo);



      }
      public function eliminar()
    	{
        $this->delete();
    	}
    /* 
    * @param arrayDatos estructura con el nombre y la duracion del tipo de cita 
    */
      public function editar($ArrayDatos)
    	{
          $this->nombre = $ArrayDatos['nombre'];
          $this->duracion = $ArrayDatos['duracion'];
          $this->costo = $ArrayDatos['costo'];
          $this->denominacion = $ArrayDatos['denominacion'];
          $this->servicio_domicilio = $ArrayDatos['servicio'];
          $this->save();
    	}

}
