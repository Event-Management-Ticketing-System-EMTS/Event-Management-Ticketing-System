<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Statistics - Admin</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gradient-to-br from-slate-900 to-slate-950 min-h-screen">
    <div class="min-h-screen p-6">
        <!-- Header -->
        <div class="max-w-7xl mx-auto mb-8">
            <div class="bg-slate-800/20 backdrop-blur-md rounded-xl border border-slate-700/50 p-6 shadow-2xl">
                <h1 class="text-3xl font-bold text-gray-100 mb-2">Password Reset Statistics</h1>
                <p class="text-gray-400">Monitor password reset activity and manage expired tokens</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Active Tokens -->
            <div class="bg-slate-800/20 backdrop-blur-md rounded-xl border border-slate-700/50 p-6 shadow-2xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm font-medium">Active Reset Tokens</p>
                        <p class="text-3xl font-bold text-cyan-400">{{ $stats['active_tokens'] }}</p>
                    </div>
                    <div class="p-3 bg-cyan-600/20 rounded-lg">
                        <svg class="w-8 h-8 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Expired Tokens -->
            <div class="bg-slate-800/20 backdrop-blur-md rounded-xl border border-slate-700/50 p-6 shadow-2xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm font-medium">Expired Tokens</p>
                        <p class="text-3xl font-bold text-red-400">{{ $stats['expired_tokens'] }}</p>
                    </div>
                    <div class="p-3 bg-red-600/20 rounded-lg">
                        <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Today's Resets -->
            <div class="bg-slate-800/20 backdrop-blur-md rounded-xl border border-slate-700/50 p-6 shadow-2xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm font-medium">Resets Today</p>
                        <p class="text-3xl font-bold text-green-400">{{ $stats['total_resets_today'] }}</p>
                    </div>
                    <div class="p-3 bg-green-600/20 rounded-lg">
                        <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Cards -->
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Cleanup Action -->
            <div class="bg-slate-800/20 backdrop-blur-md rounded-xl border border-slate-700/50 p-6 shadow-2xl">
                <h3 class="text-xl font-semibold text-gray-100 mb-4">Token Management</h3>
                <p class="text-gray-400 mb-6">Clean up expired password reset tokens to maintain database hygiene.</p>

                <form action="{{ route('admin.password-reset.cleanup') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            class="group relative flex items-center justify-center py-3 px-6 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Clean Up Expired Tokens
                    </button>
                </form>
            </div>

            <!-- System Information -->
            <div class="bg-slate-800/20 backdrop-blur-md rounded-xl border border-slate-700/50 p-6 shadow-2xl">
                <h3 class="text-xl font-semibold text-gray-100 mb-4">System Information</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Token Expiry Time:</span>
                        <span class="text-gray-200">60 minutes</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Rate Limit:</span>
                        <span class="text-gray-200">1 request per 5 minutes</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Email Delivery:</span>
                        <span class="text-gray-200">{{ config('mail.driver', 'Log') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Security Pattern:</span>
                        <span class="text-cyan-400">Command Pattern</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back to Dashboard -->
        <div class="max-w-7xl mx-auto mt-8 text-center">
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center text-cyan-400 hover:text-cyan-300 text-sm font-medium transition duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if (session('status'))
        <div class="fixed bottom-4 right-4 bg-green-900/90 border border-green-800 text-green-200 px-6 py-4 rounded-lg backdrop-blur-sm shadow-xl">
            {{ session('status') }}
        </div>
    @endif
</body>
</html>
