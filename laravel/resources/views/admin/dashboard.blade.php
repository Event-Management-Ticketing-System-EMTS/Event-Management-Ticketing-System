{{-- resources/views/admin/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Dashboard</title>
  @vite('resources/css/app.css')
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-slate-100 antialiased">

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
          <path d="M12 3l9 4.5v9L12 21 3 16.5v-9L12 3zM5 8l7 3 7-3-7-3-7 3zm7 5l7-3v5l-7 3-7-3v-5l7 3z"/>
        </svg>
      </div>
      <h1 class="text-xl font-bold text-cyan-300">Admin Dashboard</h1>
    </div>

    <div class="flex items-center gap-4">
      <span class="text-sm text-slate-400 hidden sm:inline">Hello, {{ Auth::user()->name }}</span>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
          class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 border border-cyan-400/20 text-sm transition">
          Logout
        </button>
      </form>
    </div>
  </header>

  <main class="max-w-7xl mx-auto p-6 space-y-6">

    {{-- KPI cards --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      @php
        $activeEventsCount = \App\Models\Event::where('status', 'published')->count();
        $totalTicketsSold = \App\Models\Event::sum('tickets_sold');
        $totalRevenue = \App\Models\Event::sum(\DB::raw('tickets_sold * price'));
        $pendingEvents = \App\Models\Event::where('status', 'draft')->count();
      @endphp

      <div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-5 shadow-lg">
        <p class="text-sm text-slate-400">Active Events</p>
        <p class="mt-2 text-2xl font-semibold text-cyan-300">{{ $activeEventsCount }}</p>
      </div>

      <div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-5 shadow-lg">
        <p class="text-sm text-slate-400">Tickets Sold</p>
        <p class="mt-2 text-2xl font-semibold text-cyan-300">{{ $totalTicketsSold }}</p>
      </div>

      <div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-5 shadow-lg">
        <p class="text-sm text-slate-400">Total Revenue</p>
        <p class="mt-2 text-2xl font-semibold text-cyan-300">${{ number_format($totalRevenue, 2) }}</p>
      </div>

      <div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-5 shadow-lg">
        <p class="text-sm text-slate-400">Draft Events</p>
        <p class="mt-2 text-2xl font-semibold text-cyan-300">{{ $pendingEvents }}</p>
      </div>
    </section>

    {{-- Quick Actions --}}
    <section class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-6 shadow-lg">
      <h2 class="text-lg font-semibold text-cyan-300 mb-6 flex items-center gap-3">
        <svg class="h-6 w-6 text-cyan-400" fill="currentColor" viewBox="0 0 20 20">
          <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
        </svg>
        Quick Actions
      </h2>

      {{-- Primary Actions Row --}}
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
        <a href="{{ url('/events/create') }}"
           class="group relative overflow-hidden rounded-2xl bg-gradient-to-r from-cyan-500 to-sky-500 hover:from-cyan-400 hover:to-sky-400 p-6 text-center font-medium shadow-lg transition-all duration-300 hover:shadow-xl hover:scale-[1.02]">
          <div class="flex flex-col items-center gap-3">
            <div class="p-3 rounded-full bg-white/20 group-hover:bg-white/30 transition-colors">
              <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
            </div>
            <span class="text-white font-semibold">Create Event</span>
            <span class="text-xs text-white/80">Add new event</span>
          </div>
        </a>

        <a href="{{ route('admin.approvals.index') }}"
           class="group relative overflow-hidden rounded-2xl bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-400 hover:to-orange-400 p-6 text-center font-medium shadow-lg transition-all duration-300 hover:shadow-xl hover:scale-[1.02]">
          <div class="flex flex-col items-center gap-3">
            <div class="p-3 rounded-full bg-white/20 group-hover:bg-white/30 transition-colors">
              <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
            </div>
            <span class="text-white font-semibold">Event Approvals</span>
            @php $pendingApprovals = \App\Models\Event::where('approval_status', 'pending')->count(); @endphp
            @if($pendingApprovals > 0)
              <span class="px-2 py-1 rounded-full bg-white/20 text-xs text-white font-medium">{{ $pendingApprovals }} pending</span>
            @else
              <span class="text-xs text-white/80">All up to date</span>
            @endif
          </div>
        </a>

        <a href="{{ route('admin.payments.index') }}"
           class="group relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-500 to-green-500 hover:from-emerald-400 hover:to-green-400 p-6 text-center font-medium shadow-lg transition-all duration-300 hover:shadow-xl hover:scale-[1.02]">
          <div class="flex flex-col items-center gap-3">
            <div class="p-3 rounded-full bg-white/20 group-hover:bg-white/30 transition-colors">
              <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
              </svg>
            </div>
            <span class="text-white font-semibold">Payment Management</span>
            @php $pendingPayments = \App\Models\Ticket::where('payment_status', 'pending')->count(); @endphp
            @if($pendingPayments > 0)
              <span class="px-2 py-1 rounded-full bg-white/20 text-xs text-white font-medium">{{ $pendingPayments }} pending</span>
            @else
              <span class="text-xs text-white/80">All up to date</span>
            @endif
          </div>
        </a>
      </div>

      {{-- Secondary Actions Row --}}
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <a href="{{ url('/events') }}"
           class="group flex items-center gap-3 p-4 rounded-xl bg-slate-800/60 hover:bg-slate-700/80 border border-cyan-400/10 hover:border-cyan-400/30 transition-all duration-300">
          <div class="p-2 rounded-lg bg-cyan-500/20 group-hover:bg-cyan-500/30 transition-colors">
            <svg class="h-4 w-4 text-cyan-400" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <div class="text-sm font-medium text-slate-200 group-hover:text-white transition-colors">Manage Events</div>
            <div class="text-xs text-slate-400">Edit & organize</div>
          </div>
        </a>

        <a href="{{ route('users.index') }}"
           class="group flex items-center gap-3 p-4 rounded-xl bg-slate-800/60 hover:bg-slate-700/80 border border-cyan-400/10 hover:border-cyan-400/30 transition-all duration-300">
          <div class="p-2 rounded-lg bg-purple-500/20 group-hover:bg-purple-500/30 transition-colors">
            <svg class="h-4 w-4 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <div class="text-sm font-medium text-slate-200 group-hover:text-white transition-colors">Manage Users</div>
            <div class="text-xs text-slate-400">Roles & permissions</div>
          </div>
        </a>

        <a href="{{ route('events.statistics') }}"
           class="group flex items-center gap-3 p-4 rounded-xl bg-slate-800/60 hover:bg-slate-700/80 border border-cyan-400/10 hover:border-cyan-400/30 transition-all duration-300">
          <div class="p-2 rounded-lg bg-blue-500/20 group-hover:bg-blue-500/30 transition-colors">
            <svg class="h-4 w-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
              <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <div class="text-sm font-medium text-slate-200 group-hover:text-white transition-colors">Event Statistics</div>
            <div class="text-xs text-slate-400">Analytics & reports</div>
          </div>
        </a>

        <a href="{{ url('/bookings') }}"
           class="group flex items-center gap-3 p-4 rounded-xl bg-slate-800/60 hover:bg-slate-700/80 border border-cyan-400/10 hover:border-cyan-400/30 transition-all duration-300">
          <div class="p-2 rounded-lg bg-indigo-500/20 group-hover:bg-indigo-500/30 transition-colors">
            <svg class="h-4 w-4 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <div class="text-sm font-medium text-slate-200 group-hover:text-white transition-colors">View Bookings</div>
            <div class="text-xs text-slate-400">Customer tickets</div>
          </div>
        </a>
      </div>
    </section>

    {{-- Recent Events table (placeholder data) --}}
    <section class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-6 shadow-lg overflow-x-auto">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-cyan-300">Recent Events</h2>
        <a href="{{ url('/events') }}" class="text-sm text-cyan-300 hover:text-cyan-200">View all</a>
      </div>
      <table class="min-w-full text-sm">
        <thead class="text-slate-400">
          <tr class="text-left border-b border-white/10">
            <th class="py-2 pr-4">Event</th>
            <th class="py-2 pr-4">Date</th>
            <th class="py-2 pr-4">Venue</th>
            <th class="py-2 pr-4">Tickets</th>
            <th class="py-2 pr-4">Status</th>
            <th class="py-2">Actions</th>
          </tr>
        </thead>
        <tbody class="text-slate-200">
          @php
            $dashboardEvents = \App\Models\Event::orderBy('created_at', 'desc')->take(3)->get();
        @endphp

        @forelse ($dashboardEvents as $event)
          <tr class="border-b border-white/5">
            <td class="py-3 pr-4">{{ $event->title }}</td>
            <td class="py-3 pr-4">{{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}</td>
            <td class="py-3 pr-4">{{ $event->venue }}</td>
            <td class="py-3 pr-4">{{ $event->tickets_sold }}/{{ $event->total_tickets }}</td>
            <td class="py-3 pr-4">
              <span class="px-2 py-1 text-xs rounded-full
                {{ $event->status === 'published' ? 'bg-emerald-500/20 text-emerald-300' :
                  ($event->status === 'cancelled' ? 'bg-red-500/20 text-red-300' : 'bg-yellow-500/20 text-yellow-300') }}">
                {{ ucfirst($event->status) }}
              </span>
            </td>
            <td class="py-3 flex gap-2">
              <a href="{{ route('events.edit', $event->id) }}" class="text-cyan-300 hover:text-cyan-200">Edit</a>
              <a href="{{ route('events.show', $event->id) }}" class="text-slate-300 hover:text-slate-100">View</a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="py-6 text-center text-slate-400">No events found. <a href="{{ route('events.create') }}" class="text-cyan-300 hover:underline">Create your first event</a>.</td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </section>

  </main>
</body>
</html>
