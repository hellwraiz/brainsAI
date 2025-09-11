<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Log;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Content::where('isVideo', 'true')->orderBy('order')->get());
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
                'isVideo' => 'required|in:true',
                'isLocal' => 'required|in:true,false',
                'video_file' => 'required_if:isLocal,true|file|mimes:mp4,mov,avi|max:51200', // 50MB max
                'video_url' => 'required_unless:isLocal,true|url|max:511',
                'order' => 'required|integer'
            ]);

            Log::info('are we here?');
            if ($request->isLocal === 'true') {
                $filePath = $request->file('video_file')->store('content', 'public');
                $contentUrl = '/storage/' . $filePath;
            } else {
                $contentUrl = $request->video_url;
            }
            Log::info('are we here?');
            
            // Create the video record
            $video = Content::create([
                'title' => $request->title,
                'description' => $request->description,
                'isVideo' => $request->isVideo,
                'isLocal' => $request->isLocal,
                'content_url' => $contentUrl,
                'order' => $request->order
            ]);
            Log::info('are we here?');

            return response()->json($video, 201);
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
    public function show(Content $video)
    {
        return response()->json($video);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Content $video)
    {
        try {
            Log::info('we are the video!!!');
            Log::info($request->all());
            // Validate the request
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'isVideo' => 'required|in:true',
                'isLocal' => 'required|in:true,false',
                'video_file' => 'sometimes|required_if:isLocal,true|file|mimes:mp4,mov,avi|max:51200', // 50MB max
                'video_url' => 'sometimes|required_unless:isLocal,true|url|max:511',
                'order' => 'required|integer'
            ]);
            
            // Handle new file upload if provided
            if ($request->isLocal && $video->isLocal && $request->hasFile('video_file')) {
                // Delete old file
                $oldFilePath = str_replace('storage/', 'public/', $video->content_url);
                Storage::delete($oldFilePath);
                
                // Store new file
                $filePath = $request->file('video_file')->store('content', 'public');
                $video->content_url = '/storage/' . $filePath;
            } else if ($request->isLocal && ! $video->isLocal && $request->hasFile('video_file')) {
                // Store new file
                $filePath = $request->file('video_file')->store('content', 'public');
                $video->content_url = '/storage/' . $filePath;
            } else if (! $request->isLocal && $video->isLocal && $request->video_url) {
                // Delete old file
                $oldFilePath = str_replace('storage/', 'public/', $video->content_url);
                Storage::delete($oldFilePath);
                
                $video->content_url = $request->video_url;
            } else if (! $request->isLocal && ! $video->isLocal && $request->video_url) {
                $video->content_url = $request->video_url;
            }
            
            // Update the other fields if provided
            $video->fill($request->only(['title', 'description', 'isVideo', 'isLocal', 'order']));
            $video->save();
            
            return response()->json($video);
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
    public function destroy(Content $video)
    {
        $deletedOrder = $video->order;

        if ($video->isLocal && $video->content_url) {
            $filePath = str_replace('/storage/', '', $video->content_url);
            Storage::disk('public')->delete($filePath);
        }
        $video->delete();

        // Reorder all items that came after the deleted item
        DB::transaction(function () use ($deletedOrder) {
            Content::where('isVideo', 'true')->where('order', '>', $deletedOrder)->decrement('order');
        });
        return response()->json();
    }


    public function stream(Request $request, $id)
    {
        $video = Content::findOrFail($id);
        
        // Convert URL path to actual file path
        $relativePath = str_replace('/storage/', '', $video->content_url);
        $path = Storage::disk('public')->path($relativePath);
        
        // Let's also check what files are in the directory
        $directory = dirname($path);
        Log::info('Directory: ' . $directory);
        if (is_dir($directory)) {
            $files = scandir($directory);
            Log::info('Files in directory: ' . json_encode($files));
        }
        
        if (!is_file($path)) {
            abort(404, 'Video file not found or is not a file');
        }

        $fileSize = filesize($path);
        $start = 0;
        $end = $fileSize - 1;

        // Handle range requests (for seeking/progressive loading)
        if ($request->hasHeader('Range')) {
            $range = $request->header('Range');
            list($start, $end) = $this->parseRange($range, $fileSize);
        }


        Log::info('we get here1');

        $length = $end - $start + 1;

        Log::info('we get here2');

        $response = new StreamedResponse(function() use ($path, $start, $length) {
            $handle = fopen($path, 'rb');
            fseek($handle, $start);
            
            $chunkSize = 1024 * 256; // 256KB chunks
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

        Log::info('we get here3');

        $response->headers->set('Content-Type', 'video/mp4');
        $response->headers->set('Accept-Ranges', 'bytes');
        $response->headers->set('Content-Length', $length);


        Log::info('we get here4');
        
        if ($request->hasHeader('Range')) {
            $response->setStatusCode(206); // Partial Content
            $response->headers->set('Content-Range', "bytes $start-$end/$fileSize");
        }


        Log::info('we get here5');

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
