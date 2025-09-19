<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use App\Models\Like;
use App\Models\Post;

class LikesHelper
{

    function getLike($post_id, $user_id) {

        $like = Like::query('like')
            ->where('post_id', $post_id)
            ->where('user_id', $user_id)
            ->value('like') ?? 0;

        return $like;
    }

    function getTotalLikes($post_id) {
        $likes = Like::where('post_id', $post_id)
            ->where('like', 1)
            ->count();

        Post::where('id', $post_id)->update(['likes_count' => $likes]);
 
        return $likes;
    }

    function toggleLike($post_id, $user_id, $post_owner, $user_nickname)
    {
        $existingLike = Like::select('id', 'like')
            ->where('post_id', $post_id)
            ->where('user_id', $user_id)
            ->first();

        if ($existingLike) {
            $newLike = $existingLike->like === 1 ? 0 : 1;
            $existingLike->update(['like' => $newLike]);
        } else {
            $newLike = 1;
            Like::create([
                'like' => $newLike,
                'post_id' => $post_id,
                'user_id' => $user_id
            ]);
        }

        if ($newLike === 1 && $user_nickname !== $post_owner) {
            // addNotification($post_owner, $user_nickname, $post_id);
        }

        return [
            'success' => true,
            'liked' => $newLike
        ];
    }

    function addNotification(string $post_owner, string $sender_nickname, int $post_id): bool {
        if ($post_owner === $sender_nickname) return true;

        $link = "{{route('post.view', $post_id)}}";
        $message = "{$sender_nickname} liked your post";
        /*
        $notification_action = Notification_Actions::create([
            'place' => "like{$post_id}",
            'content' => $message
        ]);

        Notification::create([
            'users_nickname' => $post_owner,
            'senders_nickname' => $sender_nickname,
            'actions_id' => $notification_action->id,
            'link' => $link,
        ]); */

        return true;
    }

}