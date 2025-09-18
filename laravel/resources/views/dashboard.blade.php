<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Dashboard - {{ config('app.name', 'Laravel') }}</title>

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
        <div class="min-h-screen bg-slate-100 dark:bg-slate-900">
            <nav class="bg-white dark:bg-slate-800 shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="flex-shrink-0 flex items-center">
                                <h1 class="text-2xl font-bold tracking-tighter text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">EventHub</h1>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="text-sm text-slate-500 dark:text-slate-400 mr-4">
                                Welcome, {{ Auth::user()->name }}
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-sm text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-white">
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

            <main>
                <div class="py-12">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                                <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-200">
                                    Dashboard
                                </h2>
                                <p class="mt-2 text-slate-600 dark:text-slate-400">
                                    You're logged in!
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
