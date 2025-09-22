<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            // Validate the request
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'isVideo' => 'required|in:true',
                'isLocal' => 'required|in:true,false',
                'video_file' => 'required_if:isLocal,true|file|mimes:mp4,mov,avi|max:51200', // 50MB max
                'video_url' => 'required_unless:isLocal,true|url|max:511',
                'image_file' => 'required_if:isLocal,true|file|mimes:jpeg,png',
                'image_url' => 'required_unless:isLocal,true|url',
                'order' => 'required|integer'
            ]);
            if ($request->isLocal === 'true') {
                $filePath = $request->file('video_file')->store('content', 'public');
                $contentUrl = '/storage/' . $filePath;
                $imgUrl = $request->file('image_file')->store('content', 'public');
                $imageUrl = '/storage/' . $imgUrl;
            } else {
                $contentUrl = $request->video_url;
                $imageUrl = $request->image_url;
            }
            // Create the video record
            $video = Content::create([
                'title' => $request->title,
                'description' => $request->description,
                'isVideo' => $request->isVideo,
                'isLocal' => $request->isLocal,
                'content_url' => $contentUrl,
                'img_url' => $imageUrl,
                'order' => $request->order
            ]);
            return response()->json($video, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
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
            // Validate the request
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'isVideo' => 'required|in:true',
                'isLocal' => 'required|in:true,false',
                'video_file' => 'sometimes|required_if:isLocal,true|file|mimes:mp4,mov,avi|max:51200', // 50MB max
                'video_url' => 'sometimes|required_unless:isLocal,true|url|max:511',
                'image_file' => 'sometimes|required_if:isLocal,true|file|mimes:jpeg,png',
                'image_url' => 'sometimes|required_unless:isLocal,true|url',
                'order' => 'required|integer'
            ]);
            
            // Handle new file upload if provided
            if ($request->isLocal && $video->isLocal && $request->hasFile('video_file')) {
                // Delete old file
                $oldFilePath = str_replace('/storage/', '', $video->content_url);
                Storage::disk('public')->delete($oldFilePath);
                
                // Store new file
                $filePath = $request->file('video_file')->store('content', 'public');
                $video->content_url = '/storage/' . $filePath;

                // Delete old image
                $oldImgPath = str_replace('/storage/', '', $video->img_url);
                Storage::disk('public')->delete($oldImgPath);

                // Store new image
                $imgPath = $request->file('img_file')->store('content', 'public');
                $video->img_url = '/storage/' . $imgPath;

            } else if ($request->isLocal && ! $video->isLocal && $request->hasFile('video_file')) {
                // Store new file
                $filePath = $request->file('video_file')->store('content', 'public');
                $video->content_url = '/storage/' . $filePath;

                // Store new image
                $imgPath = $request->file('img_file')->store('content', 'public');
                $video->img_url = '/storage/' . $imgPath;

            } else if (! $request->isLocal && $video->isLocal && $request->video_url) {
                // Delete old file
                $oldFilePath = str_replace('/storage/', '', $video->content_url);
                Storage::disk('public')->delete($oldFilePath);

                // Delete old image
                $oldImgPath = str_replace('/storage/', '', $video->img_url);
                Storage::disk('public')->delete($oldImgPath);
                
                $video->content_url = $request->video_url;
                $video->img_url = $request->image_url;

            } else if (! $request->isLocal && ! $video->isLocal && $request->video_url) {
                $video->content_url = $request->video_url;
                $video->img_url = $request->image_url;
            }
            
            // Update the other fields if provided
            $video->fill($request->only(['title', 'description', 'isVideo', 'isLocal', 'order']));
            $video->save();
            
            return response()->json($video);
        } catch (\Illuminate\Validation\ValidationException $e) {
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
            $imgPath = str_replace('/storage', '', $video->img_url);
            Storage::disk('public')->delete($imgPath);
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

        $length = $end - $start + 1;

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
