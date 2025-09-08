<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        Log::info('');
        Log::info('check this saontehu out');

        Log::info($request->all());
        // Validate the request
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string|in:short', // or whatever types you have
            'video_file' => 'required|file|mimes:mp4,mov,avi|max:51200', // 50MB max
            'order' => 'required|integer'
        ]);
        
        $filePath = $request->file('video_file')->store('content', 'public');
        $contentUrl = '/storage/' . $filePath;
        
        // Create the video record
        $reel = Content::create([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'content_url' => $contentUrl,
            'order' => $request->order
        ]);
        
        return response()->json($reel, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Content $reel)
    {
        return response()->json($reel);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Content $reel)
    {
        
        // Validate the request
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string|in:short',
            'video_file' => 'sometimes|required|file|mimes:mp4,mov,avi|max:51200',
            'order' => 'required|integer'
        ]);
        
        // Handle new file upload if provided
        if ($request->hasFile('video_file')) {
            // Delete old file
            $oldFilePath = str_replace('storage/', 'public/', $reel->content_url);
            Storage::delete($oldFilePath);
            
            // Store new file
            $filePath = $request->file('video_file')->store('content', 'public');
            $reel->content_url = '/storage/' . $filePath;
        }
        
        // Update the other fields if provided
        $reel->fill($request->only(['title', 'description', 'type', 'order']));
        $reel->save();
        
        return response()->json($reel);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Content $reel)
    {
        $deletedOrder = $reel->order;
        $type = $reel->type;
        
        $filePath = str_replace('storage/', 'public/', $reel->img_url);
        Storage::delete($filePath);
        $reel->delete();

        // Reorder all items that came after the deleted item
        DB::transaction(function () use ($deletedOrder, $type) {
            Content::where('type', 'short')->where('order', '>', $deletedOrder)->decrement('order');
        });
        return response()->json();
    }
}
