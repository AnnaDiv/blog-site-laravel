<?php

namespace App\Http\Controllers;


use App\Support\FollowsHelper;
use Illuminate\Http\Request;

use App\Models\User;

class FollowController extends Controller
{

    public function getFollows(Request $request, FollowsHelper $followsHelper) {

        $profileUser = $request->query('profileUser');
        $follower = $request->query('follower');

        $profileUser = User::where('nickname', $profileUser)->firstOrFail();
        $follower = User::where('nickname', $follower)->firstOrFail();

        $isFollowing = $follower->isFollowing($profileUser);
        $totalFollows = $followsHelper->getTotalFollowers($profileUser);

        return response()->json([$isFollowing, $totalFollows]);
    }

    public function toggleFollow(Request $request, FollowsHelper $followsHelper) {

        $profileUser = $request->input('profileUser');
        $follower = $request->input('follower');

        $profileUser = User::where('nickname', $profileUser)->firstOrFail();
        $follower = User::where('nickname', $follower)->firstOrFail();

        $isFollowing = $followsHelper->toggleFollow($profileUser, $follower);

        if (!$isFollowing){
            // $followsHelper->addNotification($pdo, $profileUser, $follower);
        }

        return response()->json(['success' => true]);
    }

}