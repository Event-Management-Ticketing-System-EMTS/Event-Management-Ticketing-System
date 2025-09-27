<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LoginLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        // If already authenticated, send to the right place
        if (Auth::check()) {
            $user = Auth::user();
            return $user->role === User::ROLE_ADMIN
                ? redirect()->route('dashboard')
                : redirect()->route('user.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validate input
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Get "remember me" checkbox value
        $remember = $request->boolean('remember');

        // Attempt login with remember me
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate(); // Prevent session fixation

            // (Optional) Save login log if table exists
            if (class_exists(LoginLog::class)) {
                LoginLog::create([
                    'user_id'    => Auth::id(),
                    'email'      => $credentials['email'],
                    'success'    => true,
                    'ip'         => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }

            // Redirect by role
            $user = Auth::user();
            return $user->role === User::ROLE_ADMIN
                ? redirect()->route('dashboard')->with('success', 'Welcome Admin!')
                : redirect()->route('user.dashboard')->with('success', 'Welcome User!');
        }

        // Log failed attempt
        if (class_exists(LoginLog::class)) {
            LoginLog::create([
                'user_id'    => null,
                'email'      => $credentials['email'],
                'success'    => false,
                'ip'         => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return back()
            ->withErrors(['email' => 'Invalid email or password.'])
            ->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout(); // Logs out + clears remember-me cookie

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.show')->with('success', 'Logged out.');
    }
}
