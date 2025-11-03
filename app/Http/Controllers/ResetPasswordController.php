<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetPasswordRequest;
use App\Mail\SendResetPasswordMail;
use App\Mail\SendVerificationMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ResetPasswordController extends Controller
{
    public function resetPasswordEmail()
    {
        return view('auth.forgot-password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate(['email' => 'required|exists:users',]);
//        $input = $request->validated();
//        $email = $input['email'];
        $email = $request->email;
        $user = User::where('email', $email)->first();

        Mail::to($email)->send(new SendResetPasswordMail($user));
        return back()->with('message', 'Check your mails for link to reset your password');
    }

    public function resetPasswordForm(Request $request)
    {
        $email = $request->get('email');
        $token = $request->get('token');
        return view('auth.reset-password', ['email' => $email, 'token' => $token]);
    }

    public function resetUserPassword(ResetPasswordRequest $request)
    {
        $input = $request->validated();
        $email = $input['email'];
        $password = $input['password'];
        $user = User::where('email', $email)->first();

        $user->update(['password' => Hash::make($password)]);

        return redirect()->route('login');

    }
}
