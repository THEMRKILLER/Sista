<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Seeder;
use Illuminate\Http\Response as HttpResponse;
use App\User;
use App\cita;
use App\fechahora_inhabil;

class CitaUnitTest extends TestCase
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
 
    public function horas_del_dia()
    {
        ///24 hrs day
        $fecha='2017-11-11';
        $calendario_id=3;
        $horas=cita::horasDelDia($fecha, $calendario_id);
        $this->assertInternalType('array', $horas);
        $this->assertEquals(24, count($horas));
    }
           
    public function horas_inhabiles_del_dia()
    {
        ///dia inhabil completo

        $fecha='2017-03-24';
        $calendario_id=2;
        $horas=cita::filtroHorasInhabiles($fecha, $calendario_id);
        $this->assertInternalType('array', $horas);
        $this->assertEquals(24, count($horas), json_encode($horas));
        ///10 horas menos
        
        $fecha='2017-03-31';
        $calendario_id=2;
        $horas=cita::filtroHorasInhabiles($fecha, $calendario_id);
        $this->assertInternalType('array', $horas);

        $this->assertEquals(10, count($horas), json_encode($horas));
    }
    /** @test */
    public function dias_no_habiles_del_calendario()
    {
        $calendario_id=2;
        $horas=cita::diasNoHabiles($calendario_id);
        $this->assertInternalType('array', $horas);

        $this->assertEquals(2, count($horas), json_encode($horas));
    }

    public function revisar_si_la_fecha_esta_inhabilitada()
    {
        $datoscita['fecha_inicio']='2017-03-24';
        $datoscita['calendario_id']=2;
        $dia=cita::revisarDiasInhabiles($datoscita);
        $this->assertFalse($dia);
    }
        /** @test */
    public function verificar_funcionamiento_de_timeslot()
    {
        ////todo esto es asumiendo un dia de 9 horas
        $fecha='2017-07-24';
        ///60 mins
        $tipo_id=1;
        $calendario_id=2;
        $horasdeldia=cita::timeslot($fecha, $tipo_id, $calendario_id);
        $this->assertEquals(9, count($horasdeldia), json_encode($horasdeldia));
         /////horas del dia
        $fecha='2017-07-24';
        ///120 mins
        $tipo_id=2;
        $calendario_id=2;
        $horasdeldia=cita::timeslot($fecha, $tipo_id, $calendario_id);
        $this->assertEquals(4, count($horasdeldia), json_encode($horasdeldia));
                ///20 mins
        $tipo_id=3;
        $calendario_id=2;
        $horasdeldia=cita::timeslot($fecha, $tipo_id, $calendario_id);
        $this->assertEquals(27, count($horasdeldia), json_encode($horasdeldia));
        ///1 min
        $tipo_id=15;
        $calendario_id=2;
        $horasdeldia=cita::timeslot($fecha, $tipo_id, $calendario_id);
        $this->assertEquals(540, count($horasdeldia), json_encode($horasdeldia));
    }
}
