<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | Event Management</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-950 to-slate-900 text-white antialiased">

    {{-- Background Glow --}}
    <div class="fixed inset-0 -z-10">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(6,182,212,0.15),transparent_60%)]"></div>
        <div class="absolute inset-0 opacity-10 bg-slate-800/50"></div>
    </div>

    {{-- Hero Section --}}
    <main class="flex flex-col justify-center items-center text-center min-h-screen px-4">
        <div class="max-w-3xl space-y-6">
            <h1 class="text-5xl md:text-6xl font-extrabold text-cyan-400 animate-fadeIn">
                Welcome to Event Management System
            </h1>
            <p class="text-lg md:text-xl text-slate-300 animate-fadeIn delay-200">
                Plan, manage, and book events seamlessly. Your one-stop solution for events.
            </p>
            <div class="flex flex-col md:flex-row justify-center gap-4 mt-6 animate-fadeIn delay-400">
                <a href="{{ route('register.show') }}"
                   class="px-6 py-3 rounded-lg bg-cyan-500 hover:bg-cyan-600 transition text-white font-medium shadow-md">
                    Get Started
                </a>
                <a href="{{ route('login.show') }}"
                   class="px-6 py-3 rounded-lg border border-cyan-400 hover:bg-cyan-500 hover:text-white transition text-cyan-400 font-medium shadow-md">
                    Log In
                </a>
            </div>
        </div>

        {{-- Optional Illustration --}}
        <div class="mt-12">
            <img src="{{ asset('images/event-illustration.svg') }}" alt="Event Illustration" class="w-full max-w-lg mx-auto animate-fadeIn delay-600">
        </div>
    </main>

    {{-- Animations --}}
    <style>
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(10px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn { animation: fadeIn 1s forwards; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-600 { animation-delay: 0.6s; }
    </style>

</body>
</html>
