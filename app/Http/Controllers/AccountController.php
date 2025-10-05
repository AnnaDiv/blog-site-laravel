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
use App\Models\User;

class AccountController extends Controller
{
    public function index() : View {
        return view('account.account-help');   
    }

    public function passwordHelp() : View {
        return view('account.password-help');        
    }

    public function passwordResetProduce(Request $request) {

        $request->validate(['email' => 'required|email|exists:users,email']);
        
        Password::sendResetLink($request->only('email'));
        
        return view('account.password-change-submitted');
    }

    public function passwordReset(string $token, Request $request) : View {

        $token = urldecode($token);
        $email = urldecode($request->query('email', null));

        return view('account.password-reset-form', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function passwordResetSubmit(Request $request) : RedirectResponse {
        
        $request->validate([
            'token' => 'required|string',
            'password' => 'required|string|min:3|confirmed',
            'email' => 'required|email',
        ]);
        
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );
          
        if ($status === Password::PASSWORD_RESET) {
            if ($request->user()) {
                return redirect()->route('logout')->with('success', __($status));
            }
            return redirect()->route('login')->with('success', __($status));
        }
        else {
            return back()->with(['error' => 'Invalid or expired reset token']);
        }

    }

    public function activateAccount(string $token) {

        $token = urldecode($token);
        $email = null;

        foreach (DB::table('account_activation_tokens')->get() as $entry) {
            if (Hash::check($token, $entry->token)) {
                $email = $entry->email;
                break;
            }
        }

        if ($email == null){
            return redirect()->route('register')->with('error', 'activation token invalid or expired');
        }

        $user = User::where('email', $email)->firstOrFail();

        $user->update(['status' => 'active']);

        DB::table('account_activation_tokens')->where('email', $email)->delete();

        return redirect()->route('login')->with('success', 'Successful account activation, you are now able to log in.');
    }
}
