<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'EMTS') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-r from-pink-50 via-white to-blue-50 min-h-screen flex flex-col items-center justify-center font-sans">

    <header class="text-center mb-12">
        <h1 class="text-5xl font-extrabold text-pink-600 mb-4">🎟️ Event Management Ticketing System</h1>
        <p class="text-gray-600 dark:text-gray-300 max-w-xl mx-auto text-lg">
            Organize, manage, and sell tickets for your events easily. Track attendees, payments, and event details all in one platform.
        </p>
    </header>

    <main class="flex flex-col lg:flex-row items-center justify-center gap-12 px-6">

        <!-- Features Card -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg p-8 max-w-md w-full">
            <h2 class="text-2xl font-bold text-blue-600 mb-4">Features</h2>
            <ul class="space-y-3 text-gray-700 dark:text-gray-300">
                <li class="flex items-center gap-2">
                    <span class="text-pink-500">✔️</span> Create & manage events
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-pink-500">✔️</span> Sell tickets online
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-pink-500">✔️</span> Track attendees & payments
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-pink-500">✔️</span> Easy-to-use dashboard
                </li>
            </ul>
        </div>

        <!-- Call to Action -->
        <div class="bg-blue-50 dark:bg-gray-800 rounded-2xl shadow-lg p-8 max-w-md w-full text-center">
            <h2 class="text-2xl font-bold text-blue-600 mb-4">Get Started</h2>
            <p class="text-gray-600 dark:text-gray-300 mb-6">
                Register an account now and start organizing your first event in minutes.
            </p>

            @guest
            <a href="{{ route('register') }}" class="inline-block bg-pink-500 hover:bg-pink-600 text-white font-semibold px-6 py-3 rounded-lg mb-3 transition-all">
                Create Account
            </a>
            <br>
            <a href="{{ route('login') }}" class="inline-block bg-transparent border border-pink-500 hover:bg-pink-500 hover:text-white text-pink-500 font-semibold px-6 py-3 rounded-lg transition-all">
                Login
            </a>
            @else
            <a href="{{ route('dashboard') }}" class="inline-block bg-pink-500 hover:bg-pink-600 text-white font-semibold px-6 py-3 rounded-lg transition-all">
                Go to Dashboard
            </a>
            @endguest
        </div>

    </main>

    <footer class="mt-16 text-center text-gray-500 dark:text-gray-400 text-sm">
        &copy; {{ date('Y') }} EMTS. All rights reserved.
    </footer>

</body>
</html>
