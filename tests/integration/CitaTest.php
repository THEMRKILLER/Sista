<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Seeder;

use App\cita;

class CitaTest extends TestCase
{
    use DatabaseTransactions;
   

    
          /*** @test*/
    public function creacion_de_citas()
    {
        //simulando datos de entrada de una cita
        $datosCita['calendario_id']=2;
        $datosCita['tipo_id']=2;
        $datosCita['fecha_inicio']='2018-02-21 08:00:00';
        $datosCita['cliente_nombre']='Metatron';
        $datosCita['cliente_telefono']='6665360022';
        $datosCita['cliente_email']='nyhedgg@gmail.com';
        $datosCita['costo_total']=500;
        $datosCita['cupon_descuento']='';
        
        //acceso a otro calendario :
   

        //cita creada correctamente
      
        $nuevaCita = $this->action('Post', 'CitaController@store', $datosCita);
        $this->assertEquals(200, $nuevaCita->getStatusCode(), "".$nuevaCita);
           

        //fecha no disponible
         $fechanodisponible=$this->action('Post', 'CitaController@store', $datosCita);
        $this->assertEquals(404, $fechanodisponible->getStatusCode(), 'fecha no disponible ');
        //cita con una fecha que ya paso
        $datosCita['fecha_inicio']='2017-02-14 12:00:00';
        $fechapasada = $this->action('Post', 'CitaController@store', $datosCita);
        $this->assertEquals(400, $fechapasada->getStatusCode(), "".$fechapasada);
        //dia inhabil (debe estar agregado en la bd)
        $datosCita['fecha_inicio']='2017-03-25 10:00:00';
        $diainhabil=$this->action('Post', 'CitaController@store', $datosCita);
        $this->assertEquals(404, $diainhabil->getStatusCode(), 'dia inhabil');
        // cupon no es valido
        $datosCita['cupon_descuento']='asdfeg';
        $datosCita['fecha_inicio']='2018-02-21 10:00:00';
        $cuponinvalido=$this->action('Post', 'CitaController@store', $datosCita);
        $this->assertEquals(400, $cuponinvalido->getStatusCode(), ' cupon invalido');
        // cupon valido pero costo total incorrecto
        $datosCita['cupon_descuento']='dental2003j5';
        $datosCita['costo_total']=8000;
        $cuponvalido=$this->action('Post', 'CitaController@store', $datosCita);
        $this->assertEquals(400, $cuponvalido->getStatusCode(), ' cupon valido costo total incorrecto');
        // cupon valido,costo correcto
        
                $datosCita['costo_total']=400;
        $cuponvalido=$this->action('Post', 'CitaController@store', $datosCita);
        $this->assertEquals(200, $cuponvalido->getStatusCode(), ' cupon valido'.$cuponvalido);
        //recurso no disponible.
        $datosCita['calendario_id']=28;
        $datosCita['tipo_id']=1024;
        $FalloAgendar=$this->action('Post', 'CitaController@store', $datosCita);
        $this->assertEquals(404, $FalloAgendar->getStatusCode(), 'se trata de acceder a un recurso inexistente');
        //fallo de formulario
                $datosCita['calendario_id']=2;
        $datosCita['tipo_id']=2;
        $datosCita['cliente_nombre']='               ';
        $ErrorValidador=$this->action('Post', 'CitaController@store', $datosCita);
        $this->assertEquals(400, $ErrorValidador->getStatusCode(), 'validador falla');
        
                //cita creada con conflictos
        $datosCita['calendario_id']=3;
        $datosCita['cliente_nombre']='khun aguero annis';
        $datosCita['tipo_id']=1;
        $datosCita['costo_total']=0.0000;
        $datosCita['fecha_inicio']='2018-02-24 10:00:00';
        $datosCita['cupon_descuento']='';
        $conflictos = $this->action('Post', 'CitaController@store', $datosCita);
        $this->assertEquals(404, $conflictos->getStatusCode(), ''.$conflictos);
    }
            
    public function Reagendar_citas()
    {
        $datosCita['tipo_id']=2;
        $datosCita['id_cita']=294;
        $datosCita['calendario_id']=2;
        $datosCita['fecha_inicio']='2017-08-21 10:00:00';
        //reagendacion exitosa
        $reagendar=$this->action('put', 'CitaController@reagendar', $datosCita);
        $this->assertEquals(200, $reagendar->getStatusCode(), ''.$reagendar);
        

        //fecha  no disponible
        $reagendar=$this->action('put', 'CitaController@reagendar', $datosCita);

        $this->assertEquals(404, $reagendar->getStatusCode(), ''.$reagendar);
              //fallo del formulario
        $datosCita['fecha_inicio']=' ';
        $reagendar=$this->action('put', 'CitaController@reagendar', $datosCita);
        $this->assertEquals(400, $reagendar->getStatusCode(), ''.$reagendar);
        $datosCita['fecha_inicio']='24-02-2017 10:00:00 ';
        $reagendar=$this->action('put', 'CitaController@reagendar', $datosCita);
        $this->assertEquals(400, $reagendar->getStatusCode(), ''.$reagendar);
                //dia inhabil
        $datosCita['fecha_inicio']='2017-02-24 10:00:00';
        $reagendar=$this->action('put', 'CitaController@reagendar', $datosCita);
        $this->assertEquals(404, $reagendar->getStatusCode(), ''.$reagendar);
        

        //recurso no existe
        $datosCita['tipo_id']=500;
        $reagendar=$this->action('put', 'CitaController@reagendar', $datosCita);
        $this->assertEquals(404, $reagendar->getStatusCode(), ''.$reagendar);
        $datosCita['id_cita']=1;
        $reagendar=$this->action('put', 'CitaController@reagendar', $datosCita);
        $this->assertEquals(404, $reagendar->getStatusCode(), ''.$reagendar);
        $datosCita['calendario_id']=20;
        $reagendar=$this->action('put', 'CitaController@reagendar', $datosCita);
        $this->assertEquals(404, $reagendar->getStatusCode(), ''.$reagendar);
        //accede a un calendario diferente
        $datosCita['tipo_id']=4;
        $datosCita['id_cita']=137;
        $datosCita['calendario_id']=2;
        $datosCita['fecha_inicio']='2017-09-21 10:00:00';
        $reagendar=$this->action('put', 'CitaController@reagendar', $datosCita);
        $this->assertEquals(404, $reagendar->getStatusCode(), ''.$reagendar);
    }
    
