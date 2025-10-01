<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\SupportController;

// ---------- Public (guest-only) ----------
Route::middleware('guest')->group(function () {
    // Login (both route names for compatibility)
    Route::get('/', [AuthController::class, 'showLogin'])->name('login'); // Laravel expects this name
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login.show'); // Alternative access
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

    // Simple API routes for ticket availability
    Route::prefix('api/events')->group(function () {
        Route::get('/{event}/availability', [\App\Http\Controllers\SimpleTicketController::class, 'getAvailability']);
        Route::post('/{event}/purchase', [\App\Http\Controllers\SimpleTicketController::class, 'purchaseTickets']);
    });

    // Simple notification routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [\App\Http\Controllers\SimpleNotificationController::class, 'index'])->name('notifications.index');
        Route::get('/unread', [\App\Http\Controllers\SimpleNotificationController::class, 'getUnread'])->name('notifications.unread');
        Route::post('/{id}/read', [\App\Http\Controllers\SimpleNotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::get('/count', [\App\Http\Controllers\SimpleNotificationController::class, 'getCount'])->name('notifications.count');
    });

    // Simple ticket management
    Route::prefix('api/tickets')->group(function () {
        Route::post('/{ticket}/cancel', [\App\Http\Controllers\SimpleTicketController::class, 'cancelTicket'])->name('tickets.cancel');
    });

    // My Tickets page for users
    Route::get('/my-tickets', [\App\Http\Controllers\SimpleTicketController::class, 'myTickets'])->name('tickets.my');

    // Support system
    Route::get('/support', [SupportController::class, 'create'])->name('support.create');
    Route::post('/support', [SupportController::class, 'store'])->name('support.store');

    // Admin support routes
    Route::prefix('admin/support')->name('admin.support.')->group(function () {
        Route::get('/', [SupportController::class, 'index'])->name('index');
        Route::get('/{id}', [SupportController::class, 'show'])->name('show');
        Route::post('/{id}/respond', [SupportController::class, 'respond'])->name('respond');
    });

    // Test routes (remove these later!)
    Route::prefix('test')->group(function () {
        Route::get('/cancel-ticket', [\App\Http\Controllers\TestNotificationController::class, 'testCancellation']);
        Route::get('/notifications', [\App\Http\Controllers\TestNotificationController::class, 'showNotifications']);
    });

    // User management (Admin only)
    Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [\App\Http\Controllers\UserController::class, 'show'])->name('users.show');
    Route::patch('/users/{id}/role', [\App\Http\Controllers\UserController::class, 'updateRole'])->name('users.updateRole');

    // Booking management (Simple booking functionality)
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SimpleBookingController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\SimpleBookingController::class, 'show'])->name('show');
        Route::get('/export/csv', [\App\Http\Controllers\SimpleBookingController::class, 'export'])->name('export');

        // AJAX endpoints for dynamic loading
        Route::get('/event/{eventId}/bookings', [\App\Http\Controllers\SimpleBookingController::class, 'getEventBookings'])->name('event');
        Route::get('/user/{userId}/bookings', [\App\Http\Controllers\SimpleBookingController::class, 'getUserBookings'])->name('user');
    });

    // Event approval (Admin only)
    Route::prefix('admin/approvals')->name('admin.approvals.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SimpleEventApprovalController::class, 'index'])->name('index');
        Route::get('/{event}', [\App\Http\Controllers\SimpleEventApprovalController::class, 'show'])->name('show');
        Route::post('/{event}/approve', [\App\Http\Controllers\SimpleEventApprovalController::class, 'approve'])->name('approve');
        Route::post('/{event}/reject', [\App\Http\Controllers\SimpleEventApprovalController::class, 'reject'])->name('reject');
    });

    // Payment management (Admin only)
    Route::prefix('admin/payments')->name('admin.payments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SimplePaymentController::class, 'index'])->name('index');
        Route::post('/{ticket}/mark-paid', [\App\Http\Controllers\SimplePaymentController::class, 'markPaid'])->name('mark-paid');
        Route::post('/{ticket}/mark-failed', [\App\Http\Controllers\SimplePaymentController::class, 'markFailed'])->name('mark-failed');
        Route::post('/{ticket}/refund', [\App\Http\Controllers\SimplePaymentController::class, 'refund'])->name('refund');
        Route::post('/{ticket}/retry', [\App\Http\Controllers\SimplePaymentController::class, 'retry'])->name('retry');
    });

    // Shared demo page (optional)
    Route::view('/tailwind-demo', 'tailwind-demo')->name('tailwind.demo');
});

// ---------- Public Landing ----------
Route::view('/welcome', 'welcome')->name('welcome');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
