<?php

namespace App\Http\Controllers;

use App\Models\ScrollImage;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(ScrollImage::orderBy('order')->get());
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
    public function show(ScrollImage $scrollImage)
    {
        return response()->json($scrollImage);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ScrollImage $scrollImage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ScrollImage $scrollImage)
    {
        //
    }
}
