<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use App\Models\Notification;

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

    function toggleLike(int $post_id, User $user, User $post_owner)
    {
        $user_id = $user->id;
        $post_owner_id = $post_owner->id;

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

        if ($newLike === 1 && $user_id !== $post_owner_id) {
            $this->addNotification($post_owner, $user, $post_id);
        }

        return [
            'success' => true,
            'liked' => $newLike
        ];
    }

    function addNotification(User $post_owner, User $sender, int $post_id) {

        if ($post_owner === $sender) return true;

        $post_place = Post::where('id', $post_id);

        $message = "{$sender->nickname} liked your post";

        $link = vsprintf(
            '<a href="%s">%s</a>',
            [
                route('post.view', ['post' => $post_id]), // ← id is enough
                $message
            ]
        );
        //$link = "/post/view/$post_id";

        $new_notification = Notification::create([
            'notification_owner_id' => $post_owner->id,
            'sender_id' => $sender->id,
            'content' => $message,
            'place' => "like",
            'link' => $link,
            'used' => 0
        ]);

        Notification::where('id', '!=', $new_notification->id)
            ->where('notification_owner_id', $post_owner->id)
            ->where('sender_id', $sender->id)
            ->where('content', $message)
            ->where('place', 'like')
            ->where('link', $link)
            ->where('used', 0)
            ->delete();

    }

}