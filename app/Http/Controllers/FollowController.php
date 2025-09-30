<?php

namespace App\Http\Controllers;


use App\Support\FollowsHelper;
use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\User;
use Illuminate\Http\RedirectResponse;

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
            $followsHelper->addNotification($profileUser, $follower);
        }

        return response()->json(['success' => true]);
    }

    public function showFollowers(Request $request) : View | RedirectResponse {
        $user = $request->user();

        if(!$user){
            return back()->with('error', 'cant find user');
        }
        $users = $user->followers()->paginate(15);
        
        return view('search.users')->with('users', $users);
    }

    public function showFollowing (Request $request) : View | RedirectResponse {
        $user = $request->user();

        if(!$user){
            return back()->with('error', 'cant find user');
        }
        $users = $user->following()->paginate(15);

        return view('search.users')->with('users', $users);
    }

}