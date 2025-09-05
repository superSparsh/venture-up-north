<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

class EditorUploadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        if (!$request->hasFile('image') || !$request->file('image')->isValid()) {
            return response()->json(['success' => 0, 'message' => 'Invalid file upload.'], 422);
        }

        $path = $request->file('image')->store('editor', 'public');

        return response()->json([
            'success' => 1,
            'file' => [
                'url' => '/public/storage/' .$path,
            ],
        ]);
    }
}
