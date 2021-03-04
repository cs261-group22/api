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
            'ends_at' => now(),
            'is_draft' => $this->faker->boolean(),
            'starts_at' => now(),
            'description' => $this->faker->paragraph(),
            'allow_guests' => $this->faker->boolean(),
            'max_sessions' => $this->faker->numberBetween($min = 1, $max = 20),
        ];
    }
}
