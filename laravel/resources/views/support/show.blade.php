@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="min-h-screen bg-slate-950 text-slate-100">
  <div class="max-w-4xl mx-auto px-6 py-10">

    <a href="{{ route('events.show', $event->id) }}"
       class="inline-flex items-center text-sky-300 hover:text-sky-200 mb-6">
      <svg class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4A1 1 0 018.707 6.293L6.414 8.586H17a1 1 0 110 2H6.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
      </svg>
      Back to Event
    </a>

    {{-- Optional server messages --}}
    @if(session('error'))
      <div class="mb-4 rounded-lg bg-red-500/10 border border-red-500/30 p-3 text-red-300">
        {{ session('error') }}
      </div>
    @endif
    @if(session('success'))
      <div class="mb-4 rounded-lg bg-emerald-500/10 border border-emerald-500/30 p-3 text-emerald-300">
        {{ session('success') }}
      </div>
    @endif

    <div class="bg-slate-900/70 border border-slate-700 rounded-2xl p-6 shadow-xl">
      <h1 class="text-2xl font-bold text-cyan-300 mb-4">Checkout</h1>

      <div class="grid md:grid-cols-2 gap-6">
        <!-- Summary -->
        <section class="rounded-xl bg-slate-800/60 border border-slate-700 p-5">
          <h2 class="text-lg font-semibold text-slate-200 mb-3">{{ $event->title }}</h2>
          <dl class="space-y-1 text-sm">
            <div class="flex justify-between">
              <dt class="text-slate-400">Date</dt>
              <dd class="text-slate-300">{{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}</dd>
            </div>
            <div class="flex justify-between">
              <dt class="text-slate-400">Price</dt>
              <dd class="text-slate-300">${{ number_format($event->price, 2) }}</dd>
            </div>
            <div class="flex justify-between">
              <dt class="text-slate-400">Quantity</dt>
              <dd class="text-slate-300">{{ $qty }}</dd>
            </div>
          </dl>
          <hr class="my-4 border-slate-700">
          <div class="flex justify-between text-base font-semibold">
            <span class="text-slate-200">Total</span>
            <span class="text-cyan-300">${{ number_format($total, 2) }}</span>
          </div>
        </section>

        <!-- Payment methods -->
        <section class="rounded-xl bg-slate-800/60 border border-slate-700 p-5">
          <form id="checkoutForm"
                action="{{ route('checkout.process', $event->id) }}"
                method="POST"
                data-redirect="{{ route('tickets.my') }}"
                class="space-y-5">
            @csrf
            <input type="hidden" name="qty" value="{{ $qty }}">

            <fieldset class="space-y-3">
              <legend class="text-sm text-slate-400 mb-1">Select a payment method</legend>

              <label class="group flex items-center gap-3 p-3 rounded-lg bg-slate-900/50 border border-slate-700 hover:border-sky-600 cursor-pointer transition">
                <input type="radio" name="payment_method" value="card" class="accent-sky-500" required>
                <div class="leading-tight">
                  <div class="font-medium group-hover:text-slate-100">Card (Stripe-like)</div>
                  <div class="text-xs text-slate-400">Visa, MasterCard, AMEX</div>
                </div>
              </label>

              <label class="group flex items-center gap-3 p-3 rounded-lg bg-slate-900/50 border border-slate-700 hover:border-sky-600 cursor-pointer transition">
                <input type="radio" name="payment_method" value="wallet" class="accent-sky-500">
                <div class="leading-tight">
                  <div class="font-medium group-hover:text-slate-100">Mobile Wallet (bKash/Nagad)</div>
                  <div class="text-xs text-slate-400">Pay using local wallet</div>
                </div>
              </label>

              <label class="group flex items-center gap-3 p-3 rounded-lg bg-slate-900/50 border border-slate-700 hover:border-sky-600 cursor-pointer transition">
                <input type="radio" name="payment_method" value="cash" class="accent-sky-500">
                <div class="leading-tight">
                  <div class="font-medium group-hover:text-slate-100">Cash at Venue</div>
                  <div class="text-xs text-slate-400">Pay when you arrive</div>
                </div>
              </label>
            </fieldset>

            <button id="proceedBtn" type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-400 hover:to-blue-500 text-white font-semibold shadow transition">
              <svg id="btnIcon" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M2.003 5.884 10 2l7.997 3.884v6.232L10 16l-7.997-3.884V5.884z"/>
              </svg>
              <span id="btnText">Proceed</span>
            </button>
          </form>
        </section>
      </div>
    </div>
  </div>

  <!-- Processing / Success Modal Overlay -->
  <div id="overlay" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

    <!-- Modal card -->
    <div class="relative z-10 mx-auto w-full max-w-sm px-6">
      <div class="mt-32 rounded-2xl bg-slate-900 border border-slate-700 shadow-2xl p-6 text-center">
        <!-- Spinner -->
        <div id="spinner" class="mx-auto mb-4 h-12 w-12 border-4 border-cyan-400 border-t-transparent rounded-full animate-spin"></div>

        <!-- Success check -->
        <div id="successIcon" class="hidden mx-auto mb-4 h-12 w-12 rounded-full bg-emerald-500 flex items-center justify-center shadow-lg">
          <svg class="h-7 w-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
            <path d="M20 6L9 17l-5-5"/>
          </svg>
        </div>

        <h3 id="statusTitle" class="text-lg font-semibold text-slate-100">Processing payment…</h3>
        <p id="statusSub" class="mt-1 text-sm text-slate-400">Please wait a moment.</p>

        <div class="mt-5 h-1 w-full bg-slate-800 rounded overflow-hidden">
          <div id="progressFill" class="h-full w-0 bg-cyan-400 transition-[width] duration-300 ease-out"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Toast (for errors) -->
  <div id="toast" class="fixed bottom-6 right-6 z-[60] hidden">
    <div class="rounded-lg bg-red-500/10 border border-red-500/30 px-4 py-3 text-red-200 shadow-lg">
      <span id="toastMsg">Something went wrong.</span>
    </div>
  </div>
</div>

<script>
  const form        = document.getElementById('checkoutForm');
  const overlay     = document.getElementById('overlay');
  const spinner     = document.getElementById('spinner');
  const successIcon = document.getElementById('successIcon');
  const statusTitle = document.getElementById('statusTitle');
  const statusSub   = document.getElementById('statusSub');
  const progress    = document.getElementById('progressFill');
  const toast       = document.getElementById('toast');
  const toastMsg    = document.getElementById('toastMsg');
  const btn         = document.getElementById('proceedBtn');
  const btnIcon     = document.getElementById('btnIcon');
  const btnText     = document.getElementById('btnText');
  const token       = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  function showOverlay() {
    overlay.classList.remove('hidden');
    statusTitle.textContent = 'Processing payment…';
    statusSub.textContent   = 'Please wait a moment.';
    spinner.classList.remove('hidden');
    successIcon.classList.add('hidden');
    progress.style.width = '0%';
    // staged progress for nicer feel
    setTimeout(() => progress.style.width = '35%', 150);
    setTimeout(() => progress.style.width = '65%', 500);
  }
  function showSuccessAndRedirect(url) {
    progress.style.width = '100%';
    spinner.classList.add('hidden');
    successIcon.classList.remove('hidden');
    statusTitle.textContent = 'Payment successful!';
    let seconds = 1.5; // countdown seconds
    statusSub.textContent = `Redirecting in ${seconds.toFixed(1)}s…`;
    const tick = setInterval(() => {
      seconds -= 0.1;
      statusSub.textContent = `Redirecting in ${seconds.toFixed(1)}s…`;
    }, 100);
    setTimeout(() => { clearInterval(tick); window.location.href = url; }, 1500);
  }
  function showToast(msg) {
    toastMsg.textContent = msg;
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 3500);
  }
  function setSubmitting(on) {
    btn.disabled = on;
    btn.classList.toggle('opacity-60', on);
    btn.classList.toggle('cursor-not-allowed', on);
    btnText.textContent = on ? 'Processing…' : 'Proceed';
    btnIcon.classList.toggle('hidden', on);
  }

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const redirectUrl = form.dataset.redirect;

    setSubmitting(true);
    showOverlay();

    try {
      const formData = new FormData(form);
      const r = await fetch(form.action, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': token,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        body: formData,
        credentials: 'same-origin'
      });

      const ct = r.headers.get('content-type') || '';
      if (ct.includes('application/json')) {
        const data = await r.json();
        if (r.ok && (data.success ?? true)) {
          showSuccessAndRedirect(data.redirect || redirectUrl);
        } else {
          overlay.classList.add('hidden');
          showToast(data.message || 'Payment failed. Please try again.');
        }
      } else {
        // If HTML/redirect page came back, still complete gracefully
        showSuccessAndRedirect(redirectUrl);
      }
    } catch (err) {
      overlay.classList.add('hidden');
      console.error(err);
      showToast('Network error. Please try again.');
    } finally {
      setSubmitting(false);
    }
  });
</script>
@endsection
