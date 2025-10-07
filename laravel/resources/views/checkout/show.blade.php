@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="min-h-screen bg-slate-950 text-slate-100">
  <div class="max-w-4xl mx-auto px-6 py-10">

    <a href="{{ route('events.show', $event->id) }}" class="inline-flex items-center text-sky-300 hover:text-sky-200 mb-6">
      <i class="fa fa-arrow-left mr-2"></i> Back to Event
    </a>

    <div class="bg-slate-900/70 border border-slate-700 rounded-2xl p-6 shadow-xl">
      <h1 class="text-2xl font-bold text-cyan-300 mb-4">Checkout</h1>

      <div class="grid md:grid-cols-2 gap-6">
        <!-- Summary -->
        <div class="rounded-xl bg-slate-800/60 border border-slate-700 p-5">
          <h2 class="text-lg font-semibold text-slate-200 mb-3">{{ $event->title }}</h2>
          <p class="text-sm text-slate-400 mb-1">Date: {{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}</p>
          <p class="text-sm text-slate-400 mb-1">Price: ${{ number_format($event->price, 2) }}</p>
          <p class="text-sm text-slate-400 mb-1">Quantity: {{ $qty }}</p>
          <hr class="my-3 border-slate-700">
          <p class="text-base font-semibold text-cyan-300">Total: ${{ number_format($total, 2) }}</p>
        </div>

        <!-- Payment methods -->
        <div class="rounded-xl bg-slate-800/60 border border-slate-700 p-5">
          <form id="checkoutForm" action="{{ route('checkout.process', $event->id) }}" method="POST" class="space-y-5">
            @csrf
            <input type="hidden" name="qty" value="{{ $qty }}">

            <fieldset>
              <legend class="text-sm text-slate-400 mb-2">Select a payment method</legend>

              <label class="flex items-center gap-3 p-3 rounded-lg bg-slate-900/50 border border-slate-700 hover:border-sky-600 cursor-pointer mb-2">
                <input type="radio" name="payment_method" value="card" class="accent-sky-500" required>
                <div>
                  <div class="font-medium">Card (Stripe-like)</div>
                  <div class="text-xs text-slate-400">Visa, MasterCard, AMEX</div>
                </div>
              </label>

              <label class="flex items-center gap-3 p-3 rounded-lg bg-slate-900/50 border border-slate-700 hover:border-sky-600 cursor-pointer mb-2">
                <input type="radio" name="payment_method" value="wallet" class="accent-sky-500">
                <div>
                  <div class="font-medium">Mobile Wallet (bKash/Nagad)</div>
                  <div class="text-xs text-slate-400">Pay using local wallet</div>
                </div>
              </label>

              <label class="flex items-center gap-3 p-3 rounded-lg bg-slate-900/50 border border-slate-700 hover:border-sky-600 cursor-pointer">
                <input type="radio" name="payment_method" value="cash" class="accent-sky-500">
                <div>
                  <div class="font-medium">Cash at Venue</div>
                  <div class="text-xs text-slate-400">Pay when you arrive</div>
                </div>
              </label>
            </fieldset>

            <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-400 hover:to-blue-500 text-white font-semibold shadow">
              <i class="fa fa-lock"></i> Proceed
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  {{-- Processing overlay --}}
  <div id="processingOverlay" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-50">
    <div class="text-center">
      <div class="animate-spin h-12 w-12 border-4 border-sky-400 border-t-transparent rounded-full mx-auto mb-4"></div>
      <p class="text-slate-200">Processing payment…</p>
    </div>
  </div>
</div>

<script>
  // Show a processing overlay while the POST happens
  const form = document.getElementById('checkoutForm');
  const overlay = document.getElementById('processingOverlay');

  form.addEventListener('submit', function () {
    overlay.classList.remove('hidden');
    overlay.classList.add('flex');
  });
</script>
@endsection
