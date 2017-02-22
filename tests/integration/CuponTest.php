<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Seeder;
use App\User;
use App\cupon;

class CuponTest extends TestCase
{
    use DatabaseTransactions;
      /** @test */
    public function crear_cupon()
    {
    	$sesion['email']='test@gmail.com';
        $sesion['password']='123456';
    	$user= $this->action('get', 'AuthenticateController@authenticate', $sesion);
    	
    	//validacion formulario invalido
    	$datoscupon['dia']='2017-02-20';
        
        $datoscupon['calendario_id']=1;
        $crearCupon=$this->action('get', 'CuponController@create',$datoscupon);
        $this->assertEquals(400, $crearCupon->getStatusCode(), ''.$crearCupon);
        //404
        
        $datoscupon['servicio_id']=800;
        $datoscupon['word_key']='bergial';
        $datoscupon['porcentaje']=20;
        $datoscupon['fecha_inicial']='2017-02-23';
        $datoscupon['fecha_final']='2017-03-20';
        $crearCupon=$this->action('get', 'CuponController@create', $datoscupon);
        $this->assertEquals(404, $crearCupon->getStatusCode(), ''.$crearCupon);

    }
}