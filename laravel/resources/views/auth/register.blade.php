{{-- Professional Registration (matches new Login) --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Register</title>
  @vite('resources/css/app.css')
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-slate-100 antialiased">

  {{-- Subtle grid + cyan glow --}}
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
      <div class="mb-8 text-center">
        <div class="mx-auto mb-4 h-12 w-12 rounded-2xl bg-cyan-500/20 ring-1 ring-cyan-400/40 grid place-items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-cyan-400" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 3l9 4.5v9L12 21 3 16.5v-9L12 3zM5 8l7 3 7-3-7-3-7 3zm7 5l7-3v5l-7 3-7-3v-5l7 3z"/>
          </svg>
        </div>
        <h1 class="text-2xl font-semibold tracking-tight text-cyan-300">Create your account</h1>
        <p class="mt-1 text-sm text-slate-400">Join and get access to your dashboard</p>
      </div>

      {{-- Alerts --}}
      @if ($errors->any())
        <div class="mb-4 text-sm bg-red-500/10 border border-red-500/40 text-red-300 rounded-lg p-3">
          <ul class="list-disc ml-4">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @if (session('success'))
        <div class="mb-4 text-sm bg-emerald-500/10 border border-emerald-500/40 text-emerald-300 rounded-lg p-3">
          {{ session('success') }}
        </div>
      @endif>

      {{-- Card --}}
      <div x-data="{showPass:false, showConf:false, pwd:'', strength:0}"
           x-effect="
             // simple strength score: length + diversity
             strength = 0;
             if (pwd.length >= 8) strength += 1;
             if (/[A-Z]/.test(pwd)) strength += 1;
             if (/[a-z]/.test(pwd)) strength += 1;
             if (/\d/.test(pwd)) strength += 1;
             if (/[^A-Za-z0-9]/.test(pwd)) strength += 1;
           "
           class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md shadow-xl shadow-cyan-900/20">

        <form method="POST" action="{{ route('register.perform') }}" class="p-6 space-y-5">
          @csrf

          {{-- Name --}}
          <div>
            <label for="name" class="block text-sm mb-1 text-slate-300">Full name</label>
            <input id="name" name="name" value="{{ old('name') }}" required
                   class="w-full rounded-lg bg-slate-800/70 border border-cyan-400/20 px-3 py-2.5
                          focus:outline-none focus:ring-2 focus:ring-cyan-400"
                   placeholder="Your name" />
          </div>

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
              <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                     class="w-full rounded-lg bg-slate-800/70 border border-cyan-400/20 pl-10 pr-3 py-2.5
                            focus:outline-none focus:ring-2 focus:ring-cyan-400"
                     placeholder="you@example.com" />
            </div>
          </div>

          {{-- Password --}}
          <div>
            <label for="password" class="block text-sm mb-1 text-slate-300">Password</label>
            <div class="relative">
              <input :type="showPass ? 'text' : 'password'"
                     id="password" name="password" required autocomplete="new-password"
                     x-model="pwd"
                     class="w-full rounded-lg bg-slate-800/70 border border-cyan-400/20 pr-10 px-3 py-2.5
                            focus:outline-none focus:ring-2 focus:ring-cyan-400"
                     placeholder="Create a password" />
              <button type="button" x-on:click="showPass = !showPass"
                      class="absolute inset-y-0 right-0 pr-3 flex items-center text-cyan-400 hover:text-cyan-200">
                <svg x-show="!showPass" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5C21.27 7.61 17 4.5 12 4.5zm0 12a4.5 4.5 0 110-9 4.5 4.5 0 010 9z"/>
                </svg>
                <svg x-show="showPass" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M3.28 2.22L2.22 3.28 5.2 6.27A12.72 12.72 0 001 12c1.73 4.39 6 7.5 11 7.5 2.1 0 4.06-.53 5.8-1.47l2.92 2.92 1.06-1.06-18.5-18.5zM12 17.5c-3.97 0-7.35-2.48-8.88-5.5A10.73 10.73 0 016.3 8.04l2.2 2.2A4.5 4.5 0 0012 16.5c.63 0 1.23-.12 1.78-.34l1.66 1.66c-.86.43-1.8.68-2.78.68z"/>
                </svg>
              </button>
            </div>

            {{-- Strength meter --}}
            <div class="mt-2">
              <div class="h-2 w-full rounded bg-slate-800/70 overflow-hidden">
                <div class="h-2 transition-all"
                     :class="[
                       strength <= 1 ? 'bg-red-400 w-1/5' :
                       strength == 2 ? 'bg-orange-400 w-2/5' :
                       strength == 3 ? 'bg-yellow-400 w-3/5' :
                       strength == 4 ? 'bg-lime-400 w-4/5' :
                                       'bg-cyan-400 w-full'
                     ]"></div>
              </div>
              <p class="mt-1 text-xs text-slate-400">
                Use 8+ chars with upper/lowercase, a number, and a symbol.
              </p>
            </div>
          </div>

          {{-- Confirm Password --}}
          <div>
            <label for="password_confirmation" class="block text-sm mb-1 text-slate-300">Confirm password</label>
            <div class="relative">
              <input :type="showConf ? 'text' : 'password'"
                     id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                     class="w-full rounded-lg bg-slate-800/70 border border-cyan-400/20 pr-10 px-3 py-2.5
                            focus:outline-none focus:ring-2 focus:ring-cyan-400"
                     placeholder="Re-type your password" />
              <button type="button" x-on:click="showConf = !showConf"
                      class="absolute inset-y-0 right-0 pr-3 flex items-center text-cyan-400 hover:text-cyan-200">
                <svg x-show="!showConf" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5C21.27 7.61 17 4.5 12 4.5zm0 12a4.5 4.5 0 110-9 4.5 4.5 0 010 9z"/></svg>
                <svg x-show="showConf" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M3.28 2.22L2.22 3.28 5.2 6.27A12.72 12.72 0 001 12c1.73 4.39 6 7.5 11 7.5 2.1 0 4.06-.53 5.8-1.47l2.92 2.92 1.06-1.06-18.5-18.5zM12 17.5c-3.97 0-7.35-2.48-8.88-5.5A10.73 10.73 0 016.3 8.04l2.2 2.2A4.5 4.5 0 0012 16.5c.63 0 1.23-.12 1.78-.34l1.66 1.66c-.86.43-1.8.68-2.78.68z"/></svg>
              </button>
            </div>
          </div>

          {{-- Submit --}}
          <button type="submit"
                  class="w-full rounded-xl px-4 py-2.5 bg-gradient-to-r from-cyan-500 to-sky-500 hover:from-cyan-400 hover:to-sky-400
                         focus:outline-none focus:ring-2 focus:ring-cyan-400 shadow-lg shadow-cyan-900/30 transition">
            Create account
          </button>
        </form>

        <div class="px-6 py-4 border-t border-white/10 text-sm text-center text-slate-400">
          Already have an account?
          <a href="{{ route('login.show') }}" class="text-cyan-300 hover:text-cyan-200">Log in</a>
        </div>
      </div>
    </div>
  </main>
</body>
</html>
