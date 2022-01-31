<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->realText(rand(10, 30)),
            'description' => $this->faker->realText(rand(25, 100)),
            'times_played' => $this->faker->numberBetween($min = 0, $max = 200)
        ];
    }
}
