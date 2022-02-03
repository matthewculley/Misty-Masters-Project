<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoryController;


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

Route::get('stories/{id}', [StoryController::class, 'apiShow'])
    ->name('api.stories.show');

Route::get('stories/{id}/reviews', [StoryController::class, 'apiShowReviews'])
    ->name('api.stories.showReviews');

