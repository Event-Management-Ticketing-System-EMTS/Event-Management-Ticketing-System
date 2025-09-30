<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Event Management</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gradient-to-br from-slate-900 to-slate-950 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <h2 class="mt-6 text-3xl font-bold text-gray-100">
                    Forgot your password?
                </h2>
                <p class="mt-2 text-sm text-gray-400">
                    No problem. Just let us know your email address and we will email you a password reset link.
                </p>
            </div>

            <!-- Status Messages -->
            @if (session('status'))
                <div class="bg-green-900/30 border border-green-800 text-green-200 px-4 py-3 rounded-lg backdrop-blur-sm">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Errors -->
            @if ($errors->any())
                <div class="bg-red-900/30 border border-red-800 text-red-200 px-4 py-3 rounded-lg backdrop-blur-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Forgot Password Form -->
            <div class="bg-slate-800/20 backdrop-blur-md rounded-xl border border-slate-700/50 p-8 shadow-2xl">
                <form class="space-y-6" action="{{ route('password.email') }}" method="POST">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                            Email Address
                        </label>
                        <input id="email"
                               name="email"
                               type="email"
                               autocomplete="email"
                               required
                               value="{{ old('email') }}"
                               class="w-full px-4 py-3 bg-slate-900/50 border border-slate-600 rounded-lg text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition duration-200"
                               placeholder="Enter your email address">
                    </div>

                    <div>
                        <button type="submit"
                                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 transition duration-200 transform hover:scale-105">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <!-- Email Icon -->
                                <svg class="h-5 w-5 text-cyan-300 group-hover:text-cyan-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </span>
                            Email Password Reset Link
                        </button>
                    </div>
                </form>

                <!-- Back to Login -->
                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}"
                       class="text-cyan-400 hover:text-cyan-300 text-sm font-medium transition duration-200">
                        ← Back to Login
                    </a>
                </div>
            </div>

            <!-- Footer Info -->
            <div class="text-center">
                <p class="text-xs text-gray-500">
                    If you don't receive an email within a few minutes, check your spam folder.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
