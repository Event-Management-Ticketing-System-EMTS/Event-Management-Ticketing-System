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
          <path d="M12 3l9 4.5v9L12 21 3 16.5v-9L12 3zM5 8l7 3 7-3-7-3-7 3zm7 5l7-3v5l-7 3-7-3v-5l7 3z"/>
        </svg>
      </div>
      <h1 class="text-xl font-bold text-cyan-300">Event Statistics</h1>
    </div>

    <div class="flex items-center gap-4">
      <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 border border-cyan-400/20 text-sm transition">
        Back to Dashboard
      </a>
    </div>
  </header>

  <main class="max-w-7xl mx-auto p-6 space-y-6">
    {{-- Key Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-5 shadow-lg">
        <p class="text-sm text-slate-400">Total Events</p>
        <p class="mt-2 text-2xl font-semibold text-cyan-300">{{ $totalEvents ?? (isset($events) ? $events->count() : 0) }}</p>
      </div>

      <div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-5 shadow-lg">
        <p class="text-sm text-slate-400">Tickets Sold</p>
        <p class="mt-2 text-2xl font-semibold text-cyan-300">{{ $ticketsData['sold'] ?? 0 }}</p>
        @php
          $pct = $ticketsData['percentageSold'] ?? 0;
        @endphp
        <div class="w-full bg-slate-700 rounded-full h-2 mt-2">
          <div class="bg-gradient-to-r from-cyan-500 to-blue-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
        </div>
        <p class="text-xs text-slate-400 mt-1">{{ $pct }}% of capacity</p>
      </div>

      <div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-5 shadow-lg">
        <p class="text-sm text-slate-400">Total Revenue</p>
        <p class="mt-2 text-2xl font-semibold text-cyan-300">${{ number_format($totalRevenue ?? 0, 2) }}</p>
      </div>

      <div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-5 shadow-lg">
        <p class="text-sm text-slate-400">Active Events</p>
        <p class="mt-2 text-2xl font-semibold text-cyan-300">{{ ($eventsByStatus['published'] ?? 0) }}</p>
      </div>
    </div>

    {{-- Optional: Approval status card (only if provided) --}}
    @if(!empty($eventsByApproval))
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-5 shadow-lg">
          <p class="text-sm text-slate-400">Pending Approval</p>
          <p class="mt-2 text-2xl font-semibold text-amber-300">{{ $eventsByApproval['pending'] ?? 0 }}</p>
        </div>
        <div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-5 shadow-lg">
          <p class="text-sm text-slate-400">Approved</p>
          <p class="mt-2 text-2xl font-semibold text-emerald-300">{{ $eventsByApproval['approved'] ?? 0 }}</p>
        </div>
        <div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-5 shadow-lg">
          <p class="text-sm text-slate-400">Rejected</p>
          <p class="mt-2 text-2xl font-semibold text-rose-300">{{ $eventsByApproval['rejected'] ?? 0 }}</p>
        </div>
      </div>
    @endif

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      {{-- Event Status Chart --}}
      <div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-6 shadow-lg">
        <h2 class="text-lg font-semibold text-cyan-300 mb-4">Event Status Distribution</h2>
        <div class="flex justify-center">
          <div class="w-64 h-64">
            <canvas id="eventStatusChart"></canvas>
          </div>
        </div>
        <div class="grid grid-cols-3 gap-2 mt-4 text-center text-sm">
          <div>
            <div class="inline-block w-3 h-3 rounded-full bg-cyan-400 mr-1"></div>
            <span class="text-slate-300">Published</span>
          </div>
          <div>
            <div class="inline-block w-3 h-3 rounded-full bg-amber-400 mr-1"></div>
            <span class="text-slate-300">Draft</span>
          </div>
          <div>
            <div class="inline-block w-3 h-3 rounded-full bg-red-400 mr-1"></div>
            <span class="text-slate-300">Cancelled</span>
          </div>
        </div>
      </div>

      {{-- Tickets Chart --}}
      <div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-6 shadow-lg">
        <h2 class="text-lg font-semibold text-cyan-300 mb-4">Ticket Sales</h2>
        <div class="flex justify-center">
          <div class="w-64 h-64">
            <canvas id="ticketSalesChart"></canvas>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-2 mt-4 text-center text-sm">
          <div>
            <div class="inline-block w-3 h-3 rounded-full bg-cyan-400 mr-1"></div>
            <span class="text-slate-300">Sold ({{ $ticketsData['sold'] ?? 0 }})</span>
          </div>
          <div>
            <div class="inline-block w-3 h-3 rounded-full bg-slate-600 mr-1"></div>
            <span class="text-slate-300">Available ({{ $ticketsData['available'] ?? 0 }})</span>
          </div>
        </div>
      </div>

      {{-- Monthly Events --}}
      <div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-6 shadow-lg">
        <h2 class="text-lg font-semibold text-cyan-300 mb-4">Events by Month</h2>
        <div class="flex justify-center">
          <div class="w-full h-64">
            <canvas id="monthlyEventsChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    {{-- Tables Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      {{-- Upcoming Events --}}
      <div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-6 shadow-lg overflow-x-auto">
        <h2 class="text-lg font-semibold text-cyan-300 mb-4">Upcoming Events</h2>
        @if(($upcomingEvents ?? collect())->isEmpty())
          <p class="text-slate-400 text-center py-8">No upcoming events.</p>
        @else
          <table class="w-full">
            <thead>
              <tr class="border-b border-slate-800">
                <th class="px-4 py-2 text-left text-sm font-semibold text-cyan-300">Event</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-cyan-300">Date</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-cyan-300">Tickets</th>
              </tr>
            </thead>
            <tbody>
              @foreach($upcomingEvents as $event)
              <tr class="border-b border-slate-800">
                <td class="px-4 py-3">
                  <a href="{{ route('events.show', $event->id) }}" class="font-medium text-cyan-300 hover:underline">{{ $event->title }}</a>
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                  {{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}
                </td>
                <td class="px-4 py-3">
                  {{-- If per-event sold/total not available, show "-" --}}
                  @php
                    $rowSold = $event->tickets_sold ?? null;
                    $rowCap  = $event->total_tickets ?? null;
                  @endphp
                  {{ isset($rowSold, $rowCap) ? "{$rowSold}/{$rowCap}" : '-' }}
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        @endif
      </div>

      {{-- Top Performing Events --}}
      <div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-6 shadow-lg overflow-x-auto">
        <h2 class="text-lg font-semibold text-cyan-300 mb-4">Top Performing Events</h2>
        @if(($topEvents ?? collect())->isEmpty())
          <p class="text-slate-400 text-center py-8">No events with ticket sales yet.</p>
        @else
          <table class="w-full">
            <thead>
              <tr class="border-b border-slate-800">
                <th class="px-4 py-2 text-left text-sm font-semibold text-cyan-300">Event</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-cyan-300">Sales</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-cyan-300">Revenue</th>
              </tr>
            </thead>
            <tbody>
              @foreach($topEvents as $event)
              @php
                // Controller may provide ->sold; otherwise fallback to tickets_sold
                $sold = $event->sold ?? $event->tickets_sold ?? 0;
                $price = $event->price ?? null;
                $rowRevenue = isset($price) ? ($sold * $price) : null;
              @endphp
              <tr class="border-b border-slate-800">
                <td class="px-4 py-3">
                  <a href="{{ route('events.show', $event->id) }}" class="font-medium text-cyan-300 hover:underline">{{ $event->title }}</a>
                </td>
                <td class="px-4 py-3">{{ $sold }}</td>
                <td class="px-4 py-3">
                  {{ isset($rowRevenue) ? '$'.number_format($rowRevenue, 2) : '—' }}
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        @endif
      </div>
    </div>
  </main>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Chart.js defaults for dark theme
    Chart.defaults.color = '#94a3b8';
    Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.1)';

    // Event Status Chart
    new Chart(document.getElementById('eventStatusChart').getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: ['Published', 'Draft', 'Cancelled'],
        datasets: [{
          data: [
            {{ $eventsByStatus['published'] ?? 0 }},
            {{ $eventsByStatus['draft'] ?? 0 }},
            {{ $eventsByStatus['cancelled'] ?? 0 }}
          ],
          backgroundColor: ['#22d3ee', '#fbbf24', '#f87171'],
          borderWidth: 0,
          hoverOffset: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(15, 23, 42, 0.9)',
            titleColor: '#e2e8f0',
            bodyColor: '#e2e8f0',
            padding: 10,
            boxPadding: 5,
            borderColor: 'rgba(6, 182, 212, 0.3)',
            borderWidth: 1,
            cornerRadius: 8,
            usePointStyle: true,
          }
        },
        cutout: '70%'
      }
    });

    // Ticket Sales Chart
    new Chart(document.getElementById('ticketSalesChart').getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: ['Sold', 'Available'],
        datasets: [{
          data: [
            {{ $ticketsData['sold'] ?? 0 }},
            {{ $ticketsData['available'] ?? 0 }}
          ],
          backgroundColor: ['#22d3ee', '#475569'],
          borderWidth: 0,
          hoverOffset: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(15, 23, 42, 0.9)',
            titleColor: '#e2e8f0',
            bodyColor: '#e2e8f0',
            padding: 10,
            boxPadding: 5,
            borderColor: 'rgba(6, 182, 212, 0.3)',
            borderWidth: 1,
            cornerRadius: 8,
            usePointStyle: true,
          }
        },
        cutout: '70%'
      }
    });

    // Monthly Events Chart
    new Chart(document.getElementById('monthlyEventsChart').getContext('2d'), {
      type: 'bar',
      data: {
        labels: {!! json_encode($months ?? []) !!},
        datasets: [{
          label: 'Events Created',
          data: {!! json_encode($counts ?? []) !!},
          backgroundColor: 'rgba(6, 182, 212, 0.5)',
          borderColor: '#06b6d4',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
          y: {
            beginAtZero: true,
            ticks: { precision: 0 },
            grid: { color: 'rgba(255, 255, 255, 0.05)' }
          },
          x: { grid: { color: 'rgba(255, 255, 255, 0.05)' } }
        },
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(15, 23, 42, 0.9)',
            titleColor: '#e2e8f0',
            bodyColor: '#e2e8f0',
            padding: 10,
            borderColor: 'rgba(6, 182, 212, 0.3)',
            borderWidth: 1,
            cornerRadius: 8
          }
        }
      }
    });
  });
</script>
@endsection
