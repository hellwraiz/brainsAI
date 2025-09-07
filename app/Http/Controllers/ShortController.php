<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;

class ShortController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Content::where('type', 'short')->orderBy('order')->get());
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
    public function show(Content $short)
    {
        return response()->json($short);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Content $short)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Content $short)
    {
        //
    }
}
