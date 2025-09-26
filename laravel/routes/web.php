<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\SessionAuth;

// ---------- Default route: show login ----------
Route::get('/', [AuthController::class, 'showLogin'])->name('login.show');
Route::post('/login', [AuthController::class, 'login'])->name('login.perform');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ---------- Registration ----------
Route::get('/register', [RegisterController::class, 'showRegister'])->name('register.show');
Route::post('/register', [RegisterController::class, 'register'])->name('register.perform');

// ---------- Password Reset ----------
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');

// ---------- Protected pages ----------
Route::middleware(SessionAuth::class)->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::view('/tailwind-demo', 'tailwind-demo')->name('tailwind.demo');
});

Route::view('/welcome', 'welcome')->name('welcome');
