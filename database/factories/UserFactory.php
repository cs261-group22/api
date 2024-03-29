<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'is_guest' => $this->faker->boolean(),
            'is_admin' => $this->faker->boolean(),
        ];
    }

    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_admin' => true,
                'is_guest' => false,
            ];
        });
    }

    public function non_admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_admin' => false,
                'is_guest' => false,
            ];
        });
    }

    public function guest()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_admin' => false,
                'is_guest' => true,
            ];
        });
    }
}
