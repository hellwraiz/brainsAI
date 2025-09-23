<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\ShortController;
use App\Http\Controllers\ImageController;
use App\Models\FormInput;
use App\Models\Text;
use App\Mail\FormInputEmail;
use Illuminate\Http\Request;

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



Route::post('/submit-form', function (Request $request) {
    // 1. Process the incoming request
    // 2. Validate the data
    $validatedData = $request->validate([
        'name' => 'required|string',
        'email' => 'required|email',
        'phone' => 'nullable|string',
        'message' => 'nullable|string|max:500',
        'files.*' => 'nullable|file' // Assuming you handle image upload
    ]);

    $filePaths = [];

    if ($request->hasFile('files')) {
        foreach ($request->file('files') as $file) {
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = time() . '_' . $originalName . '.' . $file->getClientOriginalExtension();

            $path = $file->storeAs('uploads', $fileName);
            $filePaths[] = $path;
        }
    }


    $validatedData['files'] = $filePaths;


    FormInput::create($validatedData);

    Mail::to($validatedData['email'])->send(new FormInputEmail($validatedData));

    return response()->json(['message' => 'Form submitted successfully!']);
});


Route::put('/texts/{text}', function (Request $request, Text $text) {
    
    $validated = $request->validate([
        'content' => 'required|string',
    ]);
    
    $text->update($validated);
    
    return response()->json($text);
});
Route::get('/texts/', function () {
    return response()->json(Text::all());
});