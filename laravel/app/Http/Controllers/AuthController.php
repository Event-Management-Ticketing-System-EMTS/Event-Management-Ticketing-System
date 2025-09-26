<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LoginLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin() {
        return view('auth.login');
    }

    public function login(Request $request) {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $data['email'])->first();

        $ok = $user && Hash::check($data['password'], $user->password);

        if ($user) {
            LoginLog::create([
                'user_id'    => $user->id,
                'ip'         => $request->ip(),
                'user_agent' => $request->userAgent(),
                'success'    => $ok,
            ]);
        }

        if (!$ok) {
            return back()->withErrors(['email' => 'Invalid credentials'])->onlyInput('email');
        }

        // store in session
        $request->session()->put('user_id', $user->id);

        return redirect('/')->with('success', 'Logged in!');
    }

    public function logout(Request $request) {
        $request->session()->forget('user_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'Logged out.');
    }
}
