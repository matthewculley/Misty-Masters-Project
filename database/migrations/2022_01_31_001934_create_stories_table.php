<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stories', function (Blueprint $table) {
            $table->id()->primary_key();
            $table->timestamps();
            $table->string("title");
            $table->string("description");
            $table->integer("min_suitable_age");
            $table->integer("max_suitable_age");
            $table->integer("min_interactivity");
            $table->integer("max_interactivity");
            $table->integer("times_played");
            $table->string("thumbnail_path");
            $table->string("misty_skill_id")->nullable();
            $table->string("misty_skill_path")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stories');
    }
}
