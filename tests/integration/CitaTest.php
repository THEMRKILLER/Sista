<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Seeder;

use App\cita;

class CitaTest extends TestCase
{
    use DatabaseTransactions;
   
    public function fecha_esta_disponible_para_agendar()
    {
        //given
    $nuevaCita= Factory(App\cita::class)->create();
        $noUsadoEnLaBD['tipo_id']=525;
        $noUsadoEnLaBD['fecha_inicio']='2018-02-04 10:51:00';
    //when
    $regresaFalso =cita::fechaDisponible($nuevaCita);
        $regresaVerdadero =cita::fechaDisponible($noUsadoEnLaBD);
    //then
    $this->assertFalse($regresaFalso);
        $this->assertTrue($regresaVerdadero);
    }
   
    public function disponibilidad_del_calendario()
    {
        $tipo_id=3;//duracion 60 mins
        $calendario_id=1;
        $fechaDisponibilidadBaja= Factory(App\cita::class)->create(['fecha_inicio'=>'2017-02-21 08:00:00','fecha_final'=>'2017-02-21 14:00:00']);
        $fechaDisponibilidadMedia= Factory(App\cita::class)->create(['fecha_inicio'=>'2017-02-28 08:00:00','fecha_final'=>'2017-02-28 12:00:00']);
        $fechaDisponibilidadAlta= Factory(App\cita::class)->create(['fecha_inicio'=>'2017-02-23 08:00:00','fecha_final'=>'2017-02-23 10:00:00']);
        //testing the beast timeslot
        $Huecos_D_Baja= cita::timeslot($fechaDisponibilidadBaja->fecha_inicio, $tipo_id, $calendario_id);
        $Huecos_D_Media= cita::timeslot($fechaDisponibilidadMedia->fecha_inicio, $tipo_id, $calendario_id);
        $Huecos_D_Alta= cita::timeslot($fechaDisponibilidadAlta->fecha_inicio, $tipo_id, $calendario_id);

        $disponibilidadBaja=cita::espaciosPorFecha(count($Huecos_D_Baja));
        $disponibilidadMedia=cita::espaciosPorFecha(count($Huecos_D_Media));
        $disponibilidadAlta=cita::espaciosPorFecha(count($Huecos_D_Alta));
        
        $this->assertEquals($disponibilidadBaja, 3); //regresa disponibilidad Baja
        $this->assertEquals($disponibilidadMedia, 2); //regresa disponibilidad Media
        $this->assertEquals($disponibilidadAlta, 1); //regresa disponibilidad Alta
    }
    /** @test */
    public function creacion_de_citas()
    {
        //simulando datos de entrada de una cita
        $datosCita['calendario_id']=1;
        $datosCita['tipo_id']=1;
        $datosCita['fecha_inicio']='2018-02-21 08:00:00';
        $datosCita['cliente_nombre']='Metatron';
        $datosCita['cliente_telefono']='66660022';
        $datosCita['cliente_email']='ArcMet@gmail.com';
        $datosCita['costo_total']=0;
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
        $this->assertEquals(409, $fechapasada->getStatusCode(), "".$fechapasada);
		//dia inhabil (debe estar agregado en la bd)
        $datosCita['fecha_inicio']='2017-02-24 10:00:00';
        $diainhabil=$this->action('Post', 'CitaController@store', $datosCita);
        $this->assertEquals(404, $diainhabil->getStatusCode(), 'dia inhabil');
        // cupon no es valido
        $datosCita['cupon_descuento']='asdfeg';
        $datosCita['fecha_inicio']='2018-02-21 10:00:00';
        $cuponinvalido=$this->action('Post', 'CitaController@store', $datosCita);
        $this->assertEquals(400, $cuponinvalido->getStatusCode(), ' cupon invalido');
        // cupon valido pero costo total incorrecto
 		$datosCita['cupon_descuento']='last24z0';
 		        $datosCita['costo_total']=2000;
        $cuponvalido=$this->action('Post', 'CitaController@store', $datosCita);
        $this->assertEquals(400, $cuponvalido->getStatusCode(), ' cupon valido costo total incorrecto');
        // cupon valido,costo correcto
 		
 		        $datosCita['costo_total']=0;
        $cuponvalido=$this->action('Post', 'CitaController@store', $datosCita);
        $this->assertEquals(200, $cuponvalido->getStatusCode(), ' cupon valido');
        //recurso no disponible.
        $datosCita['calendario_id']=28;
        $datosCita['tipo_id']=1024;
        $FalloAgendar=$this->action('Post', 'CitaController@store', $datosCita);
        $this->assertEquals(404, $FalloAgendar->getStatusCode(), 'se trata de acceder a un recurso inexistente');
        //fallo de formulario
                $datosCita['calendario_id']=1;
        $datosCita['tipo_id']=1;
        $datosCita['cliente_nombre']='               ';
        $ErrorValidador=$this->action('Post', 'CitaController@store', $datosCita);
        $this->assertEquals(400, $ErrorValidador->getStatusCode(), 'validador falla');
   		
   		        //cita creada con conflictos
        $datosCita['calendario_id']=1;
        $datosCita['cliente_nombre']='khun aguero annis';
        $datosCita['tipo_id']=203;
        $datosCita['costo_total']=100;
        $datosCita['fecha_inicio']='2018-02-24 10:00:00';
        $datosCita['cupon_descuento']='';
        $conflictos = $this->action('Post', 'CitaController@store', $datosCita);
        $this->assertEquals(403, $conflictos->getStatusCode(), ''.$conflictos);



    }
    /** @test */
    public function eliminar_citas()
    {
        $datosCita['tipo_id']=1;
        $datosCita['fecha_inicio']='2018-02-21 08:00:00';
        $citaExiste=$this->action('put', 'CitaController@reagendar', $datosCita);
    }
}
