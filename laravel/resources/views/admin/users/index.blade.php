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
      <h1 class="text-xl font-bold text-cyan-300">User Management</h1>
    </div>

    <div class="flex items-center gap-4">
      <a href="{{ route('events.index') }}" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 border border-cyan-400/20 text-sm transition">
        Manage Events
      </a>
      <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 border border-cyan-400/20 text-sm transition">
        Back to Dashboard
      </a>
    </div>
  </header>

  <main class="max-w-7xl mx-auto p-6 space-y-6">
    {{-- Page header with stats --}}
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-white">All Users</h1>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
      <div class="rounded-xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-4 shadow-lg">
        <div class="flex items-center gap-3">
          <div class="h-8 w-8 rounded-lg bg-cyan-500/20 grid place-items-center">
            <span class="text-cyan-400 text-sm font-bold">👥</span>
          </div>
          <div>
            <p class="text-slate-400 text-sm">Total Users</p>
            <p class="text-white font-semibold">{{ $stats['total'] }}</p>
          </div>
        </div>
      </div>

      <div class="rounded-xl border border-red-400/20 bg-slate-900/80 backdrop-blur-md p-4 shadow-lg">
        <div class="flex items-center gap-3">
          <div class="h-8 w-8 rounded-lg bg-red-500/20 grid place-items-center">
            <span class="text-red-400 text-sm font-bold">👑</span>
          </div>
          <div>
            <p class="text-slate-400 text-sm">Admins</p>
            <p class="text-white font-semibold">{{ $stats['admins'] }}</p>
          </div>
        </div>
      </div>

      <div class="rounded-xl border border-yellow-400/20 bg-slate-900/80 backdrop-blur-md p-4 shadow-lg">
        <div class="flex items-center gap-3">
          <div class="h-8 w-8 rounded-lg bg-yellow-500/20 grid place-items-center">
            <span class="text-yellow-400 text-sm font-bold">🎪</span>
          </div>
          <div>
            <p class="text-slate-400 text-sm">Organizers</p>
            <p class="text-white font-semibold">{{ $stats['organizers'] }}</p>
          </div>
        </div>
      </div>

      <div class="rounded-xl border border-green-400/20 bg-slate-900/80 backdrop-blur-md p-4 shadow-lg">
        <div class="flex items-center gap-3">
          <div class="h-8 w-8 rounded-lg bg-green-500/20 grid place-items-center">
            <span class="text-green-400 text-sm font-bold">👤</span>
          </div>
          <div>
            <p class="text-slate-400 text-sm">Regular Users</p>
            <p class="text-white font-semibold">{{ $stats['users'] }}</p>
          </div>
        </div>
      </div>

      <div class="rounded-xl border border-purple-400/20 bg-slate-900/80 backdrop-blur-md p-4 shadow-lg">
        <div class="flex items-center gap-3">
          <div class="h-8 w-8 rounded-lg bg-purple-500/20 grid place-items-center">
            <span class="text-purple-400 text-sm font-bold">🆕</span>
          </div>
          <div>
            <p class="text-slate-400 text-sm">Recent (7d)</p>
            <p class="text-white font-semibold">{{ $stats['recent'] }}</p>
          </div>
        </div>
      </div>
    </div>

    {{-- Sorting Controls --}}
    <x-sorting-controls
        :action="route('users.index')"
        :sort-options="$sortOptions"
        :current-sort="$sortBy"
        :current-direction="$sortDirection"
        :total-count="$users->count()"
        :show-reset="!$isDefaultSort"
    />

    {{-- Status messages --}}
    @if (session('success'))
    <div class="rounded-xl border border-green-400/30 bg-green-400/10 p-4">
      <div class="flex">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400 mr-2" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <span class="text-green-300">{{ session('success') }}</span>
      </div>
    </div>
    @endif

    {{-- Users table --}}
    <div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-6 shadow-lg overflow-x-auto">
      @if($users->isEmpty())
        <div class="text-center py-8">
          <p class="text-slate-400 mb-4">No users found.</p>
        </div>
      @else
        <table class="w-full" id="usersTable">
          <thead>
            <tr class="border-b border-slate-800">
              <th class="px-4 py-3 text-left">
                <a href="{{ route('users.index', ['sort' => 'name', 'direction' => $sortBy === 'name' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}"
                   class="flex items-center gap-1 text-sm font-semibold text-cyan-300 hover:text-cyan-200 transition-colors">
                  User
                  @if($sortBy === 'name')
                    @if($sortDirection === 'asc')
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      </svg>
                    @else
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                      </svg>
                    @endif
                  @endif
                </a>
              </th>
              <th class="px-4 py-3 text-left">
                <a href="{{ route('users.index', ['sort' => 'email', 'direction' => $sortBy === 'email' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}"
                   class="flex items-center gap-1 text-sm font-semibold text-cyan-300 hover:text-cyan-200 transition-colors">
                  Email
                  @if($sortBy === 'email')
                    @if($sortDirection === 'asc')
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      </svg>
                    @else
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                      </svg>
                    @endif
                  @endif
                </a>
              </th>
              <th class="px-4 py-3 text-left">
                <a href="{{ route('users.index', ['sort' => 'role', 'direction' => $sortBy === 'role' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}"
                   class="flex items-center gap-1 text-sm font-semibold text-cyan-300 hover:text-cyan-200 transition-colors">
                  Role
                  @if($sortBy === 'role')
                    @if($sortDirection === 'asc')
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      </svg>
                    @else
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                      </svg>
                    @endif
                  @endif
                </a>
              </th>
              <th class="px-4 py-3 text-left">
                <a href="{{ route('users.index', ['sort' => 'email_verified', 'direction' => $sortBy === 'email_verified' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}"
                   class="flex items-center gap-1 text-sm font-semibold text-cyan-300 hover:text-cyan-200 transition-colors">
                  Status
                  @if($sortBy === 'email_verified')
                    @if($sortDirection === 'asc')
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      </svg>
                    @else
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                      </svg>
                    @endif
                  @endif
                </a>
              </th>
              <th class="px-4 py-3 text-left">
                <a href="{{ route('users.index', ['sort' => 'created_at', 'direction' => $sortBy === 'created_at' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}"
                   class="flex items-center gap-1 text-sm font-semibold text-cyan-300 hover:text-cyan-200 transition-colors">
                  Joined
                  @if($sortBy === 'created_at')
                    @if($sortDirection === 'asc')
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      </svg>
                    @else
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                      </svg>
                    @endif
                  @endif
                </a>
              </th>
              <th class="px-4 py-3 text-right text-sm font-semibold text-cyan-300">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($users as $user)
            <tr class="border-b border-slate-800 hover:bg-slate-800/50 transition-colors">
              <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                  <div class="h-8 w-8 rounded-full bg-gradient-to-r from-cyan-500 to-sky-500 grid place-items-center text-white text-sm font-bold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                  </div>
                  <div class="font-medium">{{ $user->name }}</div>
                </div>
              </td>
              <td class="px-4 py-3 text-slate-300">{{ $user->email }}</td>
              <td class="px-4 py-3">
                @if($user->role === 'admin')
                  <span class="inline-flex rounded-full bg-red-500/10 px-2 py-1 text-xs font-medium text-red-400 ring-1 ring-inset ring-red-500/30">👑 Admin</span>
                @elseif($user->role === 'organizer')
                  <span class="inline-flex rounded-full bg-yellow-500/10 px-2 py-1 text-xs font-medium text-yellow-400 ring-1 ring-inset ring-yellow-500/30">🎪 Organizer</span>
                @else
                  <span class="inline-flex rounded-full bg-green-500/10 px-2 py-1 text-xs font-medium text-green-400 ring-1 ring-inset ring-green-500/30">👤 User</span>
                @endif
              </td>
              <td class="px-4 py-3">
                @if($user->email_verified)
                  <span class="inline-flex rounded-full bg-green-500/10 px-2 py-1 text-xs font-medium text-green-400 ring-1 ring-inset ring-green-500/30">✅ Verified</span>
                @else
                  <span class="inline-flex rounded-full bg-amber-500/10 px-2 py-1 text-xs font-medium text-amber-400 ring-1 ring-inset ring-amber-500/30">⏳ Pending</span>
                @endif
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-slate-400">{{ $user->created_at->format('M d, Y') }}</td>
              <td class="px-4 py-3 text-right space-x-1 whitespace-nowrap">
                <a href="{{ route('users.show', $user->id) }}" class="inline-flex items-center px-2 py-1 rounded text-xs font-medium text-cyan-400 hover:bg-slate-800 transition-colors">
                  View Details
                </a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      @endif
    </div>
  </main>
</div>
@endsection
