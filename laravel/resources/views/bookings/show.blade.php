@extends('layouts.app')

@section('title', 'Booking Details')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-950 via-s              <div class="flex gap-2">
                <a href="{{ route('u              <div class="flex gap-2">
                <a href="{{ route('events.show', $booking->event->id) }}"
                   class="px-3 py-1 rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-400 hover:to-blue-500 text-white text-sm transition-all duration-200 shadow-lg shadow-blue-900/30">
                  🎪 View Event
                </a>
                <a href="{{ route('events.edit', $booking->event->id) }}"
                   class="px-3 py-1 rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-400 hover:to-orange-500 text-white text-sm transition-all duration-200 shadow-lg shadow-orange-900/30">
                  ✏️ Edit Event
                </a>
              </div>, $booking->user->id) }}"
                   class="px-3 py-1 rounded-lg bg-gradient-to-r from-cyan-500 to-sky-500 hover:from-cyan-400 hover:to-sky-400 text-white text-sm transition-all duration-200 shadow-lg shadow-cyan-900/30">
                  👤 View Profile
                </a>
              </div>00 to-slate-950 text-slate-100 antialiased">
  {{-- Subtle grid + glow overlay to match login page --}}
  <div class="fixed inset-0 -z-10">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(6,182,212,0.15),transparent_60%)]"></div>
    <div class="absolute inset-0 opacity-[0.06] [mask-image:linear-gradient(to_bottom,black,transparent)]">
      <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
            <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="0.5"/>
          </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#grid)"/>
      </svg>
    </div>
  </div>

  {{-- Header --}}
  <header class="bg-slate-900/80 backdrop-blur-md border-b border-white/10 px-6 py-4">
    <div class="max-w-4xl mx-auto flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="p-2 rounded-2xl bg-cyan-500/20 ring-1 ring-cyan-400/40">
          <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
          </svg>
        </div>
        <div>
          <h1 class="text-xl font-bold text-cyan-300 tracking-tight">Booking #{{ $booking->id }}</h1>
          <p class="text-sm text-slate-400">Booking details and information</p>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <a href="{{ route('bookings.index') }}"
           class="px-4 py-2 rounded-lg bg-slate-800/70 hover:bg-slate-700 border border-cyan-400/20 text-sm text-cyan-300 transition-all duration-200 shadow-lg">
          ← Back to Bookings
        </a>
      </div>
    </div>
  </header>  <main class="max-w-4xl mx-auto p-6 space-y-6">
    {{-- Booking Status Card --}}
    <section class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-6 shadow-xl shadow-cyan-900/20">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-semibold text-cyan-300 tracking-tight">Booking Status</h2>
        <span class="px-3 py-1 text-sm rounded-full font-medium
          {{ $booking->status === 'confirmed' ? 'bg-emerald-500/20 text-emerald-300 ring-1 ring-emerald-400/40' :
             ($booking->status === 'pending' ? 'bg-yellow-500/20 text-yellow-300 ring-1 ring-yellow-400/40' : 'bg-red-500/20 text-red-300 ring-1 ring-red-400/40') }}">
          {{ ucfirst($booking->status) }}
        </span>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Booking Information --}}
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-400 mb-1">Booking ID</label>
            <p class="text-lg font-semibold text-white">#{{ $booking->id }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-400 mb-1">Purchase Date</label>
            <p class="text-white">{{ $booking->created_at->format('F d, Y \a\t H:i A') }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-400 mb-1">Quantity</label>
            <p class="text-white">{{ $booking->quantity }} ticket(s)</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-400 mb-1">Total Price</label>
            <p class="text-xl font-bold text-green-400">${{ number_format($booking->total_price, 2) }}</p>
          </div>
        </div>

        {{-- Timeline --}}
        <div class="space-y-4">
          <h3 class="text-sm font-medium text-slate-400">Booking Timeline</h3>
          <div class="space-y-3">
            <div class="flex items-center gap-3">
              <div class="w-3 h-3 rounded-full bg-blue-500"></div>
              <div>
                <p class="text-sm text-white">Booking Created</p>
                <p class="text-xs text-slate-400">{{ $booking->created_at->format('M d, Y H:i A') }}</p>
              </div>
            </div>

            @if($booking->status === 'confirmed')
              <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                <div>
                  <p class="text-sm text-white">Payment Confirmed</p>
                  <p class="text-xs text-slate-400">{{ $booking->updated_at->format('M d, Y H:i A') }}</p>
                </div>
              </div>
            @elseif($booking->status === 'cancelled')
              <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                <div>
                  <p class="text-sm text-white">Booking Cancelled</p>
                  <p class="text-xs text-slate-400">{{ $booking->updated_at->format('M d, Y H:i A') }}</p>
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </section>

    {{-- Customer Information --}}
    <section class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-6 shadow-xl shadow-cyan-900/20">
      <h2 class="text-lg font-semibold text-cyan-300 mb-6 tracking-tight">Customer Information</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-400 mb-1">Customer Name</label>
            <p class="text-white">{{ $booking->user->name ?? 'N/A' }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-400 mb-1">Email Address</label>
            <p class="text-white">{{ $booking->user->email ?? 'N/A' }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-400 mb-1">Customer Role</label>
            <span class="px-2 py-1 text-xs rounded-full bg-blue-500/20 text-blue-300">
              {{ ucfirst($booking->user->role ?? 'user') }}
            </span>
          </div>
        </div>

        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-400 mb-1">Customer Since</label>
            <p class="text-white">{{ optional($booking->user)->created_at ? $booking->user->created_at->format('F Y') : 'N/A' }}</p>
          </div>

          @if($booking->user)
            <div>
              <label class="block text-sm font-medium text-slate-400 mb-1">Quick Actions</label>
              <div class="flex gap-2">
                <a href="{{ route('users.show', $booking->user->id) }}"
                   class="px-3 py-1 rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white text-sm transition">
                  👤 View Profile
                </a>
              </div>
            </div>
          @endif
        </div>
      </div>
    </section>

    {{-- Event Information --}}
    <section class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-6 shadow-xl shadow-cyan-900/20">
      <h2 class="text-lg font-semibold text-cyan-300 mb-6 tracking-tight">Event Information</h2>

      @if($booking->event)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-slate-400 mb-1">Event Title</label>
              <p class="text-lg font-semibold text-white">{{ $booking->event->title }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-400 mb-1">Event Date</label>
              <p class="text-white">{{ Carbon\Carbon::parse($booking->event->event_date)->format('F d, Y') }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-400 mb-1">Time</label>
              <p class="text-white">
                {{ $booking->event->start_time ? Carbon\Carbon::parse($booking->event->start_time)->format('H:i A') : 'TBA' }}
                @if($booking->event->end_time)
                  - {{ Carbon\Carbon::parse($booking->event->end_time)->format('H:i A') }}
                @endif
              </p>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-400 mb-1">Venue</label>
              <p class="text-white">{{ $booking->event->venue }}</p>
              @if($booking->event->address)
                <p class="text-sm text-slate-400">{{ $booking->event->address }}</p>
              @endif
            </div>
          </div>

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-slate-400 mb-1">Event Status</label>
              <span class="px-2 py-1 text-xs rounded-full
                {{ $booking->event->status === 'published' ? 'bg-emerald-500/20 text-emerald-300' :
                   ($booking->event->status === 'draft' ? 'bg-yellow-500/20 text-yellow-300' : 'bg-red-500/20 text-red-300') }}">
                {{ ucfirst($booking->event->status) }}
              </span>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-400 mb-1">Ticket Price</label>
              <p class="text-white">${{ number_format($booking->event->price, 2) }} per ticket</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-400 mb-1">Capacity</label>
              <p class="text-white">{{ number_format($booking->event->capacity ?? 0) }} total capacity</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-400 mb-1">Quick Actions</label>
              <div class="flex gap-2">
                <a href="{{ route('events.show', $booking->event->id) }}"
                   class="px-3 py-1 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm transition">
                  🎪 View Event
                </a>
                <a href="{{ route('events.edit', $booking->event->id) }}"
                   class="px-3 py-1 rounded-lg bg-orange-600 hover:bg-orange-700 text-white text-sm transition">
                  ✏️ Edit Event
                </a>
              </div>
            </div>
          </div>
        </div>

        {{-- Event Description --}}
        @if($booking->event->description)
          <div class="mt-6 pt-6 border-t border-white/10">
            <label class="block text-sm font-medium text-slate-400 mb-2">Event Description</label>
            <div class="prose prose-invert max-w-none">
              <p class="text-slate-300">{{ $booking->event->description }}</p>
            </div>
          </div>
        @endif
      @else
        <div class="text-center py-8">
          <div class="p-4 rounded-lg bg-red-500/20 inline-block mb-4">
            <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
          </div>
          <h3 class="text-lg font-medium text-red-400 mb-2">Event Not Found</h3>
          <p class="text-slate-400">The event associated with this booking may have been deleted.</p>
        </div>
      @endif
    </section>

    {{-- Additional Notes (if needed in future) --}}
    <section class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-6 shadow-xl shadow-cyan-900/20">
      <h2 class="text-lg font-semibold text-cyan-300 mb-4 tracking-tight">Additional Information</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-slate-400 mb-1">Created At</label>
          <p class="text-white">{{ $booking->created_at->format('F d, Y \a\t H:i:s A') }}</p>
          <p class="text-xs text-slate-400">{{ $booking->created_at->diffForHumans() }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-400 mb-1">Last Updated</label>
          <p class="text-white">{{ $booking->updated_at->format('F d, Y \a\t H:i:s A') }}</p>
          <p class="text-xs text-slate-400">{{ $booking->updated_at->diffForHumans() }}</p>
        </div>
      </div>
    </section>
  </main>
</div>
@endsection
