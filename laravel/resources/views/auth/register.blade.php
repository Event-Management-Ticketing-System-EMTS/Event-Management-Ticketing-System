{{-- Professional Registration (with eye toggle + dynamic password strength) --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Register</title>
  @vite('resources/css/app.css')
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
      {{-- Header --}}
      <div id="reg-header"
           class="mb-8 text-center opacity-0 translate-y-2 transition-all duration-500">
        <div class="mx-auto mb-4 h-12 w-12 rounded-2xl bg-cyan-500/20 ring-1 ring-cyan-400/40 grid place-items-center
                    scale-95 transition-transform duration-500">
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
      @endif

      {{-- Card --}}
      <div id="reg-card"
           class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md shadow-xl shadow-cyan-900/20
                  opacity-0 translate-y-4 scale-[.98] transition-all duration-500">

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

          {{-- Role --}}
          <div>
            <label for="role" class="block text-sm mb-1 text-slate-300">Role</label>
            <select id="role" name="role" required
                    class="w-full rounded-lg bg-slate-800/70 border border-cyan-400/20 px-3 py-2.5
                           focus:outline-none focus:ring-2 focus:ring-cyan-400">
              <option value="user"  {{ old('role','user')==='user' ? 'selected' : '' }}>User</option>
              <option value="admin" {{ old('role')==='admin' ? 'selected' : '' }}>Admin</option>
            </select>
            <p class="mt-1 text-xs text-slate-400">Admins can create/update events. Users can browse and book.</p>
          </div>

          {{-- Password --}}
          <div>
            <label for="password" class="block text-sm mb-1 text-slate-300">Password</label>
            <div class="relative">
              <input id="password" name="password" type="password" required autocomplete="new-password"
                     class="w-full rounded-lg bg-slate-800/70 border border-cyan-400/20 px-3 pr-10 py-2.5
                            focus:outline-none focus:ring-2 focus:ring-cyan-400"
                     placeholder="Create a password" />
              <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-cyan-300"
                      onclick="togglePassword('password', this)">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
              </button>
            </div>
            {{-- Dynamic strength bar --}}
            <div class="mt-2">
              <div class="h-2 w-full rounded bg-slate-800/70 overflow-hidden">
                <div id="password-strength" class="h-2 w-0 bg-red-500 transition-all duration-300"></div>
              </div>
              <p class="mt-1 text-xs text-slate-400">Use 8+ chars with uppercase, lowercase, number, and symbol.</p>
            </div>
          </div>

          {{-- Confirm Password --}}
          <div>
            <label for="password_confirmation" class="block text-sm mb-1 text-slate-300">Confirm password</label>
            <div class="relative">
              <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                     class="w-full rounded-lg bg-slate-800/70 border border-cyan-400/20 px-3 pr-10 py-2.5
                            focus:outline-none focus:ring-2 focus:ring-cyan-400"
                     placeholder="Re-type your password" />
              <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-cyan-300"
                      onclick="togglePassword('password_confirmation', this)">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
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

  {{-- animations + password toggle + dynamic strength --}}
  <script>
    // Animate header and card
    document.addEventListener('DOMContentLoaded', () => {
      const header = document.getElementById('reg-header');
      const card = document.getElementById('reg-card');
      requestAnimationFrame(() => {
        header.classList.remove('opacity-0','translate-y-2');
        const icon = header.querySelector('div'); 
        icon && icon.classList.remove('scale-95');
        card.classList.remove('opacity-0','translate-y-4','scale-[.98]');
      });
    });

    // Eye toggle
    function togglePassword(id, btn) {
      const input = document.getElementById(id);
      const svg = btn.querySelector('svg');
      if (input.type === 'password') {
        input.type = 'text';
        svg.innerHTML = `
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.519-4.362m3.617-2.367A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.956 9.956 0 01-4.132 5.411M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>`;
      } else {
        input.type = 'password';
        svg.innerHTML = `
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
      }
    }

    // Password strength
    const passwordInput = document.getElementById('password');
    const strengthBar = document.getElementById('password-strength');

    passwordInput.addEventListener('input', () => {
      const value = passwordInput.value;
      let strength = 0;

      if (value.length >= 8) strength += 1;
      if (/[A-Z]/.test(value)) strength += 1;
      if (/[0-9]/.test(value)) strength += 1;
      if (/[\W_]/.test(value)) strength += 1;

      // Set width %
      const width = (strength / 4) * 100;
      strengthBar.style.width = width + '%';

      // Color
      strengthBar.classList.remove('bg-red-500','bg-yellow-400','bg-green-400','bg-cyan-400');
      if (strength <= 1) strengthBar.classList.add('bg-red-500');
      else if (strength === 2) strengthBar.classList.add('bg-yellow-400');
      else if (strength === 3) strengthBar.classList.add('bg-green-400');
      else strengthBar.classList.add('bg-cyan-400');
    });
  </script>
</body>
</html>
