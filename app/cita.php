<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class cita extends Model
{
   	 protected $table = 'cita';
     protected $fillable = [
         'tipo_id','calendario_id','fecha_inicio', 'fecha_final', 'cliente_nombre','cliente_telefono','cliente_email',
    ];
    //relaciones
      public function tipo($id)
    	{
       return tipo::find($id)->select('nombre')->first()['nombre'];
    	}
      public function calendario()
      {
       return $this->belongsTo('App\calendario');
      }


    //metodos de clase

    /* 
    * @param arrayDatos estructura con nombre,telefono,email,fecha y hora de la cita
    */
      public function crear($arrayDatos)
    	{
          
            $calendario  = calendario::find($arrayDatos['calendario_id']);
            
            $NuevaCita = new cita;
            $NuevaCita->tipo_id = $arrayDatos['tipo_id'];
            $NuevaCita->fecha_inicio =$arrayDatos['fecha_inicio'];
            $NuevaCita->fecha_final =$arrayDatos['fecha_final'];
            $NuevaCita->cliente_nombre =$arrayDatos['cliente_nombre'];
            $NuevaCita->cliente_telefono =$arrayDatos['cliente_telefono'];
            $NuevaCita->cliente_email =$arrayDatos['cliente_email'];
            
            $calendario->citas()->save($NuevaCita);
        
    	}
      public function eliminar($cita)
    	{
      		$Cita = cita::find($cita['id']);
			if ($Cita === null) 
				{
   					return "cita no existe,verificar bien la codificacion";
				}
				else
					{
						$Cita->destroy();
					}
			
    	}
         /* 
    * @param arrayDatos estructura con nombre,telefono,email
    */
      public function editar($arrayDatos,$id)
    	{
              $Cita = cita::find($id);
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
      public function reagendar($arrayDatos,$id)
    	{
        $Cita = cita::find($id);
        if ($Cita === null) 
        {
            return "cita no existe,no se pudo realizar la modificacion";
        }
        else
          {
            $Cita->fecha_inicio = $arrayDatos['fecha_inicio'];
            $Cita->fecha_final =  $arrayDatos['fecha_final'];
            $Cita->save();
          }
       
      
    	}
      public function asignarTipo()
    	{
      
    	}
         /* 
    * @param Dato variable numerica que hace relacion a un id de la tabla Tipo
    */
      public function CambiarTipo($arrayDatos)
    	{
        $Cita = cita::find($arrayDatos['id']);
        if ($Cita === null) 
        {
            return "cita no existe,no se pudo realizar la modificacion";
        }
        else
          {
            $Cita->tipo_id = $arrayDatos['tipo_id'];
            $Cita->save();
          }
    	}    	
}
