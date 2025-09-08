<?php

namespace App\Http\Controllers;

use App\Models\ScrollImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

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
        Log::info('');
        // Validate the request
        $request->validate([
            'img_file' => 'required|required|file|mimes:jpg,jpeg,png,webp,gif|max:51200', // 50MB max
            'order' => 'required|integer'
        ]);
        
        $filePath = $request->file('video_file')->store('videos', 'public');
        $contentUrl = '/storage/' . $filePath;
        
        // Create the video record
        $video = ScrollImage::create([
            'img_url' => $contentUrl,
            'order' => $request->order
        ]);
        
        return response()->json($video, 201);
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
            $oldFilePath = str_replace('storage/', 'public/', $scrollImage->content_url);
            Storage::delete($oldFilePath);
            
            // Store new file
            $filePath = $request->file('video_file')->store('videos', 'public');
            $scrollImage->content_url = '/storage/' . $filePath;
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

        $filePath = str_replace('storage/', 'public/', $scrollImage->content_url);
        Storage::delete($filePath);
        $scrollImage->delete();

        // Reorder all items that came after the deleted item
        DB::transaction(function () use ($deletedOrder) {
            ScrollImage::where('order', '>', $deletedOrder)->decrement('order');
        });
        return response()->json();
    }
}
