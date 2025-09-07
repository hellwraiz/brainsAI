<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Content::where('type', 'video')->orderBy('order')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Content $video)
    {
        return response()->json($video);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Content $video)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Content $video)
    {
        //
    }
}
