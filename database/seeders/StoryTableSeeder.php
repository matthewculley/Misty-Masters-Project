<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $story = new Story();
        $story->title = "The Very Hungry Caterpillar";
        $story->desctiption = "a very hungry caterpillar who eats his way through a wide variety of foodstuffs before pupating and emerging as a butterfly.";
        $story->times_played = 0;
        $story->save();

    }
}
