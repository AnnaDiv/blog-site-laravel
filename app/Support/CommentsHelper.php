<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Notification;

class CommentsHelper
{
    protected $table = 'comments';

    function getComments($post_id) {
        $post_id = (int) $post_id;

        return Comment::query()
            ->leftJoin('users', 'comments.user_id', '=', 'users.id')
            ->select([
                'comments.id',
                'comments.user_id',
                'users.nickname as user_nickname',
                'comments.content',
                DB::raw('DATE_FORMAT(comments.time, "%Y-%m-%dT%H:%i:%s") AS time')
            ])
            ->where('comments.post_id', $post_id)
            ->orderBy('comments.time', 'asc')
            ->get();
    }

    function addComment($content, $post_id, User $user, User $post_owner) {

        $user_id = $user->id;

        $comment = Comment::create([
            'post_id' => (int) $post_id,
            'user_id' => (int) $user_id,
            'content' => $content
        ]);

        $post = Post::where('id', $post_id)->first();

        if ($post) {
            $post->update([
                'comments_count' => $post->comments_count + 1,
            ]);
        }

        if($user !== $post_owner){
            $this->addNotification($post_owner, $user, $post_id, $comment, $content);
        }

        return ['success' => true, 'comment_id' => $comment->id];
    }
    
    function addNotification(User $post_owner, User $sender, $place, $comment): bool {

        $message = "{$sender->nickname} commented on your post: '{$comment->content}'";
    
        $link = "/post/view/$place#comment$comment->id";

        Notification::create([
            'notification_owner_id' => $post_owner->id,
            'sender_id' => $sender->id,
            'content' => $message,
            'place' => "comment",
            'link' => $link,
            'used' => 0
        ]); 

        return true;
    }

    function removeComment($post_id, $comment_id) {
        $comment = Comment::where('id', $comment_id)->first();

        if ($comment) {
            $comment->delete();
        }

        $post = Post::where('id', $post_id)->first();

        if ($post) {
            $post->update([
                'comments_count' => max(0, $post->comments_count - 1),
            ]);
        }

        return ['success' => true];
    }

}