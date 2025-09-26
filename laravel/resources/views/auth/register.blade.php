<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Register</title>
  @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-slate-900 text-slate-100 flex items-center justify-center">
  <form method="POST" action="{{ route('register.store') }}" class="w-full max-w-sm bg-slate-800 p-6 rounded-2xl">
    @csrf
    <h1 class="text-xl font-semibold mb-4">Register</h1>

    @error('name') <p class="text-red-400 text-sm mb-2">{{ $message }}</p> @enderror
    @error('email') <p class="text-red-400 text-sm mb-2">{{ $message }}</p> @enderror
    @error('password') <p class="text-red-400 text-sm mb-2">{{ $message }}</p> @enderror

    <label class="block mb-2 text-sm">Name</label>
    <input name="name" type="text" value="{{ old('name') }}" required class="w-full mb-4 px-3 py-2 rounded bg-slate-700 outline-none">

    <label class="block mb-2 text-sm">Email</label>
    <input name="email" type="email" value="{{ old('email') }}" required class="w-full mb-4 px-3 py-2 rounded bg-slate-700 outline-none">

    <label class="block mb-2 text-sm">Password</label>
    <input name="password" type="password" required class="w-full mb-4 px-3 py-2 rounded bg-slate-700 outline-none">

    <label class="block mb-2 text-sm">Confirm Password</label>
    <input name="password_confirmation" type="password" required class="w-full mb-6 px-3 py-2 rounded bg-slate-700 outline-none">

    <button class="w-full py-2 rounded bg-fuchsia-600 hover:bg-fuchsia-500 font-medium">Register</button>

    <p class="mt-3 text-sm">
      Already have an account? <a href="{{ route('login.show') }}" class="text-fuchsia-400 underline">Login</a>
    </p>
  </form>
</body>
</html>
