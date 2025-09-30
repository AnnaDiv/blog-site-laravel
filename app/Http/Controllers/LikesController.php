<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Support\LikesHelper;

use App\Models\User;

class LikesController extends Controller
{
    
    public function getLikes($post_id, LikesHelper $likesHelper) {

        $user_id = Auth::id();

        if (!$post_id) {
            echo json_encode(['error' => 'Missing post ID']);
        }

        if (!$user_id) {
            $like = 0;
        }
        else {
            $like = $likesHelper->getLike((int) $post_id, (int) $user_id);
        }

        $totalLikes = $likesHelper->getTotalLikes((int) $post_id);

        return response()->json([$like, $totalLikes]);
    }

    public function toggleLike(LikesHelper $likesHelper, Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'You are not logged in.',
            ], 401);
        }

        $request->validate([
            'post_id' => 'required|integer|exists:posts,id',
            'post_owner' => 'nullable|string',
            'like' => 'nullable|string',
        ]);

        $post_id = (int) $request->input('post_id');
        $post_owner_nickname = $request->input('post_owner', '');
        $post_owner = User::where('nickname', $post_owner_nickname)->first();

        $result = $likesHelper->toggleLike($post_id, $user, $post_owner);

        return response()->json($result);
    }
}
