<?php

namespace App\Http\Controllers;
use App\Models\Story;
use App\Models\Review;
use App\Models\Tag;
use App\Models\History;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


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
    }

    public function apiCreateReview(Request $request){
        $validated = $request->validate([
            'review' => 'nullable|string',
            'rating' => 'required|integer|min:1|max:5',
            'story_id' => 'required|integer',
        ]);

        if ($validated['rating'] == 1 && strlen($validated['review'] == 0)) {
            // return response()->
            return response([
                'code' => '500',
                'message' => 'A review must be left when the rating is 1',
            ], 
            500)->header('Content-Type', 'text/plain');
        }

        $review = new Review();
        $review->review = $validated['review'];
        $review->rating = $validated['rating'];
        $review->story_id = $validated['story_id'];
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

    public function apiShowTags($id) {
        $story = Story::findOrFail($id);
        $tags = [];
        $allTags = $story->tags;
        
        return $story->tags;
    }

    public function downloadSkill($id) {
        $story = Story::findOrFail($id);

        $file = Storage::disk('public')->get($story->thumbnail_path);

        return response()->json([
            'thumbnail_path' => 'Abigail',
            'state' => 'CA',
        ]);

        // // return response()->json([
        // //     'name' => 'Abigail',
        // //     'state' => $file,
        // // ]);
        
        // return (new Response($file, 200))->header('Content-Type', 'image/jpeg');


        // $headers = array(
        //     'Content-Type: image/png',
        // );
        // // return response()->file($file, $headers);

        // return response()->download($file, "test", $headers);

       


        // $headers = array(
        //         'Content-Type: application/pdf',
        //         );

        // return Response::download($file, 'filename.pdf', $headers);
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

    public function apiUpdatePlayCounter($id) {
        $story = Story::findOrFail($request['id']);  
        $story->times_played = $story->times_played + 1;
        story->save();
    }

    public function apiEdit(Request $request)
    {   
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'min_suitable_age' => 'required|integer|min:1|max:15',
            'max_suitable_age' => 'required|integer|min:1|max:15',
            'min_interactivity' => 'required|integer|min:1|max:5',
            'max_interactivity' => 'required|integer|min:1|max:5',
            'misty_skill_id' => 'required|string',
            'thumb' => 'nullable|mimes:jpg,bmp,png',
            'skill' => 'nullable|mimes:zip',

        ]);

        //find old story 
        $story = Story::findOrFail($request['id']);

        $story->title = $validated['title'];
        $story->description = $validated['description'];
        $story->min_interactivity = $validated['min_interactivity'];
        $story->max_interactivity = $validated['max_interactivity'];
        $story->min_suitable_age = $validated['min_suitable_age'];
        $story->max_suitable_age = $validated['max_suitable_age'];
        $story->misty_skill_id = $validated['misty_skill_id'];

        if (array_key_exists('thumb', $validated)) {
            $story->thumbnail_path = "img/".$validated->file('thumb')->store('');
        } 

        if (array_key_exists('skill', $validated)) {
            $story->misty_skill_path = "img/".$validated->file('skill')->store('');
        } 
        
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