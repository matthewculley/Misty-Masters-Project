<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Story;
use App\Models\Review;
use App\Models\Tag;
use App\Models\History;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $stories = Story::factory()->count(20)->create();
        $reviews = Review::factory()->count(sizeof($stories)*20)->create();
        $tagsToSeed = ["fun", "exploring", "Suitable for boys", "Suitable for girls", "jungle", "fantasy", "educational", "linear", "fictional", "non-fiction"];

        foreach($reviews as $review) {
            //choose a random story
            $story = Story::inRandomOrder()->first();      
            //attach review to story
            $review->story()->associate($story->id);
            $review->save();
        }

        foreach($tagsToSeed as $tag) {
            $t = new Tag();
            $t->tag = $tag;
            $t->save();
        }

        foreach($stories as $s) {
            for ($i=0; $i<rand(1, 5); $i++) {
                $t = Tag::inRandomOrder()->first();
                if (!$s->tags()->get()->contains($t->id)) {
                    $s->tags()->attach($t->id);
                } 
            }
            $s->save();           
        }

        $histories = History::factory()->count(500)->create();
        foreach($histories as $h) {
            $story = Story::inRandomOrder()->first();
            $h->story()->associate($story->id);
            $h->save();
        }

        // $tag = Tag::all()->first();
        // $tag->stories()->attach(1);
        // $tag->save();
        
    }
}
