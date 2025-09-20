<?php

namespace App\Http\Controllers;

use App\Models\ScrollImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        try {
            Log::info($request->all());
            Log::info("so what's up here??");
            // Validate the request
            $request->validate([
                'img_file' => 'required|required|file|mimes:jpg,jpeg,png,webp,gif|max:51200', // 50MB max
                'order' => 'required|integer'
            ]);
    
            $filePath = $request->file('img_file')->store('aboutScroll', 'public');
            $contentUrl = '/storage/' . $filePath;
            
            // Create the video record
            $scrollImage = ScrollImage::create([
                'img_url' => $contentUrl,
                'order' => $request->order
            ]);
            
            return response()->json($scrollImage, 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::info("oops, we raised an error!");
            Log::info($e->errors());
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
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
        // Validate the request
        $request->validate([
            'img_file' => 'sometimes|required|file|mimes:jpg,jpeg,png,webp,gif|max:51200',
            'order' => 'required|integer'
        ]);
        
        // Handle new file upload if provided
        if ($request->hasFile('img_file')) {
            // Delete old file
            $oldFilePath = str_replace('/storage/', '', $scrollImage->content_url);
            Storage::disk('public')->delete($oldFilePath);
            
            // Store new file
            $filePath = $request->file('img_file')->store('aboutScroll', 'public');
            $scrollImage->img_url = '/storage/' . $filePath;
        }
        
        // Update the other fields if provided
        $scrollImage->fill($request->only(['order']));
        $scrollImage->save();
        
        return response()->json($scrollImage);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ScrollImage $scrollImage)
    {
        $deletedOrder = $scrollImage->order;

        $oldFilePath = str_replace('/storage/', '', $scrollImage->content_url);
        Storage::disk('public')->delete($oldFilePath);
        $scrollImage->delete();

        // Reorder all items that came after the deleted item
        DB::transaction(function () use ($deletedOrder) {
            ScrollImage::where('order', '>', $deletedOrder)->whereNotNull('order')->decrement('order');
        });
        return response()->json();
    }
}
