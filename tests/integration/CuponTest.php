<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Seeder;
use Illuminate\Http\Response as HttpResponse;
use App\User;
use App\cupon;

class CuponTest extends TestCase
{
    use DatabaseTransactions;

  
    public function crear_cupon()
    {

        ///peticion correcta
        $datoscupon['servicio_id']=17;
        $datoscupon['word_key']='';
        $datoscupon['porcentaje']=20;
        $datoscupon['fecha_inicial']='2017-03-23';
        $datoscupon['fecha_final']='2017-04-20';

        $credentials = JWTAuth::attempt(['email' => 'cristianrocker93@gmail.com', 'password' => 'nomeacuerdo']);
        $response = $this->call('post', '/api/v1/cupon', $datoscupon, [], [], ['HTTP_Authorization' => 'Bearer ' . $credentials], []);
        $this->assertEquals(404, $response->getStatusCode(), ''.$response);

        $datoscupon['servicio_id']=2;
        $credentials = JWTAuth::attempt(['email' => 'test@gmail.com', 'password' => '123456']);
        $response = $this->call('post', '/api/v1/cupon', $datoscupon, [], [], ['HTTP_Authorization' => 'Bearer ' . $credentials], []);
        $this->assertEquals(200, $response->getStatusCode(), ''.$response);
                //acceso a recurso inexistente
              $datoscupon['servicio_id']=2000;
        $response = $this->call('post', '/api/v1/cupon', $datoscupon, [], [], ['HTTP_Authorization' => 'Bearer ' . $credentials], []);
        $this->assertEquals(404, $response->getStatusCode(), ''.$response);
        $datoscupon['word_key']='limpieza2br6';
        $response = $this->call('post', '/api/v1/cupon', $datoscupon, [], [], ['HTTP_Authorization' => 'Bearer ' . $credentials], []);
        $this->assertEquals(404, $response->getStatusCode(), ''.$response);
        $datoscupon['word_key']='limpieza2br6';
        $response = $this->call('post', '/api/v1/cupon', $datoscupon, [], [], ['HTTP_Authorization' => 'Bearer ' . $credentials], []);
        $this->assertEquals(404, $response->getStatusCode(), ''.$response);
        //fallo del formulario

        $datoscupon['porcentaje']=101;
        $response = $this->call('post', '/api/v1/cupon', $datoscupon, [], [], ['HTTP_Authorization' => 'Bearer ' . $credentials], []);
        $this->assertEquals(400, $response->getStatusCode(), ''.$response);

        $datoscupon['fecha_inicial']='2017-03-23';
        $datoscupon['fecha_final']='2017-02-20';
        $response = $this->call('post', '/api/v1/cupon', $datoscupon, [], [], ['HTTP_Authorization' => 'Bearer ' . $credentials], []);
        $this->assertEquals(400, $response->getStatusCode(), ''.$response);
        //
                $datoscupon=array();
        $response = $this->call('post', '/api/v1/cupon', $datoscupon, [], [], ['HTTP_Authorization' => 'Bearer ' . $credentials], []);
        $this->assertEquals(400, $response->getStatusCode(), ''.$response);
    }

    /**
     * User may want to login, but using wrong credentials.
     * This route should be free for all unauthenticated users.
     * Users should be warned when login fails
     */
    public function LoginWithWrongData()
    {
        // as a user, I wrongly type my email and password
        $data = ['email' => 'email', 'password' => 'password'];
        // and I submit it to the login api
        $response = $this->call('POST', 'login', $data);
        // I shouldnt be able to login with wrong data
        $this->assertEquals(HttpResponse::HTTP_UNAUTHORIZED, $response->status());
    }

    /**
     * User may want to login.
     * This route should be free for all unauthenticated users.
     * User should receive an JWT token
     */
    public function LoginSuccesfull()
    {
        // as a user, I wrongly type my email and password
        $data = ['email' => 'admin@app.com', 'password' => 'secret'];
        // and I submit it to the login api
        $response = $this->call('POST', 'login', $data);
        // I should be able to login
        $this->assertEquals(HttpResponse::HTTP_ACCEPTED, $response->status());
        // assert there is a TOKEN on the response
        $content = json_decode($response->getContent());
        $this->assertObjectHasAttribute('token', $content);
        $this->assertNotEmpty($content->token);
    }
  
    public function verificar_validez_cupon()
    {
        $credentials = JWTAuth::attempt(['email' => 'test@gmail.com', 'password' => '123456']);
        $datoscupon['codigo']='minutero9ymv';
        $datoscupon['id_calendario']=3;
        $datoscupon['servicio_id']=6;
        $response = $this->call('get', '/api/v1/verificarcupon', $datoscupon, [], [], ['HTTP_Authorization' => 'Bearer ' . $credentials], []);
        $this->assertEquals(200, $response->getStatusCode(), ''.$response);
        //////CUPON NO EXISTE
        $datoscupon['codigo']='asdasdas';
        $response = $this->call('get', '/api/v1/verificarcupon', $datoscupon, [], [], ['HTTP_Authorization' => 'Bearer ' . $credentials], []);
        $this->assertEquals(404, $response->getStatusCode(), ''.$response);
        //////no es valido para el servicio
        $datoscupon['servicio_id']=4;
        $datoscupon['codigo']='minutero9ymv';
                 $response = $this->call('get', '/api/v1/verificarcupon', $datoscupon, [], [], ['HTTP_Authorization' => 'Bearer ' . $credentials], []);
        $this->assertEquals(404, $response->getStatusCode(), ''.$response);
        ///el cupon no es valido en esa fecha
                $datoscupon['servicio_id']=6;
        $datoscupon['codigo']='minutero1lsw';
                         $response = $this->call('get', '/api/v1/verificarcupon', $datoscupon, [], [], ['HTTP_Authorization' => 'Bearer ' . $credentials], []);
        $this->assertEquals(404, $response->getStatusCode(), ''.$response);
    }
    public function obtener_servicios_y_cupones()
    {
        
        ///peticion correcta
        $datoscupon['calendario_id']=3;
        $credentials = JWTAuth::attempt(['email' => 'test@gmail.com', 'password' => '123456']);
        $response = $this->call('get', '/api/v1/cupon', $datoscupon, [], [], ['HTTP_Authorization' => 'Bearer ' . $credentials], []);
        $this->assertEquals(200, $response->getStatusCode(), ''.$response);
                        ///calendario nulo
        $datoscupon['calendario_id']=20;
        $credentials = JWTAuth::attempt(['email' => 'test@gmail.com', 'password' => '123456']);
        $response = $this->call('get', '/api/v1/cupon', $datoscupon, [], [], ['HTTP_Authorization' => 'Bearer ' . $credentials], []);
        $this->assertEquals(404, $response->getStatusCode(), ''.$response);
                        ///peticion correcta
        $datoscupon['calendario_id']=4;
        $credentials = JWTAuth::attempt(['email' => 'test@gmail.com', 'password' => '123456']);
        $response = $this->call('get', '/api/v1/cupon', $datoscupon, [], [], ['HTTP_Authorization' => 'Bearer ' . $credentials], []);
        $this->assertEquals(404, $response->getStatusCode(), ''.$response);
    }
}
