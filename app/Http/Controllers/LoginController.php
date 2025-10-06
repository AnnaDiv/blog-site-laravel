<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

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

        if (Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'status' => 'active'
        ])) {

            $request->session()->regenerate();

            $user = $request->user();
            if ($user->admin) {
                return redirect()->route('admin.panel');
            }
            else {
                return redirect()->intended(route('home'))->with('success', 'Successful login');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials dont match our records'
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerate();

        return redirect()->intended(route('home'))->with('success', 'Successful logout');
    }
}
