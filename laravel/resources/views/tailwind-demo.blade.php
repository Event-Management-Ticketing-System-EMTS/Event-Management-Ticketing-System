<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Tailwind Demo</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-900 text-slate-100 grid place-items-center p-6">
  <div class="max-w-xl w-full space-y-4">
    <div class="rounded-2xl border border-fuchsia-400/20 bg-slate-800/60 shadow-lg p-6">
      <h1 class="text-3xl font-semibold">✅ Tailwind is working</h1>
      <p class="text-slate-300">This card uses Tailwind v3 utilities.</p>
      <form class="mt-4 space-y-3">
        <label class="block">
          <span class="text-sm">Email</span>
          <input type="email" class="mt-1 block w-full rounded-xl" placeholder="you@example.com">
        </label>
        <button class="px-4 py-2 rounded-xl bg-fuchsia-600 hover:bg-fuchsia-700 transition">
          Test Button
        </button>
      </form>
    </div>
  </div>
</body>
</html>
