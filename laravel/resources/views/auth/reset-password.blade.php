<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Event Management</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gradient-to-br from-slate-900 to-slate-950 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <h2 class="mt-6 text-3xl font-bold text-gray-100">
                    Reset your password
                </h2>
                <p class="mt-2 text-sm text-gray-400">
                    Enter your new password below to reset your account password.
                </p>
            </div>

            <!-- Errors -->
            @if ($errors->any())
                <div class="bg-red-900/30 border border-red-800 text-red-200 px-4 py-3 rounded-lg backdrop-blur-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Reset Password Form -->
            <div class="bg-slate-800/20 backdrop-blur-md rounded-xl border border-slate-700/50 p-8 shadow-2xl">
                <form class="space-y-6" action="{{ route('password.store') }}" method="POST">
                    @csrf

                    <!-- Hidden Fields -->
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div>
                        <label for="email_display" class="block text-sm font-medium text-gray-300 mb-2">
                            Email Address
                        </label>
                        <input id="email_display"
                               type="email"
                               value="{{ $email }}"
                               disabled
                               class="w-full px-4 py-3 bg-slate-900/30 border border-slate-600 rounded-lg text-gray-400 cursor-not-allowed">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                            New Password
                        </label>
                        <input id="password"
                               name="password"
                               type="password"
                               autocomplete="new-password"
                               required
                               class="w-full px-4 py-3 bg-slate-900/50 border border-slate-600 rounded-lg text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition duration-200"
                               placeholder="Enter your new password">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">
                            Confirm New Password
                        </label>
                        <input id="password_confirmation"
                               name="password_confirmation"
                               type="password"
                               autocomplete="new-password"
                               required
                               class="w-full px-4 py-3 bg-slate-900/50 border border-slate-600 rounded-lg text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition duration-200"
                               placeholder="Confirm your new password">
                    </div>

                    <div>
                        <button type="submit"
                                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 transition duration-200 transform hover:scale-105">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <!-- Key Icon -->
                                <svg class="h-5 w-5 text-cyan-300 group-hover:text-cyan-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                            </span>
                            Reset Password
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

            <!-- Security Notice -->
            <div class="text-center">
                <p class="text-xs text-gray-500">
                    For your security, this reset link will expire in 60 minutes.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
