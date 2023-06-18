<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    private function register()
    {
        $response = $this->post(
            '/api/register',
            [
                'name' => 'Testing User',
                'email' => 'user@testing.com',
                'password' => '12345678',
            ]
        );

        $this->app->get('auth')->forgetGuards();

        return $response;
    }

    public function testRegister()
    {
        $response = $this->post(
            '/api/register',
            [
                'name' => 'Testing User',
                'email' => 'user@testing.com',
                'password' => '12345678',
            ]
        );

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'user' => ['name', 'email', 'id'],
            'access_token',
            'token_type',
        ]);

        $response->assertJsonPath('user.id', 1);
        $response->assertJsonPath('user.name', 'Testing User');
        $response->assertJsonPath('user.email', 'user@testing.com');
        $response->assertJsonPath('token_type', 'Bearer');

        $this->assertDatabaseHas('users', ['name' => 'Testing User', 'email' => 'user@testing.com']);

        $this->app->get('auth')->forgetGuards();

        return $response;
    }

    public function testLoginAndLogout()
    {
        //Register format incorrect

        $response = $this->post(
            '/api/register',
            [
                'name' => 'Testing User',
                'email' => 'user',
                'password' => '12345678',
            ]
        );

        $response->assertStatus(400);

        //Register

        $response = $this->register();

        //Login format incorrect

        $response = $this->post('/api/login', [
            'email' => 'user',
            'password' => '12345678',
        ]);

        $response->assertStatus(400);

        $this->app->get('auth')->forgetGuards();

        //Login unauthenticated

        $response = $this->post('/api/login', [
            'email' => 'user@testing.com',
            'password' => '123456789',
        ]);

        $response->assertStatus(401);

        $this->app->get('auth')->forgetGuards();

        //Login

        $response = $this->post('/api/login', [
            'email' => 'user@testing.com',
            'password' => '12345678',
        ]);

        $response->assertStatus(200);

        $this->app->get('auth')->forgetGuards();

        //Get token

        $token = $response['access_token'];

        //Logout

        $response = $this->post('/api/logoutall', [], ['Authorization' => 'Bearer '.$token]);

        $response->assertStatus(200);

        $response->assertJsonPath('message', 'Successful logout');

        $this->app->get('auth')->forgetGuards();
    }
}
