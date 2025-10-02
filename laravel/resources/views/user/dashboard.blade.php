{{-- resources/views/user/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>User Dashboard</title>
  @vite('resources/css/app.css')
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-slate-100 antialiased">

  {{-- Background: glow + grid --}}
  <div class="fixed inset-0 -z-10">
    <div class="absolute inset-0 bg-[radial-gradient(80%_60%_at_90%_0%,rgba(34,211,238,0.15),transparent_60%)]"></div>
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

  {{-- Top bar --}}
  <header class="sticky top-0 z-20 bg-slate-900/70 backdrop-blur-md border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center gap-4">
      <div class="flex items-center gap-3">
        <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-cyan-500/15 ring-1 ring-cyan-400/30">
          {{-- logo mark --}}
          <svg viewBox="0 0 24 24" class="h-4 w-4"><path d="M12 3l7 4v10l-7 4-7-4V7l7-4z" fill="currentColor"/></svg>
        </span>
        <div class="leading-tight">
          <p class="text-xs text-slate-400">Welcome back</p>
          <h1 class="text-lg font-semibold tracking-tight text-cyan-300">User Dashboard</h1>
        </div>
      </div>

      <div class="ml-auto flex items-center gap-3">
        <a href="{{ route('events.index') }}"
           class="hidden md:inline-flex px-3 py-2 text-sm rounded-lg border border-cyan-400/20 bg-slate-800 hover:bg-slate-700 transition">Browse Events</a>

        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit"
                  class="px-4 py-2 rounded-lg bg-gradient-to-r from-cyan-500 to-sky-500 hover:from-cyan-400 hover:to-sky-400
                         text-sm font-medium shadow-md shadow-cyan-900/40 transition">
            Logout
          </button>
        </form>
      </div>
    </div>
  </header>

  {{-- Content --}}
  <main class="max-w-7xl mx-auto px-6 py-8 space-y-8">
    @php
      $user      = auth()->user();
      /** These are expected from controller; safe fallbacks for view-only previews */
      $stats     = $stats     ?? ['upcoming'=>0, 'tickets'=>0, 'spent'=>0, 'notifications'=>0];
      $upcoming  = $upcoming  ?? [];   // array of ['title','date','venue']
      $tickets   = $tickets   ?? [];   // array of ['code','event','date','status']
      $alerts    = $alerts    ?? [];   // array of ['title','time','link?']
    @endphp

    {{-- Row 1: Profile + KPIs --}}
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-6">
      {{-- Profile Card --}}
      <div class="lg:col-span-4 rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md shadow-lg p-6">
        <div class="flex items-start gap-4">
          <div class="h-16 w-16 rounded-full overflow-hidden ring-2 ring-cyan-400/30 bg-slate-800 shrink-0">
            @if($user?->avatar_path)
              <img src="{{ asset('storage/'.$user->avatar_path) }}?v={{ optional($user->updated_at)->timestamp }}"
                   alt="Avatar" class="h-full w-full object-cover">
            @else
              <div class="h-full w-full grid place-items-center bg-gradient-to-br from-cyan-500 to-sky-500 text-white text-xl font-bold">
                {{ strtoupper(mb_substr($user?->name ?? 'U', 0, 1)) }}
              </div>
            @endif
          </div>

          <div class="min-w-0">
            <h2 class="text-base font-semibold text-cyan-300 truncate">{{ $user?->name ?? 'User' }}</h2>
            <p class="text-sm text-slate-400 truncate">{{ $user?->email ?? 'user@example.com' }}</p>
            <div class="mt-2 flex flex-wrap items-center gap-2">
              <span class="text-xs px-2.5 py-1 rounded-full bg-cyan-500/15 text-cyan-300 ring-1 ring-cyan-400/20">Role: {{ ucfirst($user?->role ?? 'user') }}</span>
              <a href="{{ route('profile.edit') }}"
                 class="text-xs px-2.5 py-1 rounded-full border border-cyan-400/20 bg-slate-800 hover:bg-slate-700 transition">Edit Profile</a>
            </div>
          </div>
        </div>

        <dl class="mt-6 grid grid-cols-2 gap-4 text-center">
          <div class="rounded-xl bg-slate-800/70 ring-1 ring-cyan-400/10 p-4">
            <dt class="text-xs text-slate-400">Upcoming</dt>
            <dd class="mt-1 text-xl font-semibold text-cyan-300">{{ $stats['upcoming'] }}</dd>
          </div>
          <div class="rounded-xl bg-slate-800/70 ring-1 ring-cyan-400/10 p-4">
            <dt class="text-xs text-slate-400">Tickets Owned</dt>
            <dd class="mt-1 text-xl font-semibold text-cyan-300">{{ $stats['tickets'] }}</dd>
          </div>
          <div class="rounded-xl bg-slate-800/70 ring-1 ring-cyan-400/10 p-4 col-span-2">
            <dt class="text-xs text-slate-400">Total Spent</dt>
            <dd class="mt-1 text-xl font-semibold text-cyan-300">৳ {{ number_format((float)$stats['spent'], 2) }}</dd>
          </div>
        </dl>
      </div>

      {{-- KPI Cards --}}
      <div class="lg:col-span-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="rounded-2xl p-5 bg-slate-900/80 border border-cyan-400/20 shadow-lg">
          <div class="flex items-center justify-between">
            <p class="text-xs uppercase tracking-wide text-slate-400">Upcoming Events</p>
            <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-cyan-500/15 ring-1 ring-cyan-400/20">
              <svg viewBox="0 0 24 24" class="h-3.5 w-3.5"><path d="M7 11h10M7 15h6M7 7h10" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/></svg>
            </span>
          </div>
          <p class="mt-3 text-3xl font-semibold text-cyan-300">{{ $stats['upcoming'] }}</p>
          <a href="{{ route('events.index') }}" class="mt-4 inline-block text-sm text-cyan-300/80 hover:text-cyan-300">View all →</a>
        </div>

        <div class="rounded-2xl p-5 bg-slate-900/80 border border-cyan-400/20 shadow-lg">
          <div class="flex items-center justify-between">
            <p class="text-xs uppercase tracking-wide text-slate-400">Notifications</p>
            <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-cyan-500/15 ring-1 ring-cyan-400/20">
              <svg viewBox="0 0 24 24" class="h-3.5 w-3.5"><path d="M12 22a2 2 0 0 0 2-2H10a2 2 0 0 0 2 2zM18 16V11a6 6 0 1 0-12 0v5l-2 2h16l-2-2z" fill="currentColor"/></svg>
            </span>
          </div>
          <p class="mt-3 text-3xl font-semibold text-cyan-300">{{ $stats['notifications'] }}</p>
          <a href="{{ route('notifications.index') }}" class="mt-4 inline-block text-sm text-cyan-300/80 hover:text-cyan-300">Open inbox →</a>
        </div>

        <div class="rounded-2xl p-5 bg-slate-900/80 border border-cyan-400/20 shadow-lg">
          <div class="flex items-center justify-between">
            <p class="text-xs uppercase tracking-wide text-slate-400">My Tickets</p>
            <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-cyan-500/15 ring-1 ring-cyan-400/20">
              <svg viewBox="0 0 24 24" class="h-3.5 w-3.5"><path d="M4 8h16v8H4zM8 8V6h8v2" stroke="currentColor" stroke-width="2" fill="none"/></svg>
            </span>
          </div>
          <p class="mt-3 text-3xl font-semibold text-cyan-300">{{ $stats['tickets'] }}</p>
          <a href="{{ route('tickets.my') }}" class="mt-4 inline-block text-sm text-cyan-300/80 hover:text-cyan-300">Manage tickets →</a>
        </div>
      </div>
    </section>

    {{-- Row 2: Upcoming list + Recent tickets + Notifications --}}
    <section class="grid grid-cols-1 xl:grid-cols-12 gap-6">
      {{-- Upcoming Events --}}
      <div class="xl:col-span-5 rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md shadow-lg">
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
          <h3 class="text-base font-semibold text-cyan-300">Upcoming Events</h3>
          <a class="text-sm text-cyan-300/80 hover:text-cyan-300" href="{{ route('events.index') }}">See all</a>
        </div>

        <ul class="divide-y divide-slate-800">
          @forelse($upcoming as $e)
            <li class="p-5 flex items-center justify-between gap-3">
              <div class="min-w-0">
                <p class="font-medium truncate">{{ $e['title'] }}</p>
                <p class="text-xs text-slate-400 truncate">{{ $e['venue'] ?? '—' }}</p>
              </div>
              <div class="text-right">
                <p class="text-sm text-slate-300">{{ \Carbon\Carbon::parse($e['date'])->format('d M Y') }}</p>
                <a href="{{ route('events.show', $e['id'] ?? null) }}" class="text-xs text-cyan-300/80 hover:text-cyan-300">Details</a>
              </div>
            </li>
          @empty
            <li class="p-8 text-center text-sm text-slate-400">No upcoming events. <a class="text-cyan-300" href="{{ route('events.index') }}">Browse events</a>.</li>
          @endforelse
        </ul>
      </div>

      {{-- Recent Tickets --}}
      <div class="xl:col-span-7 rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md shadow-lg">
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
          <h3 class="text-base font-semibold text-cyan-300">Recent Tickets</h3>
          <a class="text-sm text-cyan-300/80 hover:text-cyan-300" href="{{ route('tickets.my') }}">View all</a>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="text-left text-slate-400">
              <tr class="border-b border-slate-800">
                <th class="py-3 px-6">Ticket</th>
                <th class="py-3 px-6">Event</th>
                <th class="py-3 px-6">Date</th>
                <th class="py-3 px-6">Status</th>
                <th class="py-3 px-6 text-right">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
              @forelse($tickets as $t)
                <tr>
                  <td class="py-4 px-6 font-medium">{{ $t['code'] }}</td>
                  <td class="py-4 px-6">{{ $t['event'] }}</td>
                  <td class="py-4 px-6">{{ \Carbon\Carbon::parse($t['date'])->format('d M Y') }}</td>
                  <td class="py-4 px-6">
                    @php $status = strtolower($t['status']); @endphp
                    <span @class([
                      'px-2.5 py-1 rounded-full text-xs ring-1',
                      'bg-emerald-500/15 text-emerald-300 ring-emerald-400/20' => $status === 'paid',
                      'bg-yellow-500/15 text-yellow-300 ring-yellow-400/20' => $status === 'pending',
                      'bg-rose-500/15 text-rose-300 ring-rose-400/20' => $status === 'refunded',
                      'bg-slate-500/15 text-slate-300 ring-slate-400/20' => !in_array($status, ['paid','pending','refunded']),
                    ])>{{ ucfirst($t['status']) }}</span>
                  </td>
                  <td class="py-4 px-6 text-right">
                    <a href="{{ route('tickets.show', $t['id'] ?? null) }}"
                       class="text-cyan-300/80 hover:text-cyan-300">Open</a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="py-10 text-center text-sm text-slate-400">
                    You don’t have tickets yet. <a class="text-cyan-300" href="{{ route('events.index') }}">Grab your first ticket</a>.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </section>

    {{-- Row 3: Notifications + Quick actions --}}
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-6">
      {{-- Notifications --}}
      <div class="lg:col-span-8 rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md shadow-lg">
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
          <h3 class="text-base font-semibold text-cyan-300">Notifications</h3>
          <a class="text-sm text-cyan-300/80 hover:text-cyan-300" href="{{ route('notifications.index') }}">Open inbox</a>
        </div>

        <ul class="divide-y divide-slate-800">
          @forelse($alerts as $n)
            <li class="p-5 flex items-start gap-3">
              <span class="mt-0.5 inline-flex h-6 w-6 items-center justify-center rounded-md bg-cyan-500/15 ring-1 ring-cyan-400/20">
                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5"><path d="M12 22a2 2 0 0 0 2-2H10a2 2 0 0 0 2 2zM18 16V11a6 6 0 1 0-12 0v5l-2 2h16l-2-2z" fill="currentColor"/></svg>
              </span>
              <div class="min-w-0">
                <p class="font-medium">{{ $n['title'] }}</p>
                <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($n['time'])->diffForHumans() }}</p>
              </div>
              @if(!empty($n['link']))
                <a href="{{ $n['link'] }}" class="ml-auto text-sm text-cyan-300/80 hover:text-cyan-300">View</a>
              @endif
            </li>
          @empty
            <li class="p-8 text-center text-sm text-slate-400">No new notifications.</li>
          @endforelse
        </ul>
      </div>

      {{-- Quick actions --}}
      <div class="lg:col-span-4 rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md shadow-lg p-6">
        <h3 class="text-base font-semibold text-cyan-300 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 gap-3">
          <a href="{{ route('events.index') }}"
             class="p-3 rounded-lg bg-gradient-to-r from-cyan-500 to-sky-500 hover:from-cyan-400 hover:to-sky-400 text-sm font-medium shadow-md text-center">
            Browse Events
          </a>
          <a href="{{ route('tickets.my') }}"
             class="p-3 rounded-lg bg-slate-800 hover:bg-slate-700 text-sm font-medium border border-cyan-400/20 text-center">
            My Tickets
          </a>
          <a href="{{ route('profile.edit') }}"
             class="p-3 rounded-lg bg-slate-800 hover:bg-slate-700 text-sm font-medium border border-cyan-400/20 text-center">
            Profile Settings
          </a>
          <a href="{{ route('support.create') }}"
             class="p-3 rounded-lg bg-slate-800 hover:bg-slate-700 text-sm font-medium border border-cyan-400/20 text-center">
            Support
          </a>
        </div>

        <div class="mt-6 rounded-xl bg-slate-800/70 ring-1 ring-cyan-400/10 p-4">
          <p class="text-xs text-slate-400">Tip</p>
          <p class="mt-1 text-sm text-slate-300">
            Keep your profile info up to date to receive event alerts tailored to your interests.
          </p>
        </div>
      </div>
    </section>
  </main>
</body>
</html>
