<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;

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

    // Admin Dashboard → resources/views/admin/dashboard.blade.php
    Route::view('/dashboard', 'admin.dashboard')->name('dashboard');

    // User Dashboard → resources/views/user/dashboard.blade.php
    Route::view('/user-dashboard', 'user.dashboard')->name('user.dashboard');

    // Profile (view + update)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Shared demo page (optional)
    Route::view('/tailwind-demo', 'tailwind-demo')->name('tailwind.demo');
});

// ---------- Public Landing ----------
Route::view('/welcome', 'welcome')->name('welcome');
