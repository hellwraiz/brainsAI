<?php

use Illuminate\Support\Facades\Route;


require __DIR__.'/auth.php';
require __DIR__.'/api.php';

Route::get('/', function () {return view('welcome');});
Route::get('/work', function () {return view('welcome');});
Route::get('/shorts', function () {return view('welcome');});
Route::get('/about', function () {return view('welcome');});
Route::get('/contact', function () {return view('welcome');});
Route::get('/admin', function () {return view('welcome');});
Route::get('/loginAdmin', function () {return view('welcome');});


Route::apiResource('videos', App\Http\Controllers\VideoController::class);
Route::apiResource('reels', App\Http\Controllers\ShortController::class);
Route::apiResource('scrollImages', App\Http\Controllers\ImageController::class);
