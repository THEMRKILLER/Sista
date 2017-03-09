<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Seeder;
use Illuminate\Http\Response as HttpResponse;
use App\User;
use App\cita;
use App\calendario;

class CalendarioUnitTest extends TestCase
{
    use DatabaseTransactions;
  

    public function dias_no_habiles_del_calendario()
    {
      
    }

    public function probar_deshabilitar_fecha()
    {
        $fechas= array('fecha' => '2017-02-01', 'completo' => false, 'horas' => [1,2,3,4]);
        $calendario = new calendario;
        $dia=$calendario->inhabilitar_fecha($fechas);
          $this->assertEquals(200, $dia->getStatusCode(), "".$dia);
    }
        
}
