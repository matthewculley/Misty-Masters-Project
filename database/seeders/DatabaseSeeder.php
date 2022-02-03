<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Story;
use App\Models\Review;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $stories = Story::factory()->count(10)->create();
        
        $reviews = Review::factory()->count(sizeof($stories)*20)->create();

        foreach($reviews as $review) {
            //choose a random story
            $story = Story::inRandomOrder()->first();      
            //attach review to story
            $review->story()->associate($story->id);
            $review->save();
        }
        
    }
}
