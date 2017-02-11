<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Seeder;

use App\cita;

class CitaTest extends TestCase
{
    use DatabaseTransactions;
    /** @test */
    public function fecha_esta_disponible_para_agendar()
    {
        //given
    $nuevaCita= Factory(App\cita::class)->create();
        $noUsadoEnLaBD['tipo_id']=2;
        $noUsadoEnLaBD['fecha_inicio']='2018-02-04 10:51:00';
    //when
    $regresaFalso =cita::fechaDisponible($nuevaCita);
        $regresaVerdadero =cita::fechaDisponible($noUsadoEnLaBD);
    //then
    $this->assertFalse($regresaFalso);
        $this->assertTrue($regresaVerdadero);
    }
    /** @test */
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
        //given
        //simulando datos de entrada de una cita
        $datosCita['calendario_id']=1;
        $datosCita['tipo_id']=1;
        $datosCita['fecha_inicio']='2018-02-21 08:00:00';
        //$datosCita['fecha_final']='2018-02-21 09:00:00';
        $datosCita['cliente_nombre']='Metatron';
        $datosCita['cliente_telefono']='66660022';
        $datosCita['cliente_email']='ArcMet@gmail.com';
        //uso del metodo para crear cita
        $nuevaCita = $this->action('Post', 'CitaController@store', $datosCita);
        //cita creada correctamente
        $this->assertEquals(200, $nuevaCita->getStatusCode(), "cita agendada correctamente");
        //cambio de valores de calendario y tipo para probar un recurso no disponible
        $datosCita['calendario_id']=23;
        $datosCita['tipo_id']=23;
        
        $FalloAgendar=$this->action('Post', 'CitaController@store', $datosCita);
        $this->assertEquals(404, $FalloAgendar->getStatusCode(), 'se trata de acceder a un recurso inexistente');
        
        $datosCita['tipo_id']='a';
        $datosCita['calendario_id']='aweer';
        $datosCita['cliente_nombre']='               ';
        $ErrorValidador=$this->action('Post', 'CitaController@store', $datosCita);
        $this->assertEquals(400, $ErrorValidador->getStatusCode(), 'validador falla');
    }
    /** @test */
    public function reagendacion_de_citas()
    {
        $datosCita['tipo_id']=1;
        $datosCita['fecha_inicio']='2018-02-21 08:00:00';
        $citaExiste=$this->action('put', 'CitaController@reagendar', $datosCita);
           
    }
}
