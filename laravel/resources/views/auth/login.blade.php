<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login</title>
  @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-slate-900 text-slate-100 flex items-center justify-center">
  <form method="POST" action="{{ route('login.perform') }}" class="w-full max-w-sm bg-slate-800 p-6 rounded-2xl">
    @csrf
    <h1 class="text-xl font-semibold mb-4">Login</h1>

    @if(session('error'))
      <p class="text-red-400 text-sm mb-2">{{ session('error') }}</p>
    @endif
    @if(session('success'))
      <p class="text-green-400 text-sm mb-2">{{ session('success') }}</p>
    @endif
    @error('email') <p class="text-red-400 text-sm mb-2">{{ $message }}</p> @enderror

    <label class="block mb-2 text-sm">Email</label>
    <input name="email" type="email" value="{{ old('email') }}" required class="w-full mb-4 px-3 py-2 rounded bg-slate-700 outline-none">

    <label class="block mb-2 text-sm">Password</label>
    <input name="password" type="password" required class="w-full mb-6 px-3 py-2 rounded bg-slate-700 outline-none">

    <button class="w-full py-2 rounded bg-fuchsia-600 hover:bg-fuchsia-500 font-medium">Sign in</button>

    <p class="mt-3 text-sm">
      No account? <a href="{{ route('register.show') }}" class="text-fuchsia-400 underline">Register</a>
    </p>
  </form>
</body>
</html>
