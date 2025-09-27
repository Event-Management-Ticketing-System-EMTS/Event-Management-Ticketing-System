<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\AuthController;

// ---------- Public (guest-only) ----------
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/', [AuthController::class, 'showLogin'])->name('login.show');
    Route::post('/login', [AuthController::class, 'login'])->name('login.perform');

    // Registration
    Route::get('/register', [RegisterController::class, 'showRegister'])->name('register.show');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.perform');

    // Password Reset
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
});

// ---------- Authenticated-only ----------
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin Dashboard
    Route::view('/dashboard', 'dashboard')->name('dashboard'); // resources/views/dashboard.blade.php

    // User Dashboard  ✅ point to resources/views/user/dashboard.blade.php
    Route::view('/user-dashboard', 'user.dashboard')->name('user.dashboard');

    // Shared demo page (optional)
    Route::view('/tailwind-demo', 'tailwind-demo')->name('tailwind.demo');
});

// ---------- Public Landing ----------
Route::view('/welcome', 'welcome')->name('welcome');
