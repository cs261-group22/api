<?php

namespace Database\Factories;

use App\Models\Session;
use Illuminate\Database\Eloquent\Factories\Factory;

class SessionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Session::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'mood' => $this->faker->$this->faker->numberBetween($min = 1, $max = 20),
            'user_id' => 0,
            'event_id' => 0,
            'started_at' => now(),
            'is_submitted' => $this->faker->boolean(),
        ];
    }

    public function submitted()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_submitted' => true,
            ];
        });
    }

    public function non_submitted()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_submitted' => false,
            ];
        });
    }
}
