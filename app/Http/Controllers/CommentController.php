<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Support\CommentsHelper;

use App\Models\User;

class CommentController extends Controller
{
    public function getComments($post_id, CommentsHelper $commentsHelper) {
        $comments = $commentsHelper->getComments($post_id);
        return response()->json($comments);
    }

    public function addComment(Request $request, CommentsHelper $commentsHelper) {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'You are not logged in.',
            ], 401);
        }

        $request->validate([
            'post_id' => 'required|integer|exists:posts,id',
            'post_owner' => 'required|string',
            'comment' => 'required|string',
        ]);

        $post_id = (int) $request->input('post_id');
        $post_owner = $request->input('post_owner', '');
        $comment = $request->input('comment', '');

        $post_owner = User::where('nickname', $post_owner)->first();

        if ($comment !== '' && $post_id !== '' && $post_owner) {
            $result = $commentsHelper->addComment($comment, $post_id, $user, $post_owner);
            return response()->json($result);
        } else {
            return response()->json(['success' => false, 'error' => 'Missing data']);
        }
    }

    public function removeComment(Request $request, CommentsHelper $commentsHelper) {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'You are not logged in.',
            ], 401);
        }

        $request->validate([
            'post_id' => 'required|integer|exists:posts,id',
            'comment_id' => 'required|integer'
        ]);

        $post_id = (int) $request->input('post_id');
        $comment_id = (int) $request->input('comment_id');

        if ($comment_id && $post_id) {
            $result = $commentsHelper->removeComment($post_id, $comment_id);
            return response()->json($result);
        } else {
            return response()->json(['success' => false, 'error' => 'Missing data']);
        }
    }
}
