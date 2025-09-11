<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\ShortController;
use App\Http\Controllers\ImageController;

require __DIR__.'/auth.php';
require __DIR__.'/api.php';

Route::get('/', function () {return view('welcome');});
Route::get('/work', function () {return view('welcome');});
Route::get('/shorts', function () {return view('welcome');});
Route::get('/about', function () {return view('welcome');});
Route::get('/contact', function () {return view('welcome');});
Route::get('/admin', function () {return view('welcome');});
Route::get('/loginAdmin', function () {return view('welcome');});


Route::get('/videos/{id}/stream', [VideoController::class, 'stream']);
Route::apiResource('videos', VideoController::class);
Route::apiResource('reels', ShortController::class);
Route::get('/reels/{id}/stream', [ShortController::class, 'stream']);
Route::apiResource('scrollImages', ImageController::class);
