<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Story;

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
    }
}
