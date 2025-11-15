<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MediaController extends Controller
{
    public function serveMedia($filename)
    {
        $filePath = "downloads/{$filename}";

        // Check if file exists in storage
        if (!Storage::disk('public')->exists($filePath)) {
            return response()->json(['message' => 'File not found'], Response::HTTP_NOT_FOUND);
        }

        // Get file contents and mime type
        $file = Storage::disk('public')->get($filePath);
        $mimeType = Storage::disk('public')->mimeType($filePath);

        // Return the file as a response
        return response($file, 200)->header("Content-Type", $mimeType);
    }
}