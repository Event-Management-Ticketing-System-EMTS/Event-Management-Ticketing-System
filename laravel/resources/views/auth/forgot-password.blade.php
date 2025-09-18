<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Forgot Password - {{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
            <script>
                tailwind.config = {
                    theme: {
                        extend: {
                            fontFamily: {
                                sans: ['Inter', 'sans-serif'],
                            },
                        },
                    },
                }
            </script>
        @endif
    </head>
    <body class="h-full font-sans antialiased text-slate-900 bg-slate-100 dark:bg-slate-900 dark:text-slate-50">
        <div 
            class="min-h-screen flex items-center justify-center bg-cover bg-center bg-no-repeat"
            style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1529333166437-7750a6dd5a70?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1738&q=80');"
        >
            <div class="w-full max-w-md p-8 space-y-8 bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm rounded-2xl shadow-2xl">
                <!-- Header -->
                <div class="text-center">
                    <a href="{{ url('/') }}" class="inline-block mb-4">
                        <h1 class="text-4xl font-bold tracking-tighter text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">EventHub</h1>
                    </a>
                    <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-200">
                        Forgot Your Password?
                    </h2>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                        No problem. Just let us know your email address and we will email you a password reset link.
                    </p>
                </div>

                <!-- Forgot Password Form -->
                <div class="px-4 sm:px-6 lg:px-8">
                    <!-- Flash Messages -->
                    @if (session('status'))
                        <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-emerald-800 dark:text-emerald-200 font-medium">{{ session('status') }}</p>
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-red-800 dark:text-red-200 font-medium">{{ session('error') }}</p>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-6">
                        @csrf

                        <!-- Email Field -->
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Email Address *
                            </label>
                            <div class="relative">
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    value="{{ old('email') }}"
                                    required 
                                    autofocus
                                    class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('email') border-red-500 focus:ring-red-500 @enderror"
                                    placeholder="Enter your email address"
                                >
                                @error('email')
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                @enderror
                            </div>
                            @error('email')
                                <p class="text-red-600 dark:text-red-400 text-sm flex items-center mt-1">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button 
                                type="submit"
                                class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-[1.02] hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-slate-900"
                            >
                                <span class="flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    Email Password Reset Link
                                </span>
                            </button>
                        </div>
                    </form>

                    <!-- Back to Login Link -->
                    <div class="mt-8 text-center">
                        <a href="{{ route('login') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>