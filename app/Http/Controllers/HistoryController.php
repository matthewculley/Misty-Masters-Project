<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\Story;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    public function apiIndex() {
        $stories_played = DB::table('histories')
            ->join('stories', 'histories.story_id', '=', 'stories.id')
            ->select('title', 'last_played', 'stories.id', 'stories.thumbnail_path')
            ->orderBy('last_played', 'desc')
            ->get();
        return $stories_played;

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    public function apiCreate(Request $request)
    {
        $story_id = $request['id'];
        $story = Story::findOrFail($story_id);
        $story->times_played = $story->times_played + 1;
        $story->save();
        $last_played = $request['last_played'];
        
        $h = new History();
        $h->story_id = $story_id;
        $h->last_played = $last_played;
        $h->save();

        return $story_id;
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
        //
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
