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

    // Event management
    Route::resource('events', \App\Http\Controllers\EventController::class);

    // Event statistics
    Route::get('/event-statistics', [\App\Http\Controllers\EventStatisticsController::class, 'index'])->name('events.statistics');

    // Ticket management
    Route::prefix('tickets')->group(function () {
        Route::post('/purchase', [\App\Http\Controllers\TicketController::class, 'purchase'])->name('tickets.purchase');
        Route::post('/confirm', [\App\Http\Controllers\TicketController::class, 'confirm'])->name('tickets.confirm');
        Route::get('/check/{event}', [\App\Http\Controllers\TicketController::class, 'checkAvailability'])->name('tickets.check');
    });

    // API routes for real-time updates
    Route::prefix('api/tickets')->group(function () {
        Route::get('/availability/{event}', [\App\Http\Controllers\TicketController::class, 'realTimeAvailability']);
        Route::post('/purchase', [\App\Http\Controllers\TicketController::class, 'purchase']);
    });

    // User management (Admin only)
    Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [\App\Http\Controllers\UserController::class, 'show'])->name('users.show');
    Route::patch('/users/{id}/role', [\App\Http\Controllers\UserController::class, 'updateRole'])->name('users.updateRole');

    // Shared demo page (optional)
    Route::view('/tailwind-demo', 'tailwind-demo')->name('tailwind.demo');
});

// ---------- Public Landing ----------
Route::view('/welcome', 'welcome')->name('welcome');
