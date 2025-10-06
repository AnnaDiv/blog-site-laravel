<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Repositories\EntriesRepository;
use Illuminate\Http\RedirectResponse;

use App\Models\User;
use App\Models\Profile;

class ProfileController extends Controller
{
    public function public(string $profile_owner_nickname, Request $request, EntriesRepository $entriesRepository) : View | RedirectResponse {
        
        $user = Auth::user();
        $profile_owner = User::where('nickname', $profile_owner_nickname)->first();

        if (!$profile_owner) {
            abort(404, 'Profile not found');
        }

        if (!$user || ($user && !$profile_owner->hasBlocked($user))) {

            $posts = $entriesRepository->publicPosts($profile_owner->id);
            $profile = Profile::where('user_id', $profile_owner->id)->first();

            return view('profile.profile', compact('profile_owner', 'posts', 'profile'));
        }
        else {
            return view('profile.blocked')->with('profile_owner_nickname', $profile_owner_nickname);
        }
    }

    public function private(string $profile_owner_nickname, Request $request, EntriesRepository $entriesRepository) : View | RedirectResponse {

        $user = Auth::user();
        $profile_owner = User::where('nickname', $profile_owner_nickname)->first();

        if ($profile_owner->id == $user->id || $user->admin) {
            $posts = $entriesRepository->privatePosts($profile_owner->id);
            $profile = Profile::where('user_id', $profile_owner->id)->first();

            return view('profile.profile', compact('profile_owner', 'posts', 'profile'));
        }
        else {
            return redirect()->route('profile.public', ['user_nickname' => $profile_owner_nickname]);
        }
    }
    
    // admin function 
    public function all(string $profile_owner_nickname, EntriesRepository $entriesRepository) : View | RedirectResponse {
        
        $user = Auth::user();
        $profile_owner = User::where('nickname', $profile_owner_nickname)->first();

        if (!$profile_owner) {
            abort(404, 'Profile not found');
        }

        $posts = $entriesRepository->allPostsPerUser($profile_owner->id);
        $profile = Profile::where('user_id', $profile_owner->id)->first();

        return view('profile.profile', compact('profile_owner', 'posts', 'profile'));

    }

    // admin function 
    public function deleted(string $profile_owner_nickname, EntriesRepository $entriesRepository) : View | RedirectResponse {
        
        $user = Auth::user();
        $profile_owner = User::where('nickname', $profile_owner_nickname)->first();

        if (!$profile_owner) {
            abort(404, 'Profile not found');
        }

        $posts = $entriesRepository->deletedPostsPerUser($profile_owner->id);
        $profile = Profile::where('user_id', $profile_owner->id)->first();

        return view('profile.profile', compact('profile_owner', 'posts', 'profile'));

    }

    public function permDelete(string $user_nickname) : RedirectResponse {

        $user = User::where('nickname', $user_nickname)->first();
        $profile = Profile::where('user_id', $user->id)->first();

        $user->delete();
        $profile->delete();

        return back()->with('success', 'deleted user');
    }
}
