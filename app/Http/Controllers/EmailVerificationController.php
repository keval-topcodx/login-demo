<?php

namespace App\Http\Controllers;

//use App\Http\Requests\EmailVerificationRequest;
use App\Http\Requests\EmailVerificationRequest;
use App\Mail\SendVerificationMail;
//use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EmailVerificationController extends Controller
{
    public function showEmailVerificationPage()
    {
        $user = Auth::user();
        if($user->email_verified_at) {
            return redirect()->route('dashboard');
        }
        return view('auth.verify-email');
    }

    public function sendVerificationMail(Request $request)
    {
        $user = Auth::user();
        Mail::to($user)->queue(new SendVerificationMail($user));

        return back()->with('message', 'Verification Link sent!');
    }

    public function verifyEmail(Request $request)
    {
        $userId = $request->id;
        $hash = $request->hash;
        $user = User::find($userId);

        if (sha1($user->email) === $hash) {
            if (!$user->email_verified_at) {
                $user->update(['email_verified_at' => now()]);
            }
            return redirect()->route('dashboard');
        }

        abort(403, 'Invalid verification link.');

    }
}
