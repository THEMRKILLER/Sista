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
}
