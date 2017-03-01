<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Seeder;
use Illuminate\Http\Response as HttpResponse;
use App\User;
use App\cupon;

class CalendarioTest extends TestCase
{
    use DatabaseTransactions;

       /** @test */
    public function probarindex()
    {
        $response = $this->call('get', '/api/v1/dashboard', [], [], [], [], []);
        $this->assertEquals(400, $response->getStatusCode(), ''.$response);
        $credentials = JWTAuth::attempt(['email' => 'test@gmail.com', 'password' => '123456']);
        $response = $this->call('get', '/api/v1/dashboard', [], [], [], ['HTTP_Authorization' => 'Bearer ' . $credentials], []);
        $this->assertEquals(200, $response->getStatusCode(), ''.$response);
////sin token 400
    }
}
