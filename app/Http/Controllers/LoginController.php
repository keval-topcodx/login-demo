<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if(Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(LoginUserRequest $request)
    {
        $input = $request->validated();
        $user = User::where('email', $input['email'])->first();

        if (!Hash::check($input['password'], $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password.'])->onlyInput('email');
        }
        auth()->login($user);

        return redirect()->route('dashboard');

    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('login');

    }

}
