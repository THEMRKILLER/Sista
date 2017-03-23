<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Seeder;
use Illuminate\Http\Response as HttpResponse;
use App\User;
use App\cupon;
use App\fechahora_inhabil;

class CalendarioTest extends TestCase
{
    use DatabaseTransactions;

       
    public function probarindex()
    {
        ////sin token 400
        $response = $this->call('get', '/api/v1/dashboard', [], [], [], [], []);
        $this->assertEquals(400, $response->getStatusCode(), ''.$response);
        //con token
        $credentials = JWTAuth::attempt(['email' => 'cristianrocker93@gmail.com', 'password' => 'nomeacuerdo']);
        $response = $this->call('get', '/api/v1/dashboard', [], [], [], ['HTTP_Authorization' => 'Bearer ' . $credentials], []);
        $this->assertEquals(200, $response->getStatusCode(), ''.$response);
    }


  
    public function prueba_obtender_dias_habiles()
    {
        ///  Route::get('dias_habiles', 'CalendarioController@getDiasHabiles');
        $calendario['calendario']=2;
        $response = $this->action('get', 'CalendarioController@getDiasHabiles', $calendario);
        $this->assertEquals(200, $response->getStatusCode(), "".$response);
        ////cal id distinc
        $calendario['calendario']=3;
        $response = $this->action('get', 'CalendarioController@getDiasHabiles', $calendario);
        $this->assertEquals(200, $response->getStatusCode(), "".$response);
        ////cal id = null
        $calendario['calendario']=4;
        $response = $this->action('get', 'CalendarioController@getDiasHabiles', $calendario);
        $this->assertEquals(404, $response->getStatusCode(), "".$response);
        ////cal id = null
        $calendario['calendario']=210;
        $response = $this->action('get', 'CalendarioController@getDiasHabiles', $calendario);
        $this->assertEquals(404, $response->getStatusCode(), "".$response);
    }
   
    public function prueba_obtender_dias_horas_inhabiles()
    {
        $datos['calendario_id']=null;
        $response = $this->action('get', 'CalendarioController@getDiasHorasInhabiles', $datos);
        $this->assertEquals(404, $response->getStatusCode(), "".$response);
            //normal
            $datos['calendario_id']=3;
        $response = $this->action('get', 'CalendarioController@getDiasHorasInhabiles', $datos);
        $this->assertEquals(200, $response->getStatusCode(), "".$response);
    }
    public function set_dias_horas_inhabiles()
    {
        /////dia deshabilitado completo normalmente
        $datos['fecha']='2017-11-11';
        $datos['completo']=true;
        $datos['horas']=array();
        $credentials = JWTAuth::attempt(['email' => 'cristianrocker93@gmail.com', 'password' => 'nomeacuerdo']);
        $response = $this->call('post', '/api/v1/fecha_inhabil', $datos, [], [], ['HTTP_Authorization' => 'Bearer ' . $credentials], []);
        $this->assertEquals(200, $response->getStatusCode(), "".$response);
         /////dia deshabilitado completo normalmente mismo dia
        $datos['fecha']='2017-11-11';
        $datos['completo']=true;
        $datos['horas']=array();
        $response = $this->call('post', '/api/v1/fecha_inhabil', $datos, [], [], ['HTTP_Authorization' => 'Bearer ' . $credentials], []);
        $this->assertEquals(400, $response->getStatusCode(), "".$response);
        /////dia deshabilitado completo con horas
        $datos['fecha']='2017-11-12';
        $datos['completo']=true;
        $datos['horas']=[9,8,7,6,5,4];
        $response = $this->call('post', '/api/v1/fecha_inhabil', $datos, [], [], ['HTTP_Authorization' => 'Bearer ' . $credentials], []);
        $this->assertEquals(200, $response->getStatusCode(), "".$response);
        /////dia deshabilitado a medias
        $datos['fecha']='2017-11-13';
        $datos['completo']=false;
        $datos['horas']=[9,8,7,6,5,4];
        $response = $this->call('post', '/api/v1/fecha_inhabil', $datos, [], [], ['HTTP_Authorization' => 'Bearer ' . $credentials], []);
        $this->assertEquals(200, $response->getStatusCode(), "".$response);
    }
   
    public function borrar_dia_hora_inhabil()
    {
        /////no encontrado
        $datos['fecha_inhabil_id']=10234;
        $credentials = JWTAuth::attempt(['email' => 'cristianrocker93@gmail.com', 'password' => 'nomeacuerdo']);
        $response = $this->call('delete', '/api/v1/fecha_inhabil', $datos, [], [], ['HTTP_Authorization' => 'Bearer ' . $credentials], []);
        $this->assertEquals(404, $response->getStatusCode(), "".$response);
        //borrar normal
        $datos['fecha_inhabil_id']=25;
        $response = $this->call('delete', '/api/v1/fecha_inhabil', $datos, [], [], ['HTTP_Authorization' => 'Bearer ' . $credentials], []);
        $this->assertEquals(200, $response->getStatusCode(), "".$response);
                
                ///borrar en calendario ageno
     $datos['fecha_inhabil_id']=2;
        $response = $this->call('delete', '/api/v1/fecha_inhabil', $datos, [], [], ['HTTP_Authorization' => 'Bearer ' . $credentials], []);
        $this->assertEquals(404, $response->getStatusCode(), "".$response);
    }
    /** @test */
    public function probar_asignar_horarios()
    {
        $credentials = JWTAuth::attempt(['email' => 'cristianrocker93@gmail.com', 'password' => 'nomeacuerdo']);
                  $datos=null;
               $response = $this->call('post', '/api/v1/dias_habiles', [null], [], [], ['HTTP_Authorization' => 'Bearer ' . $credentials], []);
        $this->assertEquals(404, $response->getStatusCode(), "".$response);
        $datos= [
    "dias" =>
                [
                    ["nombre" => "Lunes","dia" => 1,"laboral" => true,"horas" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23]],
                    ["nombre" => "Martes","dia" => 2,"laboral" => true,"horas" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23]],
                    ["nombre" => "Miercoles","dia" => 3,"laboral" => true,"horas" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23]],
                    ["nombre" => "Jueves","dia" => 4,"laboral" => true,"horas" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23]],
                    ["nombre" => "Viernes","dia" => 5,"laboral" => true,"horas" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23]],
                    ["nombre" => "Sábado","dia" => 6,"laboral" => true,"horas" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23]],
                    ["nombre" => "Domingo","dia" => 7,"laboral" => true,"horas" => [8,9,10,11]]
                ],
    "hora_inicio" => 0,
    "hora_final" => 24
];
        $response = $this->call('post', '/api/v1/dias_habiles', $datos, [], [], ['HTTP_Authorization' => 'Bearer ' . $credentials], []);
        $this->assertEquals(200, $response->getStatusCode(), "".$response);
        /// no data test
        $datos=array();
        $response = $this->call('post', '/api/v1/dias_habiles', $datos, [], [], ['HTTP_Authorization' => 'Bearer ' . $credentials], []);
        $this->assertEquals(404, $response->getStatusCode(), "".$response);

    }
 
    public function probar_deshabilitar_fecha()
    {
        $datos['fecha_inhabil_id']=11;
        $credentials = JWTAuth::attempt(['email' => 'cristianrocker93@gmail.com', 'password' => 'nomeacuerdo']);
        $response = $this->call('delete', '/api/v1/fecha_inhabil', $datos, [], [], ['HTTP_Authorization' => 'Bearer ' . $credentials], []);
        $this->assertEquals(403, $response->getStatusCode(), "".$response);
    }
}
