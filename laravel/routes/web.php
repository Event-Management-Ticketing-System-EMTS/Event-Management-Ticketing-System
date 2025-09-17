<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;

Route::get('/', function () {
    return view('welcome');
});

// Registration routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

// Temporary login route (redirects to register since login isn't implemented yet)
Route::get('/login', function () {
    return redirect()->route('register')->with('info', 'Please create an account to get started!');
})->name('login');
