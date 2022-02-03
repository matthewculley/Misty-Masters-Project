<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'review' => $this->faker->realText(rand(30, 200)),
            'rating' => $this->faker->numberBetween($min = 0, $max = 5),            
        ];
    }
}
