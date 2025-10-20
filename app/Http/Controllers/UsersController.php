<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Repositories\UsersRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Models\User;
use App\Models\Profile;

class UsersController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected UsersRepository $usersRepository) {}

    public function search(Request $request): View
    {
        $perPage = 15;
        $quote = strtolower($request->input('search_q'));

        if ($request->user()) {
            $user = $request->user();
            $excludedUsers = $this->usersRepository->excludedUsers($user->nickname);
        } else {
            $excludedUsers = [];
        }
        $users = $this->usersRepository->search($perPage, $quote, $excludedUsers);

        return view('search.users')->with('users', $users);
    }

    public function edit(Profile $profile) : View | RedirectResponse{
        
        $user = User::where('id', $profile->user_id)->first();

        if(!$user){
            return back()->with('error', 'there is no user to update');
        }
        
        $this->authorize('update', $profile);

        return view('profile.profile-update')->with('user', $user);
    }

    public function editAdmin(string $user_nickname) {
        $user = User::where('nickname', $user_nickname)->first();
        if (!$user){
            return back()->with('errors', 'cant find user');
        }
        
        $profile = Profile::where('user_id', $user->id)->first();

        return redirect()->route('profile.edit', ['profile' => $profile]);
    }

    public function update(User $user, Request $request) : RedirectResponse {

        if(!$user){
            return redirect()->route('home')->with('error', 'user not identified');
        }
        
        $validatedData = $request->validate([
            'email' => 'required|string|email',
            'nickname' => 'required|string|max:100',
            'motto' => 'nullable|string',
            'old_pass' => 'nullable|string|min:3',
            'password' => 'nullable|string|min:3|confirmed'
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
        } else {
            $image = null;
        }

        $user = $this->usersRepository->update($user, $validatedData, $image);
        
        if (!$user instanceof \App\Models\User){
            return back()->with('error' , $user);
        }
        
        return redirect()->route('profile.public', $user->nickname);
    }

    public function ban(string $user_nickname) : RedirectResponse {
        $user = User::where('nickname', $user_nickname)->first();
        if(!$user){
            return back()->with('error', 'cant find user');
        }
        $user->update(['status' => 'banned']);
        return back()->with('success', 'banned user');
    }

    public function adminActivate(string $user_nickname) : RedirectResponse {
        $user = User::where('nickname', $user_nickname)->first();
        if(!$user){
            return back()->with('error', 'cant find user');
        }
        $user->update(['status' => 'active']);
        return back()->with('success', 'activated user');
    }
}
