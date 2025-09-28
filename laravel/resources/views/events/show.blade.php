@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-slate-100 antialiased">
  {{-- Background glow + grid --}}
  <div class="fixed inset-0 -z-10">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(6,182,212,0.15),transparent_70%)]"></div>
    <div class="absolute inset-0 opacity-[0.05] [mask-image:linear-gradient(to_bottom,black,transparent)]">
      <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
            <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="0.5"/>
          </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#grid)" />
      </svg>
    </div>
  </div>

  {{-- Topbar --}}
  <header class="flex items-center justify-between px-6 py-4 border-b border-slate-800 bg-slate-900/70 backdrop-blur-md">
    <div class="flex items-center gap-3">
      <div class="h-9 w-9 rounded-xl bg-cyan-500/20 ring-1 ring-cyan-400/40 grid place-items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-cyan-400" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 3l9 4.5v9L12 21 3 16.5v-9L12 3zM5 8l7 3 7-3-7-3-7 3zm7 5l7-3v5l-7 3-7-3v-5l7 3z"/>
        </svg>
      </div>
      <h1 class="text-xl font-bold text-cyan-300">Event Details</h1>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('events.edit', $event->id) }}" class="px-4 py-2 rounded-lg bg-gradient-to-r from-cyan-500 to-sky-500 hover:from-cyan-400 hover:to-sky-400 text-white text-sm font-medium shadow-md">
        Edit Event
      </a>
      <form action="{{ route('events.destroy', $event->id) }}" method="POST" class="inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="px-4 py-2 rounded-lg bg-red-500 hover:bg-red-600 text-white text-sm font-medium" onclick="return confirm('Are you sure you want to delete this event?')">
          Delete
        </button>
      </form>
      <a href="{{ route('events.index') }}" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 border border-cyan-400/20 text-sm transition">
        Back to List
      </a>
    </div>
  </header>

  <main class="max-w-7xl mx-auto p-6">
    <div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-6 shadow-lg">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-cyan-300">{{ $event->title }}</h1>
      </div>

            @if(session('success'))
                <div class="mb-6 rounded-xl border border-green-400/30 bg-green-400/10 p-4">
                  <div class="flex">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400 mr-2" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-green-300">{{ session('success') }}</span>
                  </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    <div class="bg-slate-800/80 backdrop-blur-sm rounded-lg p-6 border border-slate-700/50">
                        <div class="mb-6">
                            @if($event->image_path)
                                <img src="{{ asset('storage/' . $event->image_path) }}" alt="{{ $event->title }}" class="w-full h-64 object-cover rounded-lg">
                            @else
                                <div class="w-full h-64 bg-slate-700 rounded-lg flex items-center justify-center">
                                    <span class="text-slate-400">No image available</span>
                                </div>
                            @endif
                        </div>

                        <h2 class="text-xl font-semibold text-cyan-300 mb-2">Description</h2>
                        <p class="text-slate-300 mb-6 whitespace-pre-line">{{ $event->description }}</p>

                        <h2 class="text-xl font-semibold text-cyan-300 mb-2">Event Details</h2>
                        <div class="space-y-2 text-slate-300">
                            <p><span class="font-medium">Date:</span> {{ \Carbon\Carbon::parse($event->event_date)->format('F j, Y') }}</p>
                            <p><span class="font-medium">Time:</span> {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }}
                                @if($event->end_time)
                                    - {{ \Carbon\Carbon::parse($event->end_time)->format('g:i A') }}
                                @endif
                            </p>
                            <p><span class="font-medium">Venue:</span> {{ $event->venue }}</p>
                            @if($event->address)
                                <p><span class="font-medium">Address:</span> {{ $event->address }}</p>
                            @endif
                            @if($event->city)
                                <p><span class="font-medium">City:</span> {{ $event->city }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="md:col-span-1">
                    <div class="bg-slate-800/80 backdrop-blur-sm rounded-lg p-6 border border-slate-700/50">
                        <h2 class="text-xl font-semibold text-cyan-300 mb-4">Ticket Information</h2>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400">Price:</span>
                                <span class="font-bold text-cyan-300">
                                    @if($event->price > 0)
                                        ${{ number_format($event->price, 2) }}
                                    @else
                                        Free
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400">Available Tickets:</span>
                                <span class="font-bold text-cyan-300">{{ $event->total_tickets - $event->tickets_sold }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400">Status:</span>
                                @if($event->status == 'published')
                                  <span class="inline-flex rounded-full bg-green-500/10 px-2 py-1 text-xs font-medium text-green-400 ring-1 ring-inset ring-green-500/30">Published</span>
                                @elseif($event->status == 'cancelled')
                                  <span class="inline-flex rounded-full bg-red-500/10 px-2 py-1 text-xs font-medium text-red-400 ring-1 ring-inset ring-red-500/30">Cancelled</span>
                                @else
                                  <span class="inline-flex rounded-full bg-amber-500/10 px-2 py-1 text-xs font-medium text-amber-400 ring-1 ring-inset ring-amber-500/30">Draft</span>
                                @endif
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400">Created by:</span>
                                <span class="font-bold text-cyan-300">{{ $event->organizer->name ?? 'Unknown' }}</span>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button class="w-full py-3 bg-gradient-to-r from-cyan-500 to-blue-500 text-white rounded-lg font-bold hover:from-cyan-400 hover:to-blue-400 transition-all shadow-md">
                                Buy Tickets
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