    public function eliminar_cita()
    {
        //elimina bien
        $datosCita['codigo']='5a6q3';
        $datosCita['numeromail']='nyhedgg@gmail.com';
        $eliminar=$this->action('delete', 'CitaController@destroy', $datosCita);
        $this->assertEquals(200, $eliminar->getStatusCode(), ''.$eliminar);
                //codigo no existe
       
        $eliminar=$this->action('delete', 'CitaController@destroy', $datosCita);
        $this->assertEquals(404, $eliminar->getStatusCode(), ''.$eliminar);
                        //codigo bien email mal
        $datosCita['codigo']='f5yhj';
        $datosCita['numeromail']='f5yhj';
        $eliminar=$this->action('delete', 'CitaController@destroy', $datosCita);
        $this->assertEquals(404, $eliminar->getStatusCode(), ''.$eliminar);
        //elimina con email y numero de telefono
        $datosCita['numeromail']='9612280890';
        $eliminar=$this->action('delete', 'CitaController@destroy', $datosCita);
        $this->assertEquals(200, $eliminar->getStatusCode(), ''.$eliminar);
    }

    public function regreso_de_horas_dispónibles()
    {
        $datosCita['dia']='2017-02-20';
        $datosCita['tipo_id']=4;
        $datosCita['calendario_id']=1;
        $horasDisponible=$this->action('get', 'CitaController@horasDisponibles', $datosCita);

        $this->assertEquals(200, $horasDisponible->getStatusCode(), ''.$horasDisponible);
         ///acceso restringido
        $datosCita['tipo_id']=1;
        $datosCita['calendario_id']=3;
        $horasDisponible=$this->action('get', 'CitaController@horasDisponibles', $datosCita);
        $this->assertEquals(403, $horasDisponible->getStatusCode(), ''.$horasDisponible);
        $datosCita['tipo_id']=203;
        $datosCita['calendario_id']=1;
        $horasDisponible=$this->action('get', 'CitaController@horasDisponibles', $datosCita);
        $this->assertEquals(403, $horasDisponible->getStatusCode(), ''.$horasDisponible);
        //recurso no disponible
        $datosCita['tipo_id']=1000;
        $datosCita['calendario_id']=1;
        $horasDisponible=$this->action('get', 'CitaController@horasDisponibles', $datosCita);
        $this->assertEquals(404, $horasDisponible->getStatusCode(), ''.$horasDisponible);
        $datosCita['tipo_id']=1;
        $datosCita['calendario_id']=500;
        $horasDisponible=$this->action('get', 'CitaController@horasDisponibles', $datosCita);
        $this->assertEquals(404, $horasDisponible->getStatusCode(), ''.$horasDisponible);
    }
     
        public function disponibilidadCalendario()
        {
            ////OK

            $datosCita['tipo_id']=3;
            $datosCita['calendario_id']=1;
            $datosCita['mes']=03;
            $dispCal=$this->action('get', 'CitaController@disponibilidadCalendario', $datosCita);
            $this->assertEquals(200, $dispCal->getStatusCode(), ''.$dispCal);
            ////acceso restringido
            $datosCita['tipo_id']=3;
            $datosCita['calendario_id']=1;
            $dispCal=$this->action('get', 'CitaController@disponibilidadCalendario', $datosCita);
            $this->assertEquals(200, $dispCal->getStatusCode(), ''.$dispCal);
            ////acceso restringido
            $datosCita['tipo_id']=3;

            $datosCita['calendario_id']=2;
            $dispCal=$this->action('get', 'CitaController@disponibilidadCalendario', $datosCita);
            $this->assertEquals(403, $dispCal->getStatusCode(), ''.$dispCal);
           ////recurso no disponible
            $datosCita['tipo_id']=6;
            $datosCita['calendario_id']=5;
            $dispCal=$this->action('get', 'CitaController@disponibilidadCalendario', $datosCita);
            $this->assertEquals(404, $dispCal->getStatusCode(), ''.$dispCal);

        }


        public function horas()
        {
                    $datosCita['dia']='2017-02-27';
        $datosCita['tipo_id']=4;
        $datosCita['calendario_id']=3;
        //$horasDisponible=$this->action('get', 'CitaController@horasDisponibles', $datosCita);

        //$this->assertEquals(200, $horasDisponible->getStatusCode(), ''.$horasDisponible);

        }
}
