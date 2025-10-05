<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Profile;

class RegisterController extends Controller
{
    public function register(): View
    {
        return view('login.register');
    }

    public function store(Request $request): View
    {
        $validatedData = $request->validate([
            'nickname' => 'required|string|max:100',
            'email' => 'required|string|email|unique:users',
            'motto' => 'nullable|string',
            'password' => 'required|string|min:3|confirmed'
        ]);

        //Hash password
        $validatedData['password'] = Hash::make($validatedData['password']);
        $validatedData['status'] = 'inactive';
        
        //make user
        $user = User::create($validatedData);
        Profile::create([
            'user_id' => $user->id,
        ]);

        $token = Str::random(64);

        DB::table('account_activation_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $user->sendActivateAccount($token);

        return view('account.register-submitted');
    }
}
