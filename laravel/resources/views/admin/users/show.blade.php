@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-slate-100 antialiased">
  {{-- Background glow + grid --}}
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

  {{-- Topbar --}}
  <header class="flex items-center justify-between px-6 py-4 border-b border-slate-800 bg-slate-900/70 backdrop-blur-md">
    <div class="flex items-center gap-3">
      <div class="h-9 w-9 rounded-xl bg-cyan-500/20 ring-1 ring-cyan-400/40 grid place-items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-cyan-400" viewBox="0 0 24 24" fill="currentColor">
          <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
      </div>
      <h1 class="text-xl font-bold text-cyan-300">User Details</h1>
    </div>

    <div class="flex items-center gap-4">
      <a href="{{ route('users.index') }}" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 border border-cyan-400/20 text-sm transition">
        Back to Users
      </a>
      <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 border border-cyan-400/20 text-sm transition">
        Dashboard
      </a>
    </div>
  </header>

  <main class="max-w-4xl mx-auto p-6 space-y-6">
    {{-- User Profile Card --}}
    <div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-6 shadow-lg">
      <div class="flex items-start gap-6">
        {{-- Avatar --}}
        <div class="h-20 w-20 rounded-full bg-gradient-to-r from-cyan-500 to-sky-500 grid place-items-center text-white text-2xl font-bold flex-shrink-0">
          {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>

        {{-- User Info --}}
        <div class="flex-1 space-y-4">
          <div>
            <h1 class="text-2xl font-bold text-white">{{ $user->name }}</h1>
            <p class="text-slate-400">{{ $user->email }}</p>
          </div>

          <div class="flex items-center gap-4">
            {{-- Role Badge --}}
            @if($user->role === 'admin')
              <span class="inline-flex rounded-full bg-red-500/10 px-3 py-1 text-sm font-medium text-red-400 ring-1 ring-inset ring-red-500/30">👑 Admin</span>
            @elseif($user->role === 'organizer')
              <span class="inline-flex rounded-full bg-yellow-500/10 px-3 py-1 text-sm font-medium text-yellow-400 ring-1 ring-inset ring-yellow-500/30">🎪 Organizer</span>
            @else
              <span class="inline-flex rounded-full bg-green-500/10 px-3 py-1 text-sm font-medium text-green-400 ring-1 ring-inset ring-green-500/30">👤 User</span>
            @endif

            {{-- Verification Status --}}
            @if($user->email_verified)
              <span class="inline-flex rounded-full bg-green-500/10 px-3 py-1 text-sm font-medium text-green-400 ring-1 ring-inset ring-green-500/30">✅ Email Verified</span>
            @else
              <span class="inline-flex rounded-full bg-amber-500/10 px-3 py-1 text-sm font-medium text-amber-400 ring-1 ring-inset ring-amber-500/30">⏳ Email Pending</span>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- Account Details --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      {{-- Account Information --}}
      <div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-6 shadow-lg">
        <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
          <span class="text-cyan-400">📋</span>
          Account Information
        </h2>

        <div class="space-y-4">
          <div class="flex justify-between items-center py-2 border-b border-slate-800">
            <span class="text-slate-400">User ID</span>
            <span class="text-white font-mono">#{{ $user->id }}</span>
          </div>

          <div class="flex justify-between items-center py-2 border-b border-slate-800">
            <span class="text-slate-400">Full Name</span>
            <span class="text-white">{{ $user->name }}</span>
          </div>

          <div class="flex justify-between items-center py-2 border-b border-slate-800">
            <span class="text-slate-400">Email Address</span>
            <span class="text-white">{{ $user->email }}</span>
          </div>

          <div class="flex justify-between items-center py-2 border-b border-slate-800">
            <span class="text-slate-400">Account Role</span>
            <span class="text-white capitalize">{{ ucfirst($user->role) }}</span>
          </div>

          <div class="flex justify-between items-center py-2">
            <span class="text-slate-400">Avatar</span>
            <span class="text-white">
              @if($user->avatar_path)
                <span class="text-green-400">✅ Custom Avatar</span>
              @else
                <span class="text-slate-400">🎭 Default Avatar</span>
              @endif
            </span>
          </div>
        </div>
      </div>

      {{-- Account Timestamps --}}
      <div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-6 shadow-lg">
        <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
          <span class="text-cyan-400">⏰</span>
          Account Timeline
        </h2>

        <div class="space-y-4">
          <div class="flex justify-between items-center py-2 border-b border-slate-800">
            <span class="text-slate-400">Joined</span>
            <span class="text-white">{{ $user->created_at->format('M d, Y \a\t H:i') }}</span>
          </div>

          <div class="flex justify-between items-center py-2 border-b border-slate-800">
            <span class="text-slate-400">Last Updated</span>
            <span class="text-white">{{ $user->updated_at->format('M d, Y \a\t H:i') }}</span>
          </div>

          <div class="flex justify-between items-center py-2 border-b border-slate-800">
            <span class="text-slate-400">Account Age</span>
            <span class="text-white">{{ $user->created_at->diffForHumans() }}</span>
          </div>

          <div class="flex justify-between items-center py-2">
            <span class="text-slate-400">Remember Token</span>
            <span class="text-white">
              @if($user->remember_token)
                <span class="text-green-400">✅ Active</span>
              @else
                <span class="text-slate-400">❌ None</span>
              @endif
            </span>
          </div>
        </div>
      </div>
    </div>

    {{-- Quick Stats (if organizer) --}}
    @if($user->role === 'organizer')
    <div class="rounded-2xl border border-yellow-400/20 bg-slate-900/80 backdrop-blur-md p-6 shadow-lg">
      <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
        <span class="text-yellow-400">🎪</span>
        Organizer Statistics
      </h2>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="text-center">
          <div class="text-2xl font-bold text-yellow-400">{{ $user->events->count() ?? 0 }}</div>
          <div class="text-slate-400 text-sm">Total Events</div>
        </div>

        <div class="text-center">
          <div class="text-2xl font-bold text-green-400">{{ $user->events->where('status', 'published')->count() ?? 0 }}</div>
          <div class="text-slate-400 text-sm">Published Events</div>
        </div>

        <div class="text-center">
          <div class="text-2xl font-bold text-cyan-400">{{ $user->events->sum('tickets_sold') ?? 0 }}</div>
          <div class="text-slate-400 text-sm">Tickets Sold</div>
        </div>
      </div>
    </div>
    @endif

    {{-- Security Warning for Admins --}}
    @if($user->role === 'admin')
    <div class="rounded-2xl border border-red-400/20 bg-red-900/20 backdrop-blur-md p-6 shadow-lg">
      <h2 class="text-lg font-semibold text-red-400 mb-2 flex items-center gap-2">
        <span>⚠️</span>
        Administrator Account
      </h2>
      <p class="text-red-300 text-sm">
        This user has administrative privileges and can access all system features including user management,
        event oversight, and system configuration. Exercise caution when modifying admin accounts.
      </p>
    </div>
    @endif
  </main>
</div>
@endsection
