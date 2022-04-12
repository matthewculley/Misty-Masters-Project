<?php

namespace App\Http\Controllers;
use App\Models\Story;
use App\Models\Review;
use App\Models\Tag;
use App\Models\History;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stories = Story::all();
        return view('stories.index', ['stories'=>$stories]);
    }

    public function apiIndex(){
        return Story::all();
    }

    public function apiIndexTags($t) {
        $tagsString = str_replace("_", " ", $t); //string with all tags 
        $tagsArray = explode("+", $tagsString); // serparate into array of strings
        $filterTags = [];
        //foreach tag, find the tag model, and add to array
        foreach($tagsArray as $t) {
            $tag = Tag::all()->where('tag', $t)->first();
            array_push($filterTags, $tag);
        }
        
        $stories = Story::all();
        $returnStories = [];

        //for each story
        foreach ($stories as $s) {
            $addToArray = true;
            //check if each user supplied tag is attached to the story
            //only add the story to the return array if all tags match
            foreach ($filterTags as $t) {
                if (!$s->tags()->get()->contains($t->id)) {
                    $addToArray = false;
                }
            }

            if ($addToArray == true) {
                array_push($returnStories, $s);
            }
            
        }
        return $returnStories;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('stories.add');
    }

    public function apiCreate(Request $request) {
       
        $story = new Story();
        $story->title = $request['title'];
        $story->description = $request['description'];
        $story->min_interactivity = $request['min_interactivity'];
        $story->max_interactivity = $request['max_interactivity'];
        $story->min_suitable_age = $request['min_suitable_age'];
        $story->max_suitable_age = $request['max_suitable_age'];
        $story->times_played = 0;
        $story->thumbnail_path = "img/".$request->file('thumb')->store('');
        $story->misty_skill_id = $request['unique_id'];
        $story->misty_skill_path = "skills/".$request->file('skill')->store('');
      
        
        $story->save();


        $tags = explode(',', $request['tags']);
        foreach($tags as $t) {
            $tag = null;
            $tag = Tag::where("tag", $t)->get()->first();
            if ($tag != null) {
                $story->tags()->attach($tag->id);
            }
        }
        $story->save();

        return response()->json([
            'story' => $request,
        ]);
    }

    public function apiCreateReview(Request $request){
        $review = new Review();
        $review->review = $request['review'];
        $review->rating = $request['rating'];
        $review->story_id = $request['story_id'];
        $review->save();
        return $review;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $story = Story::findOrFail($id);
        return view('stories.show', ['story' => $story]);
    }

    public function apiShow($id)
    {
        $story = Story::findOrFail($id);
        return $story;
    }

    public function apiShowReviews($id) {
        $reviews = Review::all()->where('story_id', $id); 
        return $reviews;
    }

    public function apiShowHistories($id) {
        $histories = DB::table('histories')->where('story_id', $id)->orderBy('last_played', 'desc')->get();
        return $histories;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $story = Story::findOrFail($id);
        return view('stories.edit', ['story' => $story]); 
    }

    public function apiEdit(Request $request)
    {   
        //find old story and delete itf
        $story = Story::findOrFail($request['id']);  
        
        //add new story
        $story->title = $request['title'];
        $story->description = $request['description'];
        $story->min_interactivity = $request['min_interactivity'];
        $story->max_interactivity = $request['max_interactivity'];
        $story->min_suitable_age = $request['min_suitable_age'];
        $story->max_suitable_age = $request['max_suitable_age'];
        $story->times_played = 0;
        $story->misty_skill_id = $request['unique_id'];

        if (!$request['skill'] == -1) {
            $story->thumbnail_path = "img/".$request->file('thumb')->store('');
        }

        if (!$request['skill'] == -1) {
            $story->thumbnail_path = "skills/".$request->file('skill')->store('');
        }

        // SQLSTATE[HY000]: General error: 1364 Field 'thumbnail_path' doesn't have a default value (SQL: insert into `stories` (`title`, `description`, `min_interactivity`, `max_interactivity`, `min_suitable_age`, `max_suitable_age`, `times_played`, `misty_skill_id`, `updated_at`, `created_at`) values (Testessa, dsfsdf, 1, 3, 12, 11, 0, undefined, 2022-04-12 14:50:32, 2022-04-12 14:50:32))

        $story->tags()->detach();

        $tags = explode(',', $request['tags']);
        foreach($tags as $t) {
            $tag = null;
            $tag = Tag::where("tag", $t)->get()->first();
            if ($tag != null) {
                $story->tags()->attach($tag->id);
            }
        }

        $story->save();

        return $story;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update( $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}