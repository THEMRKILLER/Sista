<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class cita extends Model
{
   	 protected $table = 'cita';
     protected $fillable = [
         'tipo_id','calendario_id','fecha', 'hora', 'cliente_nombre','cliente_telefono','cliente_email',
    ];
    //relaciones
      public function tipo()
    	{
       return $this->hasOne('App\tipo');
    	}
    //metodos de clase

    /* 
    * @param arrayDatos estructura con nombre,telefono,email,fecha y hora de la cita
    */
      public function crear($arrayDatos,$calendario_id,$tipo_id)
    	{
          
            $NuevaCita = new App\cita;
            $NuevaCita->calendario_id =$calendario_id;
            $NuevaCita->tipo_id =$tipo_id;
            $NuevaCita->fecha =$arrayDatos['fecha'];
            $NuevaCita->hora =$arrayDatos['hora'];
            $NuevaCita->cliente_nombre =$arrayDatos['cliente_nombre'];
            $NuevaCita->cliente_telefono =$arrayDatos['cliente_telefono'];
            $NuevaCita->cliente_email =$arrayDatos['cliente_email'];
            $NuevaCita->save();
        
    	}
      public function eliminar($id)
    	{
      		$Cita = App\cita::find($id);
			if ($Cita === null) 
				{
   					return "cita no existe,verificar bien la codificacion";
				}
				else
					{
						$Cita->delete();
					}
			
    	}
         /* 
    * @param arrayDatos estructura con nombre,telefono,email
    */
      public function editar($cita_id,$arrayDatos)
    	{
              $Cita = App\cita::find($cita_id);
        if ($Cita === null) 
        {
            return "cita no existe,no se pudo realizar la modificacion";
        }
        else
          {
            $Cita->cliente_nombre = $arrayDatos['cliente_nombre'];
            $Cita->cliente_telefono =  $arrayDatos['cliente_telefono'];
            $Cita->cliente_email =  $arrayDatos['cliente_email'];
            $Cita->save();
          }
    	}
   /* 
    * @param arrayDatos estructura con fecha y hora 
    */
      public function reagendar($cita_id,$arrayDatos)
    	{
        $Cita = App\cita::find($cita_id);
        if ($Cita === null) 
        {
            return "cita no existe,no se pudo realizar la modificacion";
        }
        else
          {
            $Cita->fecha = $arrayDatos['fecha'];
            $Cita->hora =  $arrayDatos['hora'];
            $Cita->save();
          }
       
      
    	}
      public function asignarTipo()
    	{
      
    	}
         /* 
    * @param Dato variable numerica que hace relacion a un id de la tabla Tipo
    */
      public function CambiarTipo($cita_id,$Dato)
    	{
        $Cita = App\cita::find($cita_id);
        if ($Cita === null) 
        {
            return "cita no existe,no se pudo realizar la modificacion";
        }
        else
          {
            $Cita->tipo_id = $Dato;
            $Cita->save();
          }
    	}    	
}
