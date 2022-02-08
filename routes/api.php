<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\HistoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('stories', [StoryController::class, 'apiIndex'])
    ->name('api.stories.index');

Route::get('stories/tags/{tags}', [StoryController::class, 'apiIndexTags'])
    ->name('api.stories.getStoriesWithTags');

Route::get('stories/{id}', [StoryController::class, 'apiShow'])
    ->name('api.stories.show');

Route::get('stories/{id}/reviews', [StoryController::class, 'apiShowReviews'])
    ->name('api.stories.showReviews');
    
Route::get('stories/{id}/history', [StoryController::class, 'apiShowHistories'])
    ->name('api.stories.showHistories');

Route::post('stories/{id}/addReview', [StoryController::class, 'apiCreateReview'])
    ->name('api.stories.createReview');

Route::get('tags', [TagController::class, 'apiIndex'])
    ->name('api.tags.index');

Route::get('misty/stories_played', [HistoryController::class, 'apiIndex']) 
    ->name('api.misty.stories_played');



