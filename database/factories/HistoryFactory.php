<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class HistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'last_played' => $this->faker->dateTimeThisYear(),
        ];
    }
}
