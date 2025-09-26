{{-- Login page setup finished --}}

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 flex items-center justify-center p-4">
  <div class="w-full max-w-md bg-slate-900/60 rounded-2xl p-6 border border-fuchsia-400/20">
    <h1 class="text-2xl font-semibold mb-4 text-fuchsia-400">Welcome back</h1>

    @if (session('success'))
      <div class="mb-4 text-sm bg-emerald-500/10 border border-emerald-500/40 text-emerald-300 rounded p-3">
        {{ session('success') }}
      </div>
    @endif
  
    @if ($errors->any())
      <div class="mb-4 text-sm bg-red-500/10 border border-red-500/40 text-red-300 rounded p-3">
        <ul class="list-disc ml-4">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('login.perform') }}" class="space-y-4">
      @csrf
      <div>
        <label class="block mb-1 text-sm">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required
               class="w-full rounded-lg bg-slate-800 border border-slate-700 px-3 py-2">
      </div>
      <div>
        <label class="block mb-1 text-sm">Password</label>
        <input type="password" name="password" required
               class="w-full rounded-lg bg-slate-800 border border-slate-700 px-3 py-2">
      </div>
      <button class="w-full py-2 rounded-xl bg-fuchsia-600 hover:bg-fuchsia-500">Login</button>
    </form>

    <p class="mt-4 text-sm">
      New here?
      <a href="{{ route('register.show') }}" class="text-fuchsia-400 hover:underline">Create an account</a>
    </p>
  </div>
</body>
</html>
