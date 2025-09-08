<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

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
        // Validate the request
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string|in:video', // or whatever types you have
            'video_file' => 'required|file|mimes:mp4,mov,avi|max:51200', // 50MB max
            'order' => 'required|integer'
        ]);
        
        $filePath = $request->file('video_file')->store('videos', 'public');
        $contentUrl = '/storage/' . $filePath;
        
        // Create the video record
        $video = Content::create([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'content_url' => $contentUrl,
            'order' => $request->order
        ]);
        return response()->json($video, 201);
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

        
        // Validate the request
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string|in:video',
            'video_file' => 'sometimes|required|file|mimes:mp4,mov,avi|max:51200',
            'order' => 'required|integer'
        ]);
        
        // Handle new file upload if provided
        if ($request->hasFile('video_file')) {
            // Delete old file
            $oldFilePath = str_replace('storage/', 'public/', $video->content_url);
            Storage::delete($oldFilePath);
            
            // Store new file
            $filePath = $request->file('video_file')->store('videos', 'public');
            $video->content_url = '/storage/' . $filePath;
        }
        
        // Update the other fields if provided
        $video->fill($request->only(['title', 'description', 'type', 'order']));
        $video->save();
        
        return response()->json($video);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Content $video)
    {
        $deletedOrder = $video->order;

        $filePath = str_replace('storage/', 'public/', $video->content_url);
        Storage::delete($filePath);
        $video->delete();

        // Reorder all items that came after the deleted item
        DB::transaction(function () use ($deletedOrder) {
            Content::where('type', 'video')->where('order', '>', $deletedOrder)->decrement('order');
        });
        return response()->json();
    }
}
