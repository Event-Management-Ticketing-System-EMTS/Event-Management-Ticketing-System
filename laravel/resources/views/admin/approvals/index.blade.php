<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Approvals - Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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

  {{-- Header --}}
  <header class="flex items-center justify-between px-6 py-4 border-b border-slate-800 bg-slate-900/70 backdrop-blur-md">
    <div class="flex items-center gap-3">
      <div class="h-9 w-9 rounded-xl bg-yellow-500/20 ring-1 ring-yellow-400/40 grid place-items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
      </div>
      <h1 class="text-xl font-bold text-cyan-300">Event Approvals</h1>
      <span class="px-3 py-1 bg-yellow-500/20 text-yellow-300 rounded-full text-xs font-medium border border-yellow-400/20">
        Admin Dashboard
      </span>
    </div>

    <div class="flex items-center gap-4">
      <span class="text-sm text-slate-400 hidden sm:inline">{{ Auth::user()->name }}</span>
      <a href="{{ route('dashboard') }}"
         class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 border border-cyan-400/20 text-sm transition">
        Back to Dashboard
      </a>
    </div>
  </header>

  <main class="max-w-7xl mx-auto p-6 space-y-6">

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 text-emerald-300 px-6 py-4 shadow-lg">
            <div class="flex items-center gap-3">
              <svg class="h-5 w-5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
              </svg>
              {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl border border-red-400/20 bg-red-500/10 text-red-300 px-6 py-4 shadow-lg">
            <div class="flex items-center gap-3">
              <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
              </svg>
              {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- Approval Statistics --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="rounded-2xl border border-yellow-400/20 bg-slate-900/80 backdrop-blur-md p-5 shadow-lg">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-400">Pending Approval</p>
            <p class="mt-2 text-2xl font-semibold text-yellow-300">{{ $stats['pending'] }}</p>
          </div>
          <div class="h-12 w-12 rounded-xl bg-yellow-500/20 ring-1 ring-yellow-400/40 grid place-items-center">
            <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-emerald-400/20 bg-slate-900/80 backdrop-blur-md p-5 shadow-lg">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-400">Approved</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-300">{{ $stats['approved'] }}</p>
          </div>
          <div class="h-12 w-12 rounded-xl bg-emerald-500/20 ring-1 ring-emerald-400/40 grid place-items-center">
            <svg class="h-6 w-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
        </div>
      </div>
      <div class="rounded-2xl border border-red-400/20 bg-slate-900/80 backdrop-blur-md p-5 shadow-lg">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-400">Rejected</p>
            <p class="mt-2 text-2xl font-semibold text-red-300">{{ $stats['rejected'] }}</p>
          </div>
          <div class="h-12 w-12 rounded-xl bg-red-500/20 ring-1 ring-red-400/40 grid place-items-center">
            <svg class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-5 shadow-lg">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-400">Total Events</p>
            <p class="mt-2 text-2xl font-semibold text-cyan-300">{{ $stats['total'] }}</p>
          </div>
          <div class="h-12 w-12 rounded-xl bg-cyan-500/20 ring-1 ring-cyan-400/40 grid place-items-center">
            <svg class="h-6 w-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
          </div>
        </div>
      </div>
    </section>
                </div>
            </div>
    {{-- Pending Events List --}}
    <section class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md shadow-lg overflow-hidden">
      <div class="bg-gradient-to-r from-cyan-500 to-sky-500 px-6 py-4">
        <h2 class="text-xl font-semibold text-white flex items-center gap-3">
          <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          Pending Events for Approval
        </h2>
      </div>

      @if($pendingEvents->count() > 0)
        <div class="p-6">
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead class="text-slate-400">
                <tr class="text-left border-b border-white/10">
                  <th class="py-3 pr-4">Event</th>
                  <th class="py-3 pr-4">Organizer</th>
                  <th class="py-3 pr-4">Date</th>
                  <th class="py-3 pr-4">Tickets</th>
                  <th class="py-3 pr-4">Price</th>
                  <th class="py-3 pr-4">Created</th>
                  <th class="py-3">Actions</th>
                </tr>
              </thead>
              <tbody class="text-slate-200">
                @foreach($pendingEvents as $event)
                  <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                    <td class="py-4 pr-4">
                      <div>
                        <h3 class="font-semibold text-cyan-300">{{ $event->title }}</h3>
                        <p class="text-sm text-slate-400 truncate max-w-xs">{{ $event->description }}</p>
                      </div>
                    </td>
                    <td class="py-4 pr-4">
                      <div class="text-slate-200">{{ $event->organizer->name }}</div>
                      <div class="text-xs text-slate-400">{{ $event->organizer->email }}</div>
                    </td>
                    <td class="py-4 pr-4 text-slate-300">
                      {{ $event->event_date->format('M d, Y') }}
                    </td>
                    <td class="py-4 pr-4 text-slate-300">
                      {{ number_format($event->total_tickets) }}
                    </td>
                                    <td class="py-4 pr-4 text-emerald-300 font-semibold">
                      ${{ number_format($event->price, 2) }}
                    </td>
                    <td class="py-4 pr-4 text-slate-400 text-sm">
                      {{ $event->created_at->diffForHumans() }}
                    </td>
                    <td class="py-4">
                      <div class="flex gap-2">
                        <a href="{{ route('admin.approvals.show', $event) }}"
                           class="px-3 py-1 rounded-lg bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-400 hover:to-blue-400 text-white font-medium text-xs transition shadow-lg">
                          Review
                        </a>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          {{-- Pagination --}}
          <div class="mt-6">
            {{ $pendingEvents->links() }}
          </div>
        </div>
      @else
        <div class="p-12 text-center">
          <div class="flex flex-col items-center gap-4">
            <div class="h-16 w-16 rounded-full bg-emerald-500/20 ring-1 ring-emerald-400/40 grid place-items-center">
              <svg class="h-8 w-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-medium text-emerald-300 mb-2">No Pending Approvals</h3>
              <p class="text-slate-400">All events have been reviewed. Great job!</p>
            </div>
          </div>
        </div>
      @endif
    </section>

  </main>
</body>
</html>
