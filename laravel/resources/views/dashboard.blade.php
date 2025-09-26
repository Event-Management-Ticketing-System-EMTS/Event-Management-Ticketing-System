{{-- Dashboard page added--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-3xl font-bold text-fuchsia-400">Welcome to your Dashboard</h1>
        <p class="mt-2 text-slate-300">You are logged in successfully 🎉</p>

        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button class="px-4 py-2 rounded bg-red-600 hover:bg-red-500">Logout</button>
        </form>
    </div>
</body>
</html>
