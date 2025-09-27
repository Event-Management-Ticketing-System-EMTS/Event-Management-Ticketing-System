{{-- User Profile (edit) --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Profile</title>
  @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-slate-100 antialiased">

  <header class="flex items-center justify-between px-6 py-4 border-b border-slate-800 bg-slate-900/70 backdrop-blur-md">
    <h1 class="text-xl font-bold text-cyan-400">Profile Settings</h1>
    <a href="{{ route('user.dashboard') }}"
       class="px-3 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 border border-cyan-400/20 text-sm">Back to dashboard</a>
  </header>

  <main class="max-w-3xl mx-auto p-6">
    @if(session('success'))
      <div class="mb-4 rounded-lg border border-emerald-500/40 bg-emerald-500/10 text-emerald-300 p-3">
        {{ session('success') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="mb-4 rounded-lg border border-red-500/40 bg-red-500/10 text-red-300 p-3">
        <ul class="list-disc ml-4">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md shadow-lg p-6">
      <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PATCH')

        {{-- Avatar preview --}}
        <div class="flex items-center gap-4">
          <div class="h-16 w-16 rounded-full bg-slate-800 overflow-hidden ring-1 ring-cyan-400/30">
            @if($user->avatar_path ?? false)
              <img src="{{ asset('storage/'.$user->avatar_path) }}" class="h-full w-full object-cover" alt="avatar">
            @else
              <div class="h-full w-full grid place-items-center text-cyan-300 text-xl">
                {{ strtoupper(substr($user->name, 0, 1)) }}
              </div>
            @endif
          </div>
          <div>
            <label class="block text-sm mb-1 text-slate-300">Avatar</label>
            <input type="file" name="avatar"
                   class="block w-full text-sm text-slate-200 file:mr-4 file:py-2 file:px-3
                          file:rounded-lg file:border-0 file:bg-cyan-500/20 file:text-cyan-200
                          hover:file:bg-cyan-500/30">
            <p class="text-xs text-slate-400 mt-1">JPG/PNG/WEBP up to 2MB.</p>
          </div>
        </div>

        {{-- Name --}}
        <div>
          <label class="block text-sm mb-1 text-slate-300">Full name</label>
          <input name="name" value="{{ old('name', $user->name) }}" required
                 class="w-full rounded-lg bg-slate-800/70 border border-cyan-400/20 px-3 py-2.5
                        focus:outline-none focus:ring-2 focus:ring-cyan-400">
        </div>

        {{-- Email --}}
        <div>
          <label class="block text-sm mb-1 text-slate-300">Email</label>
          <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                 class="w-full rounded-lg bg-slate-800/70 border border-cyan-400/20 px-3 py-2.5
                        focus:outline-none focus:ring-2 focus:ring-cyan-400">
        </div>

        <hr class="border-white/10">

        {{-- Password change (optional) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm mb-1 text-slate-300">Current password</label>
            <input type="password" name="current_password"
                   class="w-full rounded-lg bg-slate-800/70 border border-cyan-400/20 px-3 py-2.5
                          focus:outline-none focus:ring-2 focus:ring-cyan-400" placeholder="Leave blank to keep">
          </div>
          <div>
            <label class="block text-sm mb-1 text-slate-300">New password</label>
            <input type="password" name="password"
                   class="w-full rounded-lg bg-slate-800/70 border border-cyan-400/20 px-3 py-2.5
                          focus:outline-none focus:ring-2 focus:ring-cyan-400">
          </div>
          <div>
            <label class="block text-sm mb-1 text-slate-300">Confirm new password</label>
            <input type="password" name="password_confirmation"
                   class="w-full rounded-lg bg-slate-800/70 border border-cyan-400/20 px-3 py-2.5
                          focus:outline-none focus:ring-2 focus:ring-cyan-400">
          </div>
        </div>

        <div class="flex items-center justify-end gap-3">
          <a href="{{ route('user.dashboard') }}"
             class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 border border-cyan-400/20">Cancel</a>
          <button type="submit"
             class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-sky-500 hover:from-cyan-400 hover:to-sky-400
                    shadow-md shadow-cyan-900/40">
            Save changes
          </button>
        </div>
      </form>
    </div>
  </main>
</body>
</html>
