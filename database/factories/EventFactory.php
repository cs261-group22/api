<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->catchPhrase(),
            'code' => Event::generateUniqueEventCode(),
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
            'is_draft' => $this->faker->boolean(),
            'description' => $this->faker->paragraph(),
            'allow_guests' => $this->faker->boolean(),
            'max_sessions' => $this->faker->numberBetween($min = 1, $max = 20),
        ];
    }


    public function draft()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_draft' => true
            ];
        });
    }
}
