<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class RegisterController extends Controller
{
    public function register(): View
    {
        return view('login.register');
    }

    public function store(Request $request): RedirectResponse
    {

        $validatedData = $request->validate([
            'nickname' => 'required|string|max:100',
            'email' => 'required|string|email|unique:users',
            'motto' => 'nullable|string',
            'password' => 'required|string|min:3|confirmed',
        ]);

        //Hash password
        $validatedData['password'] = Hash::make($validatedData['password']);

        //make user
        $user = User::create($validatedData);

        return redirect()->route('login')->with('success', 'Registered successfully you can now log in');
    }
}
