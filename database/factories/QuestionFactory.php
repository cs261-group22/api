<?php

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Question::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $responses = $this->faker->numberBetween($min = 1, $max = 4);
        return [
            'type' => Question::TYPE_FREE_TEXT,
            'order' => 0,
            'prompt' => $this->faker->paragraph(),
            'event_id' => 0,
            'min_responses' => $responses,
            'max_responses' => $responses + $this->faker->numberBetween($min = 0, $max = 2),
        ];
    }
}
