<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/work', function () {
    return view('welcome');
});

Route::get('/shorts', function () {
    return view('welcome');
});

Route::get('/about', function () {
    return view('welcome');
});

Route::get('/contact', function () {
    return view('welcome');
});

Route::apiResource('mains', App\Http\Controllers\MainController::class);
