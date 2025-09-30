<?php

namespace App\Support;

use App\Models\User;
use App\Models\Notification;

class FollowsHelper
{
    public function getTotalFollowers(User $profileUser) {

        $totalFollowers = $profileUser->followers()->count();
        return $totalFollowers;
    }

    public function toggleFollow(User $profileUser, User $follower) {
        $isFollowing = $follower->isFollowing($profileUser);

        if ($isFollowing) {
            $follower->following()->detach($profileUser->id);
        }
        else {
            $follower->following()->attach($profileUser->id);
            if($profileUser->isBlockedBy($follower)){
                $follower->blockedUsers()->detach($profileUser->nickname);
            }
        }

        return $isFollowing;
    }

    function addNotification(User $profileUser, User $follower): bool {

        $message = "{$follower->nickname} followed you";

        $link = "/profile/$follower->nickname";

        Notification::create([
            'notification_owner_id' => $profileUser->id,
            'sender_id' => $follower->id,
            'content' => $message,
            'place' => "follow",
            'link' => $link,
            'used' => 0
        ]);

        return true;
    }
}