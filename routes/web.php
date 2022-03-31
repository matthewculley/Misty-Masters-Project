<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoryController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('stories.index');
});

Route::get('/stories', [StoryController::class, 'index'])
    ->name('stories.index');

Route::get('/stories/{id}', [StoryController::class, 'show'])
    ->name('stories.show');

Route::get('/add', [StoryController::class, 'show'])
    ->name('stories.add');

Route::get('/misty', function (){
    return view('misty.show');
});
    