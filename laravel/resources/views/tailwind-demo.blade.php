<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Tailwind Demo</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  <style>
    /* Extra 3D tilt effect */
    .tilt:hover {
      transform: perspective(1000px) rotateX(6deg) rotateY(6deg);
      transition: transform 0.4s ease;
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-900 via-purple-900 to-fuchsia-900 text-slate-100 p-6">

  <div class="max-w-xl w-full">
    <div class="tilt rounded-3xl border border-fuchsia-400/30 
                bg-slate-800/40 backdrop-blur-xl 
                shadow-2xl shadow-fuchsia-500/20 
                p-8 space-y-6 transition-all">

      <h1 class="text-4xl font-extrabold bg-gradient-to-r from-fuchsia-400 to-indigo-400 bg-clip-text text-transparent">
        ✅ Tailwind 3D Demo
      </h1>

      <p class="text-slate-300">
        If you see gradient backgrounds, blurred glass card, and hover effects — Tailwind is working!
      </p>

      <form class="space-y-4">
        <label class="block">
          <span class="text-sm text-fuchsia-200">Email</span>
          <input type="email" 
                 class="mt-1 block w-full rounded-xl bg-slate-900/70 border border-fuchsia-400/30 
                        focus:ring-2 focus:ring-fuchsia-400 focus:outline-none p-2"
                 placeholder="you@example.com">
        </label>

        <button class="px-6 py-3 rounded-xl font-semibold 
                       bg-gradient-to-r from-fuchsia-500 via-purple-500 to-indigo-600
                       hover:from-fuchsia-400 hover:via-purple-400 hover:to-indigo-500
                       shadow-lg shadow-fuchsia-500/40 transition-all">
          🚀 Test Button
        </button>
      </form>

      <div class="grid grid-cols-3 gap-4 pt-4">
        <div class="h-20 rounded-xl bg-gradient-to-br from-pink-500 to-red-500 animate-pulse"></div>
        <div class="h-20 rounded-xl bg-gradient-to-br from-green-400 to-emerald-600 animate-bounce"></div>
        <div class="h-20 rounded-xl bg-gradient-to-br from-blue-400 to-cyan-600 animate-spin"></div>
      </div>

    </div>
  </div>

</body>
</html>
