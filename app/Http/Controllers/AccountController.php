<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


use App\Mail\ResetPasswordSubmission;
use App\models\User;

class AccountController extends Controller
{
    public function index() : View {
        return view('account.account-help');   
    }

    public function passwordHelp() : View {
        return view('account.password-help');        
    }

    public function passwordResetProduce(Request $request, PasswordBroker $broker) {

        $email = $request->input('email');

        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return view('account.password-change-submitted');
        }

        $user = User::where('email', $email)->firstOrFail();

        $token = $broker->createToken($user);

        $user->sendPasswordReset($token);

        return view('account.password-change-submitted');
    }

    public function passwordReset(string $token) : View {

        $token = urldecode($token);

        return view('account.password-reset-form', [
            'token' => $token,
        ]);
    }

    public function passwordResetSubmit(Request $request) : RedirectResponse {
        
        $request->validate([
            'token' => 'required|string',
            'password' => 'required|string|min:3|confirmed',
        ]);

        $token_found = DB::table('password_reset_tokens')
                ->where('token', $request->input('token'))
                ->first();
          
        if (!$token_found) {
            return back()->withErrors(['token' => 'Invalid or expired reset token']);
        }

        $email = $token_found->email;

        $user = User::where('email', $email)->firstOrFail();

        $user->forceFill([
            'password' => Hash::make($request->input('password')),
        ])->save();

        DB::table('password_reset_tokens')->where('email', $email)->delete();

        $user = $request->user();

        if ($user) {
            return redirect()->route('logout');
        }

        return redirect()->route('login');
    }
}
