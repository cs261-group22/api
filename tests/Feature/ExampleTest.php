<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * @coversNothing
 */
class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $email = 'admin@example.com';
        $password = 'password';

        $user = User::factory()->admin()->create([
            'email' => $email,
            'password' => Hash::make($password)
        ]);

        $response = $this->postJson(route('login.employee'), ['email' => $email, 'password' => $password]);

        $response->dump();

        $this->assertTrue(true);
    }
}
