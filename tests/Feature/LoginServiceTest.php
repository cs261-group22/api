<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginServiceTest extends TestCase
{

    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testSuccessfulAdminLogin()
    {
        $email = 'admin@example.com';
        $password = 'password';

        $user = User::factory()->admin()->create([
            'email' => $email,
            'password' => Hash::make($password)
        ]);
        $response = $this->postJson('/api/v1/login/employee', ['email' => $email, 'password' => $password]);
        $response->assertStatus(200)->assertJsonStructure(['token']);
        $token = $response['token'];

        $validResponse = $this->withHeader('Authorization', 'Bearer ' . $token)->get('/api/v1/users');
        $validResponse->assertStatus(200);
    }

}
