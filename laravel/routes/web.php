<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventStatisticsController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\UserController;

// Simple feature controllers
use App\Http\Controllers\SimpleBookingController;
use App\Http\Controllers\SimpleEventApprovalController;
use App\Http\Controllers\SimpleNotificationController;
use App\Http\Controllers\SimplePaymentController;
use App\Http\Controllers\SimpleTicketController;
use App\Http\Controllers\TestNotificationController;
use App\Http\Controllers\UserDashboardController;

/*
|--------------------------------------------------------------------------
| Public (guest-only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Login (both route names for compatibility)
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login.show');
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

/*
|--------------------------------------------------------------------------
| Public Landing & Event browsing
|--------------------------------------------------------------------------
*/
Route::view('/welcome', 'welcome')->name('welcome');

// Public event browsing (view-only)
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])
    ->whereNumber('event') // ✅ Fix: prevents conflict with /events/create
    ->name('events.show');

/*
|--------------------------------------------------------------------------
| Authenticated-only
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin Dashboard
    Route::view('/dashboard', 'admin.dashboard')->name('dashboard');

    // ✅ User Dashboard (dynamic with controller)
    Route::get('/user-dashboard', [UserDashboardController::class, 'show'])->name('user.dashboard');

    // Profile (view + update)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Event management (CRUD except index/show which are public)
    Route::resource('events', EventController::class)->except(['index', 'show']);

    // Event statistics
    Route::get('/event-statistics', [EventStatisticsController::class, 'index'])->name('events.statistics');

    /*
    |--------------------------------------------------------------------------
    | Ticket purchase (web)
    |--------------------------------------------------------------------------
    */
    Route::post('/events/{event}/purchase', [SimpleBookingController::class, 'purchase'])
        ->name('tickets.purchase');

    /*
    |--------------------------------------------------------------------------
    | Simple API-style endpoints used by pages (availability, cancel, etc.)
    |--------------------------------------------------------------------------
    */
    Route::prefix('api/events')->group(function () {
        Route::get('/{event}/availability', [SimpleTicketController::class, 'getAvailability']);
        Route::post('/{event}/purchase', [SimpleTicketController::class, 'purchaseTickets']); // keep if used elsewhere (AJAX)
    });

    // Simple notification routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [SimpleNotificationController::class, 'index'])->name('notifications.index');
        Route::get('/unread', [SimpleNotificationController::class, 'getUnread'])->name('notifications.unread');
        Route::post('/{id}/read', [SimpleNotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::get('/count', [SimpleNotificationController::class, 'getCount'])->name('notifications.count');
    });

    // Simple ticket management
    Route::prefix('api/tickets')->group(function () {
        Route::post('/{ticket}/cancel', [SimpleTicketController::class, 'cancelTicket'])->name('tickets.cancel');
    });

    // My Tickets page for users
    Route::get('/my-tickets', [SimpleTicketController::class, 'myTickets'])->name('tickets.my');
    Route::post('/api/tickets/{ticket}/cancel', [SimpleTicketController::class, 'cancelTicket'])
        ->name('tickets.cancel');

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
        Route::get('/cancel-ticket', [TestNotificationController::class, 'testCancellation']);
        Route::get('/notifications', [TestNotificationController::class, 'showNotifications']);
    });

    // User management (Admin only)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::patch('/users/{id}/role', [UserController::class, 'updateRole'])->name('users.updateRole');

    // Booking management (reports/export)
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [SimpleBookingController::class, 'index'])->name('index');
        Route::get('/{id}', [SimpleBookingController::class, 'show'])->name('show');
        Route::get('/export/csv', [SimpleBookingController::class, 'export'])->name('export');

        // AJAX endpoints for dynamic loading
        Route::get('/event/{eventId}/bookings', [SimpleBookingController::class, 'getEventBookings'])->name('event');
        Route::get('/user/{userId}/bookings', [SimpleBookingController::class, 'getUserBookings'])->name('user');
    });

    // Event approval (Admin only)
    Route::prefix('admin/approvals')->name('admin.approvals.')->group(function () {
        Route::get('/', [SimpleEventApprovalController::class, 'index'])->name('index');
        Route::get('/{event}', [SimpleEventApprovalController::class, 'show'])->name('show');
        Route::post('/{event}/approve', [SimpleEventApprovalController::class, 'approve'])->name('approve');
        Route::post('/{event}/reject', [SimpleEventApprovalController::class, 'reject'])->name('reject');
    });

    // Payment management (Admin only)
    Route::prefix('admin/payments')->name('admin.payments.')->group(function () {
        Route::get('/', [SimplePaymentController::class, 'index'])->name('index');
        Route::post('/{ticket}/mark-paid', [SimplePaymentController::class, 'markPaid'])->name('mark-paid');
        Route::post('/{ticket}/mark-failed', [SimplePaymentController::class, 'markFailed'])->name('mark-failed');
        Route::post('/{ticket}/refund', [SimplePaymentController::class, 'refund'])->name('refund');
        Route::post('/{ticket}/retry', [SimplePaymentController::class, 'retry'])->name('retry');
    });

    // Shared demo page (optional)
    Route::view('/tailwind-demo', 'tailwind-demo')->name('tailwind.demo');
});
