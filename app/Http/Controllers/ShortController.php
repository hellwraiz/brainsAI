<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Log;

class ShortController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Content::where('isVideo', 'false')->orderBy('order')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            Log::info($request->all());
            Log::info('are we here?');
            // Validate the request
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'isVideo' => 'required|in:false',
                'isLocal' => 'required|in:true,false',
                'video_file' => 'required_if:isLocal,true|file|mimes:mp4,mov,avi|max:51200', // 50MB max
                'video_url' => 'required_unless:isLocal,true|url|max:511',
                'order' => 'required|integer'
            ]);
            
            if ($request->isLocal === 'true') {
                $filePath = $request->file('video_file')->store('content', 'public');
                $contentUrl = '/storage/' . $filePath;

            } else {
                $contentUrl = $request->video_url;
            }
            
            // Create the video record
            $reel = Content::create([
                'title' => $request->title,
                'description' => $request->description,
                'isVideo' => $request->isVideo,
                'isLocal' => $request->isLocal,
                'content_url' => $contentUrl,
                'order' => $request->order
            ]);
            return response()->json($reel, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::info('oh oops, we raised an error!!!');
            Log::info($e->errors());
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
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
        try {
            Log::info('we are the short!!!');
            Log::info($request->all());
            // Validate the request
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'isVideo' => 'required|in:false',
                'isLocal' => 'required|in:true,false',
                'video_file' => 'sometimes|required_if:isLocal,true|file|mimes:mp4,mov,avi|max:51200', // 50MB max
                'video_url' => 'sometimes|required_unless:isLocal,true|url|max:511',
                'order' => 'required|integer'
            ]);
            
            // Handle new file upload if provided
            if ($request->isLocal && $reel->isLocal && $request->hasFile('video_file')) {
                // Delete old file
                $oldFilePath = str_replace('storage/', 'public/', $reel->content_url);
                Storage::delete($oldFilePath);
                
                // Store new file
                $filePath = $request->file('video_file')->store('content', 'public');
                $reel->content_url = '/storage/' . $filePath;
            } else if ($request->isLocal && ! $reel->isLocal && $request->hasFile('video_file')) {
                // Store new file
                $filePath = $request->file('video_file')->store('content', 'public');
                $reel->content_url = '/storage/' . $filePath;
            } else if (! $request->isLocal && $reel->isLocal && $request->video_url) {
                // Delete old file
                $oldFilePath = str_replace('storage/', 'public/', $reel->content_url);
                Storage::delete($oldFilePath);
                
                $reel->content_url = $request->video_url;
            } else if (! $request->isLocal && ! $reel->isLocal && $request->video_url) {
                $reel->content_url = $request->video_url;
            }
            
            // Update the other fields if provided
            $reel->fill($request->only(['title', 'description', 'isVideo', 'isLocal', 'order']));
            $reel->save();
            
            return response()->json($reel);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::info('oh oops, we raised an error!!!');
            Log::info($e->errors());
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Content $reel)
    {
        $deletedOrder = $reel->order;

        if ($reel->isLocal && $reel->content_url) {
            $filePath = str_replace('/storage/', '', $reel->content_url);
            Storage::disk('public')->delete($filePath);
        }
        $reel->delete();

        // Reorder all items that came after the deleted item
        DB::transaction(function () use ($deletedOrder) {
            Content::where('isVideo', 'false')->where('order', '>', $deletedOrder)->decrement('order');
        });
        return response()->json();
    }

    public function stream(Request $request, Content $reel)
    {
        $path = $reel->content_url;

        $fileSize = filesize($path);
        $start = 0;
        $end = $fileSize - 1;

        // Handle range requests (for seeking/progressive loading)
        if ($request->hasHeader('Range')) {
            $range = $request->header('Range');
            list($start, $end) = $this->parseRange($range, $fileSize);
        }

        $length = $end - $start + 1;

        $response = new StreamedResponse(function() use ($path, $start, $length) {
            $handle = fopen($path, 'rb');
            fseek($handle, $start);
            
            $chunkSize = 8192; // 8KB chunks
            $remaining = $length;
            
            while ($remaining > 0 && !feof($handle)) {
                $read = min($chunkSize, $remaining);
                echo fread($handle, $read);
                $remaining -= $read;
                
                if (ob_get_level()) {
                    ob_flush();
                }
                flush();
            }
            
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'video/mp4');
        $response->headers->set('Accept-Ranges', 'bytes');
        $response->headers->set('Content-Length', $length);
        
        if ($request->hasHeader('Range')) {
            $response->setStatusCode(206); // Partial Content
            $response->headers->set('Content-Range', "bytes $start-$end/$fileSize");
        }

        return $response;
    }

    private function parseRange($range, $fileSize)
    {
        // Parse "bytes=start-end" format
        $range = str_replace('bytes=', '', $range);
        $parts = explode('-', $range);
        
        $start = intval($parts[0]);
        $end = isset($parts[1]) && $parts[1] !== '' ? intval($parts[1]) : $fileSize - 1;
        
        return [$start, min($end, $fileSize - 1)];
    }
}
