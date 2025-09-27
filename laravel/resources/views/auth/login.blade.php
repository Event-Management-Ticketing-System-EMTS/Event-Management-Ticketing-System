{{-- Professional Login with Premium Colors + subtle entrance --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login</title>
  @vite('resources/css/app.css')
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-slate-100 antialiased">

  {{-- Subtle grid + glow overlay --}}
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

  <main class="flex items-center justify-center p-6">
    <div class="w-full max-w-md">
      <div id="login-header" class="mb-8 text-center opacity-0 translate-y-2 transition-all duration-500">
        {{-- Logo / Brand mark --}}
        <div class="mx-auto mb-4 h-12 w-12 rounded-2xl bg-cyan-500/20 ring-1 ring-cyan-400/40 grid place-items-center
                    scale-95 transition-transform duration-500">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-cyan-400" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 3l9 4.5v9L12 21 3 16.5v-9L12 3zM5 8l7 3 7-3-7-3-7 3zm7 5l7-3v5l-7 3-7-3v-5l7 3z"/>
          </svg>
        </div>
        <h1 class="text-2xl font-semibold tracking-tight text-cyan-300">Welcome back</h1>
        <p class="mt-1 text-sm text-slate-400">Sign in to continue to your dashboard</p>
      </div>

      {{-- Alerts --}}
      @if (session('success'))
        <div class="mb-4 text-sm bg-emerald-500/10 border border-emerald-500/40 text-emerald-300 rounded-lg p-3">
          {{ session('success') }}
        </div>
      @endif

      @if ($errors->any())
        <div class="mb-4 text-sm bg-red-500/10 border border-red-500/40 text-red-300 rounded-lg p-3">
          <ul class="list-disc ml-4">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      {{-- Card --}}
      <div id="login-card"
        x-data="{loading:false, show:false}"
        class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md shadow-xl shadow-cyan-900/20
               opacity-0 translate-y-4 scale-[.98] transition-all duration-500"
      >
        <form method="POST" action="{{ route('login.perform') }}" class="p-6 space-y-5" x-on:submit="loading=true">
          @csrf

          {{-- Email --}}
          <div>
            <label for="email" class="block text-sm mb-1 text-slate-300">Email</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-cyan-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M2.94 6.34L10 10.88l7.06-4.54A2 2 0 0015.82 4H4.18a2 2 0 00-1.24 2.34z" />
                  <path d="M18 8.13l-7.4 4.76a1 1 0 01-1.2 0L2 8.13V14a2 2 0 002 2h12a2 2 0 002-2V8.13z" />
                </svg>
              </span>
              <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="email"
                class="w-full rounded-lg bg-slate-800/70 border border-cyan-400/20 pl-10 pr-3 py-2.5
                       focus:outline-none focus:ring-2 focus:ring-cyan-400"
                placeholder="you@example.com"
              />
            </div>
          </div>

          {{-- Password --}}
          <div x-data="{show:false}">
            <label for="password" class="block text-sm mb-1 text-slate-300">Password</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-cyan-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 1a5 5 0 00-5 5v3H6a2 2 0 00-2 2v7a2 2 0 002 2h12a2 2 0 002-2v-7a2 2 0 00-2-2h-1V6a5 5 0 00-5-5zm-3 8V6a3 3 0 016 0v3H9z"/>
                </svg>
              </span>
              <input
                :type="show ? 'text' : 'password'"
                id="password"
                name="password"
                required
                autocomplete="current-password"
                class="w-full rounded-lg bg-slate-800/70 border border-cyan-400/20 pl-10 pr-10 py-2.5
                       focus:outline-none focus:ring-2 focus:ring-cyan-400"
                placeholder="Your password"
              />
              <button type="button" x-on:click="show = !show"
                class="absolute inset-y-0 right-0 pr-3 flex items-center text-cyan-400 hover:text-cyan-200">
                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5C21.27 7.61 17 4.5 12 4.5zm0 12a4.5 4.5 0 110-9 4.5 4.5 0 010 9z"/>
                </svg>
                <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M3.28 2.22L2.22 3.28 5.2 6.27A12.72 12.72 0 001 12c1.73 4.39 6 7.5 11 7.5 2.1 0 4.06-.53 5.8-1.47l2.92 2.92 1.06-1.06-18.5-18.5zM12 17.5c-3.97 0-7.35-2.48-8.88-5.5A10.73 10.73 0 016.3 8.04l2.2 2.2A4.5 4.5 0 0012 16.5c.63 0 1.23-.12 1.78-.34l1.66 1.66c-.86.43-1.8.68-2.78.68z"/>
                </svg>
              </button>
            </div>
          </div>

          {{-- Options row --}}
          <div class="flex items-center justify-between">
            <label class="inline-flex items-center gap-2 text-sm text-slate-300">
              <input type="checkbox" name="remember" class="rounded border-white/20 bg-slate-800/70 text-cyan-500 focus:ring-cyan-400">
              Remember me
            </label>
            <a href="{{ route('password.request') }}" class="text-sm text-cyan-300 hover:text-cyan-200">Forgot password?</a>
          </div>

          {{-- Submit --}}
          <button type="submit"
            :class="loading ? 'opacity-70 cursor-not-allowed' : ''"
            class="w-full inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5
                   bg-gradient-to-r from-cyan-500 to-sky-500 hover:from-cyan-400 hover:to-sky-400
                   focus:outline-none focus:ring-2 focus:ring-offset-0 focus:ring-cyan-400
                   shadow-lg shadow-cyan-900/30 transition">
            <svg x-show="loading" class="h-5 w-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
            <span>Login</span>
          </button>
        </form>

        <div class="px-6 py-4 border-t border-white/10 text-sm text-center text-slate-400">
          New here?
          <a href="{{ route('register.show') }}" class="text-cyan-300 hover:text-cyan-200">Create an account</a>
        </div>
      </div>
    </div>
  </main>

  {{-- tiny script to trigger entrance --}}
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const header = document.getElementById('login-header');
      const card = document.getElementById('login-card');
      requestAnimationFrame(() => {
        header.classList.remove('opacity-0','translate-y-2');
        const icon = header.querySelector('div'); // logo block
        icon && icon.classList.remove('scale-95');
        card.classList.remove('opacity-0','translate-y-4','scale-[.98]');
      });
    });
  </script>
</body>
</html>
