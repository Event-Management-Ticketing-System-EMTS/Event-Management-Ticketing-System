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
        // placeholders; replace with real counts from controller if needed
        $kpis = [
          ['label'=>'Active Events','value'=>12],
          ['label'=>'Tickets Sold (Today)','value'=>148],
          ['label'=>'Revenue (Today)','value'=>'$2,340'],
          ['label'=>'Pending Approvals','value'=>3],
        ];
      @endphp
      @foreach ($kpis as $kpi)
        <div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-5 shadow-lg">
          <p class="text-sm text-slate-400">{{ $kpi['label'] }}</p>
          <p class="mt-2 text-2xl font-semibold text-cyan-300">{{ $kpi['value'] }}</p>
        </div>
      @endforeach
    </section>

    {{-- Quick Actions --}}
    <section class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-6 shadow-lg">
      <h2 class="text-lg font-semibold text-cyan-300 mb-4">Quick Actions</h2>
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <a href="{{ url('/events/create') }}"
           class="block p-4 rounded-xl bg-gradient-to-r from-cyan-500 to-sky-500 hover:from-cyan-400 hover:to-sky-400 text-center font-medium shadow-md">
          Create Event
        </a>
        <a href="{{ url('/events') }}"
           class="block p-4 rounded-xl bg-slate-800 hover:bg-slate-700 border border-cyan-400/20 text-center font-medium">
          Manage Events
        </a>
        <a href="{{ url('/bookings') }}"
           class="block p-4 rounded-xl bg-slate-800 hover:bg-slate-700 border border-cyan-400/20 text-center font-medium">
          View Bookings
        </a>
        <a href="{{ url('/reports') }}"
           class="block p-4 rounded-xl bg-slate-800 hover:bg-slate-700 border border-cyan-400/20 text-center font-medium">
          Reports
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
          @foreach ([
            ['Music Concert','12 Oct 2025','City Hall', '340/500','Published'],
            ['Tech Conf','20 Nov 2025','Expo Center','120/300','Draft'],
            ['Sports Meetup','05 Dec 2025','Arena A','78/200','Published'],
          ] as $row)
          <tr class="border-b border-white/5">
            <td class="py-3 pr-4">{{ $row[0] }}</td>
            <td class="py-3 pr-4">{{ $row[1] }}</td>
            <td class="py-3 pr-4">{{ $row[2] }}</td>
            <td class="py-3 pr-4">{{ $row[3] }}</td>
            <td class="py-3 pr-4">
              <span class="px-2 py-1 text-xs rounded-full
                {{ $row[4]==='Published' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-yellow-500/20 text-yellow-300' }}">
                {{ $row[4] }}
              </span>
            </td>
            <td class="py-3 flex gap-2">
              <a href="{{ url('/events/1/edit') }}" class="text-cyan-300 hover:text-cyan-200">Edit</a>
              <a href="{{ url('/events/1') }}" class="text-slate-300 hover:text-slate-100">View</a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </section>

  </main>
</body>
</html>
