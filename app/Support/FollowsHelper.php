<?php

namespace App\Support;

use App\Models\User;

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
        }

        return $isFollowing;
    }

    function addNotification($pdo, string $profileUser, string $follower): bool {

        $message = "{$follower} followed you";

        $follower = User::where('nickname', $follower)->firstOrFail();
        $link = "{{route('profile.profile', $follower)}}";

        /*
        $stmt = $pdo->prepare('INSERT INTO notification_actions (`place`, `content`) VALUES (:place, :content)');
        $stmt->execute([
            ':place' => "follow($profileUser}",
            ':content' => $message
        ]);
        $actions_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare('INSERT INTO notifications (`users_nickname`, `senders_nickname`, `actions_id`, `link`) 
                            VALUES (:users_nickname, :senders_nickname, :actions_id, :link)');
        $stmt->execute([
            ':users_nickname' => $profileUser,
            ':senders_nickname' => $follower,
            ':actions_id' => $actions_id,
            ':link' => $link
        ]);
        */
        return true;
    }
}