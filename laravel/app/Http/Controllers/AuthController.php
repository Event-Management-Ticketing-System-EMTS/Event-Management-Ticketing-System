<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LoginLog; // optional if you want to log
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $data['email'])->first();

        $ok = $user && Hash::check($data['password'], $user->password);

        // (Optional) save a login log row if you created the login_logs table
        if (class_exists(LoginLog::class)) {
            LoginLog::create([
                'user_id'   => $user?->id,
                'email'     => $data['email'],
                'success'   => $ok,
                'ip'        => $request->ip(),
                'user_agent'=> $request->userAgent(),
            ]);
        }

        if (!$ok) {
            return back()
                ->withErrors(['email' => 'Invalid email or password.'])
                ->withInput();
        }

        // Simple session-based login
        Session::put('user_id', $user->id);
        Session::put('user_name', $user->name);

        return redirect()->route('dashboard')->with('success', 'Logged in successfully!');

    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect()->route('login.show')->with('success', 'Logged out.');
    }
}
