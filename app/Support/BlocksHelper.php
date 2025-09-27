<?php

namespace App\Support;
use App\Support\FollowsHelper;

use App\Models\User;

class BlocksHelper
{

    function toggleBlock(string $profileUser, string $blockingUser) {


        $profileUser = User::where('nickname', $profileUser)->firstOrFail();
        $blockingUser = User::where('nickname', $blockingUser)->firstOrFail();

        $isBlocked = $profileUser->isBlockedBy($blockingUser);
        $remove = false;
        if ($isBlocked) {
            $blockingUser->blockedUsers()->detach($profileUser->nickname);
        }
        else {
            $blockingUser->blockedUsers()->attach($profileUser->nickname);
            if ($blockingUser->isFollowing($profileUser)){
                $followsHelper = new FollowsHelper();
                $followsHelper->toggleFollow($profileUser, $blockingUser);
                $remove = true;
            }
        }

        return ['success' => true, 'remove' => $remove];
    }
}