@extends('layouts.app')

@section('title', 'View Bookings')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
  {{-- Header --}}
  <header class="bg-slate-900/80 backdrop-blur-md border-b border-white/10 px-6 py-4">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="p-2 rounded-lg bg-cyan-500/20">
          <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
          </svg>
        </div>
        <div>
          <h1 class="text-xl font-bold text-white">View Bookings</h1>
          <p class="text-sm text-slate-400">Manage all ticket bookings</p>
        </div>
      </div>

      {{-- Quick Actions --}}
      <div class="flex items-center gap-3">
        <button onclick="refreshBookings()"
                class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 border border-cyan-400/20 text-sm text-cyan-300 transition">
          🔄 Refresh
        </button>
        <a href="{{ route('bookings.export', request()->query()) }}"
           class="px-4 py-2 rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white text-sm transition">
          📊 Export CSV
        </a>
        <a href="{{ route('dashboard') }}"
           class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 border border-cyan-400/20 text-sm text-cyan-300 transition">
          ← Back to Dashboard
        </a>
      </div>
    </div>
  </header>

  <main class="max-w-7xl mx-auto p-6 space-y-6">
    {{-- Stats Cards --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-5 shadow-lg">
        <p class="text-sm text-slate-400">Total Bookings</p>
        <p class="mt-2 text-2xl font-semibold text-cyan-300">{{ number_format($stats['total_bookings']) }}</p>
      </div>

      <div class="rounded-2xl border border-emerald-400/20 bg-slate-900/80 backdrop-blur-md p-5 shadow-lg">
        <p class="text-sm text-slate-400">Confirmed</p>
        <p class="mt-2 text-2xl font-semibold text-emerald-300">{{ number_format($stats['confirmed_bookings']) }}</p>
      </div>

      <div class="rounded-2xl border border-yellow-400/20 bg-slate-900/80 backdrop-blur-md p-5 shadow-lg">
        <p class="text-sm text-slate-400">Pending</p>
        <p class="mt-2 text-2xl font-semibold text-yellow-300">{{ number_format($stats['pending_bookings']) }}</p>
      </div>

      <div class="rounded-2xl border border-red-400/20 bg-slate-900/80 backdrop-blur-md p-5 shadow-lg">
        <p class="text-sm text-slate-400">Cancelled</p>
        <p class="mt-2 text-2xl font-semibold text-red-300">{{ number_format($stats['cancelled_bookings']) }}</p>
      </div>
    </section>

    {{-- Revenue Card --}}
    <section class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-6 shadow-lg">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-lg font-semibold text-cyan-300">Total Revenue</h2>
          <p class="text-3xl font-bold text-white mt-2">${{ number_format($stats['total_revenue'], 2) }}</p>
        </div>
        <div class="p-3 rounded-lg bg-green-500/20">
          <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
          </svg>
        </div>
      </div>
    </section>

    {{-- Filters --}}
    <section class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-6 shadow-lg">
      <h2 class="text-lg font-semibold text-cyan-300 mb-4">Filter Bookings</h2>

      <form method="GET" action="{{ route('bookings.index') }}" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
        {{-- Status Filter --}}
        <div>
          <label class="block text-sm font-medium text-slate-300 mb-1">Status</label>
          <select name="status" class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100 focus:border-cyan-500">
            <option value="">All Statuses</option>
            @foreach($filterOptions['statuses'] as $value => $label)
              @if($value !== 'all')
                <option value="{{ $value }}" {{ (request('status') === $value) ? 'selected' : '' }}>
                  {{ $label }}
                </option>
              @endif
            @endforeach
          </select>
        </div>

        {{-- Event Filter --}}
        <div>
          <label class="block text-sm font-medium text-slate-300 mb-1">Event</label>
          <select name="event_id" class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100 focus:border-cyan-500">
            <option value="">All Events</option>
            @foreach($filterOptions['recent_events'] as $id => $title)
              <option value="{{ $id }}" {{ (request('event_id') == $id) ? 'selected' : '' }}>
                {{ Str::limit($title, 30) }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Date From --}}
        <div>
          <label class="block text-sm font-medium text-slate-300 mb-1">From Date</label>
          <input type="date" name="date_from" value="{{ request('date_from') }}"
                 class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100 focus:border-cyan-500">
        </div>

        {{-- Date To --}}
        <div>
          <label class="block text-sm font-medium text-slate-300 mb-1">To Date</label>
          <input type="date" name="date_to" value="{{ request('date_to') }}"
                 class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100 focus:border-cyan-500">
        </div>

        {{-- Filter Actions --}}
        <div class="flex flex-col gap-2">
          <button type="submit"
                  class="px-4 py-2 rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white text-sm transition">
            🔍 Filter
          </button>
          <a href="{{ route('bookings.index') }}"
             class="px-4 py-2 rounded-lg bg-slate-700 hover:bg-slate-600 text-center text-slate-300 text-sm transition">
            ✨ Clear
          </a>
        </div>
      </form>
    </section>

    {{-- Bookings Table --}}
    <section class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md shadow-lg overflow-hidden">
      <div class="p-6 border-b border-white/10">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-semibold text-cyan-300">
            All Bookings
            <span class="text-sm text-slate-400">({{ $bookings->total() }} total)</span>
          </h2>

          @if(count($filters) > 0)
            <div class="text-sm text-slate-400">
              <span class="px-2 py-1 rounded bg-cyan-500/20 text-cyan-300">
                {{ count($filters) }} filter(s) applied
              </span>
            </div>
          @endif
        </div>
      </div>

      @if($bookings->count() > 0)
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead class="bg-slate-800/50">
              <tr class="text-left border-b border-white/5">
                <th class="px-6 py-4 text-xs font-medium text-slate-400 uppercase tracking-wider">Booking</th>
                <th class="px-6 py-4 text-xs font-medium text-slate-400 uppercase tracking-wider">Event</th>
                <th class="px-6 py-4 text-xs font-medium text-slate-400 uppercase tracking-wider">Customer</th>
                <th class="px-6 py-4 text-xs font-medium text-slate-400 uppercase tracking-wider">Details</th>
                <th class="px-6 py-4 text-xs font-medium text-slate-400 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-xs font-medium text-slate-400 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
              @foreach($bookings as $booking)
                <tr class="hover:bg-slate-800/30 transition-colors">
                  {{-- Booking Info --}}
                  <td class="px-6 py-4">
                    <div>
                      <div class="text-sm font-medium text-white">#{{ $booking->id }}</div>
                      <div class="text-xs text-slate-400">{{ $booking->created_at->format('M d, Y H:i') }}</div>
                    </div>
                  </td>

                  {{-- Event Info --}}
                  <td class="px-6 py-4">
                    <div>
                      <div class="text-sm font-medium text-white">{{ Str::limit($booking->event->title ?? 'N/A', 25) }}</div>
                      <div class="text-xs text-slate-400">
                        📅 {{ optional($booking->event)->event_date ? Carbon\Carbon::parse($booking->event->event_date)->format('M d, Y') : 'N/A' }}
                      </div>
                      <div class="text-xs text-slate-400">
                        📍 {{ Str::limit($booking->event->venue ?? 'N/A', 20) }}
                      </div>
                    </div>
                  </td>

                  {{-- Customer Info --}}
                  <td class="px-6 py-4">
                    <div>
                      <div class="text-sm font-medium text-white">{{ $booking->user->name ?? 'N/A' }}</div>
                      <div class="text-xs text-slate-400">{{ $booking->user->email ?? 'N/A' }}</div>
                    </div>
                  </td>

                  {{-- Booking Details --}}
                  <td class="px-6 py-4">
                    <div>
                      <div class="text-sm text-white">{{ $booking->quantity }} ticket(s)</div>
                      <div class="text-sm font-medium text-green-400">${{ number_format($booking->total_price, 2) }}</div>
                    </div>
                  </td>

                  {{-- Status --}}
                  <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded-full font-medium
                      {{ $booking->status === 'confirmed' ? 'bg-emerald-500/20 text-emerald-300' :
                         ($booking->status === 'pending' ? 'bg-yellow-500/20 text-yellow-300' : 'bg-red-500/20 text-red-300') }}">
                      {{ ucfirst($booking->status) }}
                    </span>
                  </td>

                  {{-- Actions --}}
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                      <a href="{{ route('bookings.show', $booking->id) }}"
                         class="text-cyan-400 hover:text-cyan-300 text-sm transition">
                        👁️ View
                      </a>
                      @if($booking->event)
                        <a href="{{ route('events.show', $booking->event->id) }}"
                           class="text-blue-400 hover:text-blue-300 text-sm transition">
                          🎪 Event
                        </a>
                      @endif
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-white/10">
          {{ $bookings->withQueryString()->links() }}
        </div>
      @else
        <div class="p-12 text-center">
          <div class="p-4 rounded-lg bg-slate-800/50 inline-block mb-4">
            <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
          </div>
          <h3 class="text-lg font-medium text-white mb-2">No Bookings Found</h3>
          <p class="text-slate-400 mb-4">
            @if(count($filters) > 0)
              No bookings match your current filters. Try adjusting your search criteria.
            @else
              No ticket bookings have been made yet.
            @endif
          </p>
          @if(count($filters) > 0)
            <a href="{{ route('bookings.index') }}"
               class="inline-flex items-center px-4 py-2 rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white text-sm transition">
              Clear Filters
            </a>
          @endif
        </div>
      @endif
    </section>
  </main>
</div>

<script>
function refreshBookings() {
  window.location.reload();
}

// Auto-refresh every 30 seconds
setInterval(refreshBookings, 30000);
</script>
@endsection
