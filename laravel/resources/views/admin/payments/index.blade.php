<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Payment Management - Admin Dashboard</title>
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
          <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
        </svg>
      </div>
      <h1 class="text-xl font-bold text-cyan-300">Payment Management</h1>
      <div class="text-sm text-slate-400 hidden sm:inline ml-4">
        Total Revenue: <span class="font-bold text-cyan-300">${{ number_format($stats['total_revenue'], 2) }}</span>
      </div>
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

    {{-- Payment Statistics --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
      <div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-5 shadow-lg">
        <p class="text-sm text-slate-400">Total Tickets</p>
        <p class="mt-2 text-2xl font-semibold text-cyan-300">{{ $stats['total_tickets'] }}</p>
      </div>

      <div class="rounded-2xl border border-emerald-400/20 bg-slate-900/80 backdrop-blur-md p-5 shadow-lg">
        <p class="text-sm text-slate-400">Paid</p>
        <p class="mt-2 text-2xl font-semibold text-emerald-300">{{ $stats['paid_tickets'] }}</p>
      </div>

      <div class="rounded-2xl border border-yellow-400/20 bg-slate-900/80 backdrop-blur-md p-5 shadow-lg">
        <p class="text-sm text-slate-400">Pending</p>
        <p class="mt-2 text-2xl font-semibold text-yellow-300">{{ $stats['pending_payments'] }}</p>
      </div>

      <div class="rounded-2xl border border-red-400/20 bg-slate-900/80 backdrop-blur-md p-5 shadow-lg">
        <p class="text-sm text-slate-400">Failed</p>
        <p class="mt-2 text-2xl font-semibold text-red-300">{{ $stats['failed_payments'] }}</p>
      </div>

      <div class="rounded-2xl border border-slate-400/20 bg-slate-900/80 backdrop-blur-md p-5 shadow-lg">
        <p class="text-sm text-slate-400">Refunded</p>
        <p class="mt-2 text-2xl font-semibold text-slate-300">{{ $stats['refunded_tickets'] }}</p>
      </div>
    </section>    {{-- Pending Payments Section --}}
    @if($pendingPayments->count() > 0)
    <section class="rounded-2xl border border-yellow-400/20 bg-slate-900/80 backdrop-blur-md shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-yellow-500 to-orange-500 px-6 py-4">
            <h2 class="text-xl font-semibold text-white flex items-center gap-3">
              <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
              </svg>
              Pending Payments ({{ $pendingPayments->count() }})
            </h2>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-slate-400">
                        <tr class="text-left border-b border-white/10">
                            <th class="py-3 pr-4">Ticket ID</th>
                            <th class="py-3 pr-4">Customer</th>
                            <th class="py-3 pr-4">Event</th>
                            <th class="py-3 pr-4">Amount</th>
                            <th class="py-3 pr-4">Date</th>
                            <th class="py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-200">
                        @foreach($pendingPayments as $ticket)
                        <tr class="border-b border-white/5">
                            <td class="py-4 pr-4 font-medium text-yellow-300">
                                #{{ $ticket->id }}
                            </td>
                            <td class="py-4 pr-4">
                                {{ $ticket->user->name ?? 'N/A' }}
                            </td>
                            <td class="py-4 pr-4">
                                {{ $ticket->event->title ?? 'N/A' }}
                            </td>
                            <td class="py-4 pr-4 font-semibold text-cyan-300">
                                ${{ number_format($ticket->total_price, 2) }}
                            </td>
                            <td class="py-4 pr-4 text-slate-400">
                                {{ $ticket->created_at->format('M d, Y') }}
                            </td>
                            <td class="py-4 flex gap-2">
                                {{-- Mark as Paid Form --}}
                                <form method="POST" action="{{ route('admin.payments.mark-paid', $ticket) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="payment_amount" value="{{ $ticket->total_price }}">
                                    <input type="hidden" name="payment_reference" value="Admin Manual Payment">
                                    <button type="submit"
                                            class="px-3 py-1 rounded-lg bg-gradient-to-r from-emerald-500 to-green-500 hover:from-emerald-400 hover:to-green-400 text-white font-medium text-xs transition shadow-lg">
                                        Mark Paid
                                    </button>
                                </form>

                                {{-- Mark as Failed Form --}}
                                <form method="POST" action="{{ route('admin.payments.mark-failed', $ticket) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="reason" value="Payment failed - admin review">
                                    <button type="submit"
                                            class="px-3 py-1 rounded-lg bg-gradient-to-r from-red-500 to-red-600 hover:from-red-400 hover:to-red-500 text-white font-medium text-xs transition shadow-lg">
                                        Mark Failed
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    @endif

    {{-- Failed Payments Section --}}
    @if($failedPayments->count() > 0)
    <section class="rounded-2xl border border-red-400/20 bg-slate-900/80 backdrop-blur-md shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
            <h2 class="text-xl font-semibold text-white flex items-center gap-3">
              <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
              </svg>
              Failed Payments ({{ $failedPayments->count() }})
            </h2>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-slate-400">
                        <tr class="text-left border-b border-white/10">
                            <th class="py-3 pr-4">Ticket ID</th>
                            <th class="py-3 pr-4">Customer</th>
                            <th class="py-3 pr-4">Event</th>
                            <th class="py-3 pr-4">Amount</th>
                            <th class="py-3 pr-4">Failed Date</th>
                            <th class="py-3 pr-4">Reason</th>
                            <th class="py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-200">
                        @foreach($failedPayments as $ticket)
                        <tr class="border-b border-white/5">
                            <td class="py-4 pr-4 font-medium text-red-300">
                                #{{ $ticket->id }}
                            </td>
                            <td class="py-4 pr-4">
                                {{ $ticket->user->name ?? 'N/A' }}
                            </td>
                            <td class="py-4 pr-4">
                                {{ $ticket->event->title ?? 'N/A' }}
                            </td>
                            <td class="py-4 pr-4 font-semibold text-cyan-300">
                                ${{ number_format($ticket->total_price, 2) }}
                            </td>
                            <td class="py-4 pr-4 text-slate-400">
                                {{ $ticket->updated_at->format('M d, Y') }}
                            </td>
                            <td class="py-4 pr-4 text-slate-400 max-w-xs truncate">
                                {{ $ticket->payment_reference ?? 'No reason provided' }}
                            </td>
                            <td class="py-4 flex gap-2">
                                {{-- Retry Payment Form --}}
                                <form method="POST" action="{{ route('admin.payments.retry', $ticket) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="px-3 py-1 rounded-lg bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-400 hover:to-blue-400 text-white font-medium text-xs transition shadow-lg">
                                        Reset to Pending
                                    </button>
                                </form>

                                {{-- Mark as Paid Form --}}
                                <form method="POST" action="{{ route('admin.payments.mark-paid', $ticket) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="payment_amount" value="{{ $ticket->total_price }}">
                                    <input type="hidden" name="payment_reference" value="Admin Manual Payment - Recovery">
                                    <button type="submit"
                                            class="px-3 py-1 rounded-lg bg-gradient-to-r from-emerald-500 to-green-500 hover:from-emerald-400 hover:to-green-400 text-white font-medium text-xs transition shadow-lg">
                                        Mark Paid
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    @endif

    {{-- No Payments Message --}}
    @if($pendingPayments->count() == 0 && $failedPayments->count() == 0)
    <section class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 text-emerald-300 p-6 shadow-lg">
        <div class="flex items-center gap-4">
            <div class="flex-shrink-0">
                <svg class="h-8 w-8 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-emerald-300 mb-1">All Clear!</h3>
                <p class="text-emerald-200">
                    Great! No pending or failed payments need your attention right now.
                </p>
            </div>
        </div>
    </section>
    @endif

  </main>
</body>
</html>
