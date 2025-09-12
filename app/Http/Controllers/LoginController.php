<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function login(): View
    {
        return view('login.login');
    }

    public function loginAuth(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|string|email|max:100',
            'password' => 'required|string'
        ]);

        if (Auth::attempt($credentials)) {

            $request->session()->regenerate();
            return redirect()->intended(route('home'))->with('success', 'Successful login');
        }

        return back()->withErrors([
            'email' => 'The provided credentials dont match our records'
        ])->onlyInput('email');
    }
}
