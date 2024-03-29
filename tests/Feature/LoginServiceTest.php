<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A test for logging in as admin.
     *
     * @return void
     */
    public function testSuccessfulAdminLogin()
    {
        $email = 'admin@example.com';
        $password = 'password';

        $user = User::factory()->admin()->create([
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $response = $this->postJson(
            '/api/v1/login/employee',
            [
                'email' => $email,
                'password' => $password,
                'recaptcha-response' => config('cs261.test.recaptcha_override'),
            ]
        );

        $response->assertStatus(200)->assertJsonStructure(['token']);
        $token = $response['token'];

        $validResponse = $this->withHeader('Authorization', 'Bearer '.$token)->get('/api/v1/users');
        $validResponse->assertStatus(200);
    }

    /**
     * A test for logging in as admin with wrong password.
     *
     * @return void
     */
    public function testFailedPasswordAdminLogin()
    {
        $email = 'admin@example.com';
        $password = 'password';

        $user = User::factory()->admin()->create([
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $response = $this->postJson(
            '/api/v1/login/employee',
            [
                'email' => $email,
                'password' => $password.'1',
                'recaptcha-response' => config('cs261.test.recaptcha_override'),
            ]
        );

        $response->assertStatus(401);
    }

    /**
     * A test for logging in as admin with wrong email.
     *
     * @return void
     */
    public function testFailedEmailAdminLogin()
    {
        $email = 'admin@example.com';
        $password = 'password';

        $user = User::factory()->admin()->create([
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $response = $this->postJson(
            '/api/v1/login/employee',
            [
                'email' => $email.'1',
                'password' => $password,
                'recaptcha-response' => config('cs261.test.recaptcha_override'),
            ]
        );

        $response->assertStatus(401);
    }

    /**
     * A test for logging in as non admin.
     *
     * @return void
     */
    public function testSuccessfulNonAdminLogin()
    {
        $email = 'admin@example.com';
        $password = 'password';

        $user = User::factory()->non_admin()->create([
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $response = $this->postJson(
            '/api/v1/login/employee',
            [
                'email' => $email,
                'password' => $password,
                'recaptcha-response' => config('cs261.test.recaptcha_override'),
            ]
        );

        $response->assertStatus(200)->assertJsonStructure(['token']);
        $token = $response['token'];

        $validResponse = $this->withHeader('Authorization', 'Bearer '.$token)->get('/api/v1/users');
        $validResponse->assertStatus(403);
    }

    /**
     * A test for logging in as non admin with wrong password.
     *
     * @return void
     */
    public function testFailedPasswordNonAdminLogin()
    {
        $email = 'admin@example.com';
        $password = 'password';

        $user = User::factory()->non_admin()->create([
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $response = $this->postJson(
            '/api/v1/login/employee',
            [
                'email' => $email,
                'password' => $password.'1',
                'recaptcha-response' => config('cs261.test.recaptcha_override'),
            ]
        );

        $response->assertStatus(401);
    }

    /**
     * A test for logging in as non admin with wrong email.
     *
     * @return void
     */
    public function testFailedEmailNonAdminLogin()
    {
        $email = 'admin@example.com';
        $password = 'password';

        $user = User::factory()->non_admin()->create([
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $response = $this->postJson(
            '/api/v1/login/employee',
            [
                'email' => $email.'1',
                'password' => $password,
                'recaptcha-response' => config('cs261.test.recaptcha_override'),
            ]
        );

        $response->assertStatus(401);
    }

    /**
     * A test for logging in as guest.
     *
     * @return void
     */
    public function testSuccessfulGuestLogin()
    {
        $response = $this->post(
            '/api/v1/login/guest',
            ['recaptcha-response' => config('cs261.test.recaptcha_override')]
        );

        $response->assertStatus(200)->assertJsonStructure(['token']);
        $token = $response['token'];

        $validResponse = $this->withHeader('Authorization', 'Bearer '.$token)->get('/api/v1/users');
        $validResponse->assertStatus(403);
    }
}
