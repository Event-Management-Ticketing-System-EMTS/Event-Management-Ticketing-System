{{-- resources/views/user/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>User Dashboard</title>
  @vite('resources/css/app.css')
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-slate-100 antialiased">

  {{-- Background Glow --}}
  <div class="fixed inset-0 -z-10">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(6,182,212,0.15),transparent_70%)]"></div>
    <div class="absolute inset-0 opacity-[0.05] [mask-image:linear-gradient(to_bottom,black,transparent)]">
      <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
            <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="0.5"/>
          </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#grid)" />
      </svg>
    </div>
  </div>

  {{-- Navbar --}}
  <header class="flex items-center justify-between px-6 py-4 border-b border-slate-800 bg-slate-900/70 backdrop-blur-md">
    <h1 class="text-xl font-bold text-cyan-400">User Dashboard</h1>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit"
        class="px-4 py-2 rounded-lg bg-gradient-to-r from-cyan-500 to-sky-500 hover:from-cyan-400 hover:to-sky-400 
               shadow-md shadow-cyan-900/40 text-sm font-medium transition">
        Logout
      </button>
    </form>
  </header>

  {{-- Main Content --}}
  <main class="max-w-6xl mx-auto p-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

      {{-- Profile Card --}}
      <div class="md:col-span-1 rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md shadow-lg p-6">
        <div class="flex flex-col items-center">
          <div class="h-20 w-20 rounded-full bg-gradient-to-br from-cyan-500 to-sky-500 flex items-center justify-center text-white text-2xl font-bold">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
          </div>
          <h2 class="mt-4 text-lg font-semibold text-cyan-300">{{ auth()->user()->name }}</h2>
          <p class="text-sm text-slate-400">{{ auth()->user()->email }}</p>
          <span class="mt-2 text-xs px-3 py-1 rounded-full bg-cyan-500/20 text-cyan-300">
            Role: {{ ucfirst(auth()->user()->role) }}
          </span>
        </div>
      </div>

      {{-- Bookings / Events --}}
      <div class="md:col-span-2 space-y-6">

        {{-- Upcoming Events --}}
        <div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md shadow-lg p-6">
          <h3 class="text-lg font-semibold text-cyan-300 mb-4">Your Upcoming Events</h3>
          <ul class="space-y-3 text-sm text-slate-300">
            <li class="flex items-center justify-between">
              <span>Music Concert</span>
              <span class="text-xs text-slate-400">12 Oct 2025</span>
            </li>
            <li class="flex items-center justify-between">
              <span>Tech Conference</span>
              <span class="text-xs text-slate-400">20 Nov 2025</span>
            </li>
            <li class="flex items-center justify-between">
              <span>Sports Meetup</span>
              <span class="text-xs text-slate-400">05 Dec 2025</span>
            </li>
          </ul>
        </div>

        {{-- Quick Actions --}}
        <div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md shadow-lg p-6">
          <h3 class="text-lg font-semibold text-cyan-300 mb-4">Quick Actions</h3>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <button class="p-3 rounded-lg bg-gradient-to-r from-cyan-500 to-sky-500 hover:from-cyan-400 hover:to-sky-400 text-sm font-medium shadow-md">
              Browse Events
            </button>
            <button class="p-3 rounded-lg bg-slate-800 hover:bg-slate-700 text-sm font-medium border border-cyan-400/20">
              My Tickets
            </button>
            <button class="p-3 rounded-lg bg-slate-800 hover:bg-slate-700 text-sm font-medium border border-cyan-400/20">
              Profile Settings
            </button>
            <button class="p-3 rounded-lg bg-slate-800 hover:bg-slate-700 text-sm font-medium border border-cyan-400/20">
              Support
            </button>
          </div>
        </div>

      </div>
    </div>
  </main>
</body>
</html>
