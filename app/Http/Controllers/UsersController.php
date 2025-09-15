<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Repositories\UsersRepository;
use App\Models\User;

class UsersController extends Controller
{
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
}
