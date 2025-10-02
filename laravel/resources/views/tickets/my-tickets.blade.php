@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-slate-100">
  {{-- Background grid/glow --}}
  <div class="fixed inset-0 -z-10">
    <div class="absolute inset-0 bg-[radial-gradient(75%_60%_at_90%_0%,rgba(34,211,238,0.12),transparent_60%)]"></div>
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

  <div class="max-w-7xl mx-auto px-6 py-10">
    {{-- Header / Actions --}}
    <div class="flex items-center justify-between gap-4 mb-8">
      <div>
        <p class="text-xs text-slate-400">Tickets</p>
        <h1 class="text-3xl font-bold tracking-tight text-cyan-300">My Tickets</h1>
      </div>
      <div class="flex items-center gap-3">
        <a href="{{ route('events.index') }}"
           class="hidden md:inline-flex px-4 py-2 rounded-lg border border-cyan-400/20 bg-slate-800 hover:bg-slate-700 transition">
          Browse Events
        </a>
        <a href="{{ route('user.dashboard') }}"
           class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 border border-cyan-400/20 transition">
          Back to Dashboard
        </a>
      </div>
    </div>

    @if($tickets->count() > 0)
      <div class="space-y-5">
        @foreach($tickets as $ticket)
          <article class="rounded-2xl bg-slate-900/80 backdrop-blur-md border border-cyan-400/20 shadow-lg p-6">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-5">
              {{-- Left: Event + meta --}}
              <div class="min-w-0 md:flex-1">
                <a href="{{ route('events.show', $ticket->event->id) }}"
                   class="group inline-flex items-center gap-2">
                  <h3 class="text-xl font-semibold text-cyan-300 group-hover:text-cyan-200 transition truncate">
                    {{ $ticket->event->title }}
                  </h3>
                  <svg class="h-4 w-4 text-cyan-300/70 group-hover:text-cyan-200 transition"
                       viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.293 3.293a1 1 0 011.414 0l4 4a1
                    1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586
                    9H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0
                    010-1.414z" clip-rule="evenodd"/>
                  </svg>
                </a>

                <dl class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                  <div class="rounded-xl bg-slate-800/70 ring-1 ring-cyan-400/10 p-3">
                    <dt class="text-slate-400">Event Date</dt>
                    <dd class="mt-0.5 font-medium text-slate-200">
                      {{ \Carbon\Carbon::parse($ticket->event->event_date)->format('M d, Y') }}
                    </dd>
                  </div>
                  <div class="rounded-xl bg-slate-800/70 ring-1 ring-cyan-400/10 p-3">
                    <dt class="text-slate-400">Venue</dt>
                    <dd class="mt-0.5 font-medium text-slate-200">
                      {{ $ticket->event->venue }}
                    </dd>
                  </div>
                  <div class="rounded-xl bg-slate-800/70 ring-1 ring-cyan-400/10 p-3">
                    <dt class="text-slate-400">Unit Price</dt>
                    <dd class="mt-0.5 font-medium text-cyan-300">
                      ${{ number_format($ticket->event->price, 2) }}
                    </dd>
                  </div>
                </dl>

                <div class="mt-4 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm">
                  <div>
                    <span class="text-slate-400">Quantity:</span>
                    <span class="font-semibold">{{ $ticket->quantity }}</span>
                  </div>
                  <div>
                    <span class="text-slate-400">Total Price:</span>
                    <span class="font-semibold text-cyan-300">
                      ${{ number_format($ticket->total_price, 2) }}
                    </span>
                  </div>
                  <div>
                    <span class="text-slate-400">Purchased:</span>
                    <span class="font-semibold">
                      {{ $ticket->purchase_date->format('M d, Y') }}
                    </span>
                  </div>
                </div>
              </div>

              {{-- Right: Status + actions --}}
              <div class="flex flex-col items-end gap-3 shrink-0">
                {{-- Booking status badge --}}
                <span @class([
                  'px-3 py-1.5 rounded-full text-xs font-medium ring-1',
                  'bg-emerald-500/10 text-emerald-300 ring-emerald-500/20' => $ticket->status === 'confirmed',
                  'bg-amber-500/10  text-amber-300  ring-amber-500/20'   => $ticket->status === 'pending',
                  'bg-rose-500/10   text-rose-300   ring-rose-500/20'    => $ticket->status === 'cancelled',
                  'bg-slate-600/20  text-slate-300  ring-slate-500/20'   => !in_array($ticket->status, ['confirmed','pending','cancelled']),
                ])>
                  {{ ucfirst($ticket->status) }}
                </span>

                {{-- Payment status badge --}}
                <span @class([
                  'px-3 py-1.5 rounded-full text-xs font-medium ring-1',
                  'bg-emerald-500/10 text-emerald-300 ring-emerald-500/20' => $ticket->payment_status === 'paid',
                  'bg-amber-500/10  text-amber-300  ring-amber-500/20'    => $ticket->payment_status === 'pending',
                  'bg-rose-500/10   text-rose-300   ring-rose-500/20'     => $ticket->payment_status === 'failed',
                  'bg-slate-600/20  text-slate-300  ring-slate-500/20'    => !in_array($ticket->payment_status, ['paid','pending','failed']),
                ])>
                  Payment: {{ ucfirst($ticket->payment_status) }}
                </span>

                {{-- Cancel button (uses named route via data-url) --}}
                @if($ticket->status !== 'cancelled' && $ticket->event->event_date > now())
                  <button
                    data-url="{{ route('tickets.cancel', $ticket->id) }}"
                    onclick="cancelTicket(this)"
                    class="px-4 py-2 rounded-lg text-sm font-medium bg-rose-600 hover:bg-rose-500
                           ring-1 ring-rose-300/30 shadow-sm transition">
                    Cancel Ticket
                  </button>
                @endif
              </div>
            </div>
          </article>
        @endforeach
      </div>
    @else
      {{-- Empty state --}}
      <div class="max-w-3xl mx-auto text-center py-20">
        <div class="mx-auto mb-5 inline-flex h-16 w-16 items-center justify-center rounded-2xl
                    bg-cyan-500/15 ring-1 ring-cyan-400/30">
          <span class="text-3xl">🎫</span>
        </div>
        <h2 class="text-2xl font-semibold text-slate-200">No Tickets Yet</h2>
        <p class="mt-2 text-slate-400">
          You haven’t purchased any tickets yet. Browse events to get started.
        </p>
        <a href="{{ route('events.index') }}"
           class="mt-6 inline-flex px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-sky-500
                  hover:from-cyan-400 hover:to-sky-400 text-white font-medium shadow-md transition">
          Browse Events
        </a>
      </div>
    @endif
  </div>
</div>
@endsection

@push('scripts')
<script>
function cancelTicket(btn) {
  const url = btn.dataset.url;
  if (!url) return alert('Missing cancel URL.');

  if (!confirm('Are you sure you want to cancel this ticket?')) return;

  const tokenTag = document.querySelector('meta[name="csrf-token"]');
  const token = tokenTag ? tokenTag.getAttribute('content') : '';

  fetch(url, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': token,
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    },
    credentials: 'same-origin'
  })
  .then(async (r) => {
    // If backend returns HTML (419/500), avoid JSON parse crash
    if (!r.ok) {
      const text = await r.text();
      throw new Error(`HTTP ${r.status} – ${text.slice(0, 200)}`);
    }
    return r.json();
  })
  .then((data) => {
    alert(data.message || 'Ticket cancelled.');
    if (data.success) location.reload();
  })
  .catch((err) => {
    console.error(err);
    alert('Could not cancel ticket. Please refresh and try again.');
  });
}
</script>
@endpush
