<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;

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

    function addComment($content, $post_id, $user_id, $post_owner) {
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

        /*if ($this->addNotification($post_owner, $user_id, 'comment'. $post_id . '/' .$comment->id, $content) !== true){
            return ['success' => false];
        } */

        return ['success' => true, 'comment_id' => $comment->id];
    }
    
    function addNotification(string $post_owner, string $sender_id, string $place, string $content): bool {
        
        $sender = User::select('nickname')->where('id', $sender_id)->first();

        $message = "{$sender->nickname} commented on your post: '{$content}'";
        if (preg_match('/comment(\d+)\/(\d+)/', $place, $matches)) {
            $post_id = $matches[1];
            $comment_id = $matches[2]; 
        }
        $link  = "{{route('post.view', $post_id)}}";
        //$link = "index.php?route=client&pages=post&post_id={$post_id}#comment{$comment_id}";
        /*
        $notification_action = Notification_Action::create([
            'place' => $place,
            'content' => $message
        ]);

        Notification::create([
            'user_nickname' => $post_owner,
            'sender_nickname' => $sender->nickname,
            'actions_id' => $notification_action->id,
            'link' => $link
        ]);
        */
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