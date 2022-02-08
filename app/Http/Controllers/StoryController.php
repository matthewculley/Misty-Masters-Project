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
        $tagsString = str_replace("_", " ", $t);
        $tagsArray = explode("+", $tagsString);
        $filterTags = [];
        foreach($tagsArray as $t) {
            $tag = Tag::all()->where('tag', $t)->first();
            array_push($filterTags, $tag);
        }
        
        $stories = Story::all();
        $returnStories = [];

        // foreach ($stories as $s) {
        //     if($s->tags()->get()->contains($filterTags->id)) {
        //         array_push($returnStories, $s);
        //     }
        // }
        
        // // $returnStories = [];

        // // $t = $filterTags[0];
        // // $stories = Story::all();

        foreach ($stories as $s) {
            $addToArray = true;
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
        //
    }

    public function createReview($request){
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
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
