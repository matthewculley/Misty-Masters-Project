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

            $max_interactivity = rand(1, 5);
            $min_interactivity = rand(0, $max_interactivity);
            $max_suitable_age = rand(2, 15);
            $min_suitable_age = rand(1, $max_suitable_age);

        return [
            'title' => $this->faker->realText(rand(10, 30)),
            'description' => $this->faker->realText(rand(25, 100)),
            'times_played' => $this->faker->numberBetween($min = 0, $max = 200),
            'min_suitable_age' => $min_suitable_age,
            'max_suitable_age' => $max_suitable_age,
            'min_interactivity' => $min_interactivity,
            'max_interactivity' => $max_interactivity,

            
        ];
    }
}
