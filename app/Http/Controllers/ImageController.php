<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\ImageCreator;

class ImageController extends Controller
{
    public function preview(Request $request, ImageCreator $creator)
    {
        $request->validate([
            'image' => 'required|image',
            'user_id' => 'required|integer',
        ]);

        $file = $request->file('image');

        $result = $creator->viewImage($file, $request->input('user_id'));

        if ($result === false) {
            return response()->json([
                'success' => false,
                'error' => 'Upload failed or file is invalid.'
            ], 422);
        }

        $uploadDir = storage_path('app/public/post/random');
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $filename = $result['filename'];
        $savePath = $uploadDir . '/' . $filename;

        if (!imagejpeg($result['new_image'], $savePath, 75)) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to save image.'
            ], 500);
        }

        imagedestroy($result['new_image']);
        imagedestroy($result['old_image']);

        return response()->json([
            'success' => true,
            'image_url' => asset("storage/post/random/{$filename}")
        ]);
    }
}