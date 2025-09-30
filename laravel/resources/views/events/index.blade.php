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
      <h1 class="text-xl font-bold text-cyan-300">Event Management</h1>
    </div>

    <div class="flex items-center gap-4">
      <a href="{{ route('events.statistics') }}" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 border border-cyan-400/20 text-sm transition">
        View Statistics
      </a>
      <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 border border-cyan-400/20 text-sm transition">
        Back to Dashboard
      </a>
    </div>
  </header>

  <main class="max-w-7xl mx-auto p-6 space-y-6">
    {{-- Page header --}}
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-white">All Events</h1>
      <a href="{{ route('events.create') }}" class="px-4 py-2 rounded-lg bg-gradient-to-r from-cyan-500 to-sky-500 hover:from-cyan-400 hover:to-sky-400 text-white font-medium shadow-md">
        Create New Event
      </a>
    </div>

    {{-- Sorting Controls --}}
    <x-sorting-controls
        :action="route('events.index')"
        :sort-options="$sortOptions"
        :current-sort="$sortBy"
        :current-direction="$sortDirection"
        :total-count="$events->count()"
        :show-reset="!$isDefaultSort"
    />

    {{-- Status messages --}}
    @if (session('success'))
    <div class="rounded-xl border border-green-400/30 bg-green-400/10 p-4">
      <div class="flex">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400 mr-2" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <span class="text-green-300">{{ session('success') }}</span>
      </div>
    </div>
    @endif

    {{-- Events table --}}
    <div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-6 shadow-lg overflow-x-auto">
      @if($events->isEmpty())
        <div class="text-center py-8">
          <p class="text-slate-400 mb-4">No events found.</p>
          <a href="{{ route('events.create') }}" class="px-4 py-2 rounded-lg bg-gradient-to-r from-cyan-500 to-sky-500 hover:from-cyan-400 hover:to-sky-400 text-white font-medium shadow-md">
            Create Your First Event
          </a>
        </div>
      @else
        <table class="w-full" id="eventsTable">
          <thead>
            <tr class="border-b border-slate-800">
              <th class="px-4 py-3 text-left">
                <a href="{{ route('events.index', ['sort' => 'title', 'direction' => $sortBy === 'title' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}"
                   class="flex items-center gap-1 text-sm font-semibold text-cyan-300 hover:text-cyan-200 transition-colors">
                  Event
                  @if($sortBy === 'title')
                    @if($sortDirection === 'asc')
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      </svg>
                    @else
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                      </svg>
                    @endif
                  @endif
                </a>
              </th>
              <th class="px-4 py-3 text-left">
                <a href="{{ route('events.index', ['sort' => 'event_date', 'direction' => $sortBy === 'event_date' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}"
                   class="flex items-center gap-1 text-sm font-semibold text-cyan-300 hover:text-cyan-200 transition-colors">
                  Date
                  @if($sortBy === 'event_date')
                    @if($sortDirection === 'asc')
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      </svg>
                    @else
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                      </svg>
                    @endif
                  @endif
                </a>
              </th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-cyan-300">Venue</th>
              <th class="px-4 py-3 text-left">
                <a href="{{ route('events.index', ['sort' => 'price', 'direction' => $sortBy === 'price' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}"
                   class="flex items-center gap-1 text-sm font-semibold text-cyan-300 hover:text-cyan-200 transition-colors">
                  Price
                  @if($sortBy === 'price')
                    @if($sortDirection === 'asc')
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      </svg>
                    @else
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                      </svg>
                    @endif
                  @endif
                </a>
              </th>
              <th class="px-4 py-3 text-left">
                <a href="{{ route('events.index', ['sort' => 'tickets_sold', 'direction' => $sortBy === 'tickets_sold' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}"
                   class="flex items-center gap-1 text-sm font-semibold text-cyan-300 hover:text-cyan-200 transition-colors">
                  Tickets
                  @if($sortBy === 'tickets_sold')
                    @if($sortDirection === 'asc')
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      </svg>
                    @else
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                      </svg>
                    @endif
                  @endif
                </a>
              </th>
              <th class="px-4 py-3 text-left">
                <a href="{{ route('events.index', ['sort' => 'status', 'direction' => $sortBy === 'status' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}"
                   class="flex items-center gap-1 text-sm font-semibold text-cyan-300 hover:text-cyan-200 transition-colors">
                  Status
                  @if($sortBy === 'status')
                    @if($sortDirection === 'asc')
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      </svg>
                    @else
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                      </svg>
                    @endif
                  @endif
                </a>
              </th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-cyan-300">Approval</th>
              <th class="px-4 py-3 text-right text-sm font-semibold text-cyan-300">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($events as $event)
            <tr class="border-b border-slate-800">
              <td class="px-4 py-3">
                <div class="font-medium">{{ $event->title }}</div>
                <div class="text-sm text-slate-400">{{ Str::limit($event->description, 50) }}</div>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">{{ $event->event_date->format('M d, Y') }}</td>
              <td class="px-4 py-3">{{ $event->venue }}</td>
              <td class="px-4 py-3 whitespace-nowrap">${{ number_format($event->price, 2) }}</td>
              <td class="px-4 py-3">{{ $event->tickets_sold }}/{{ $event->total_tickets }}</td>
              <td class="px-4 py-3">
                @if($event->status === 'published')
                  <span class="inline-flex rounded-full bg-green-500/10 px-2 py-1 text-xs font-medium text-green-400 ring-1 ring-inset ring-green-500/30">Published</span>
                @elseif($event->status === 'draft')
                  <span class="inline-flex rounded-full bg-amber-500/10 px-2 py-1 text-xs font-medium text-amber-400 ring-1 ring-inset ring-amber-500/30">Draft</span>
                @else
                  <span class="inline-flex rounded-full bg-red-500/10 px-2 py-1 text-xs font-medium text-red-400 ring-1 ring-inset ring-red-500/30">Cancelled</span>
                @endif
              </td>
              <td class="px-4 py-3">
                @if($event->approval_status === 'approved')
                  <span class="inline-flex rounded-full bg-green-500/10 px-2 py-1 text-xs font-medium text-green-400 ring-1 ring-inset ring-green-500/30">Approved</span>
                @elseif($event->approval_status === 'pending')
                  <span class="inline-flex rounded-full bg-yellow-500/10 px-2 py-1 text-xs font-medium text-yellow-400 ring-1 ring-inset ring-yellow-500/30">Pending</span>
                @else
                  <span class="inline-flex rounded-full bg-red-500/10 px-2 py-1 text-xs font-medium text-red-400 ring-1 ring-inset ring-red-500/30">Rejected</span>
                @endif
              </td>
              <td class="px-4 py-3 text-right space-x-1 whitespace-nowrap">
                <a href="{{ route('events.edit', $event->id) }}" class="inline-flex items-center px-2 py-1 rounded text-xs font-medium text-cyan-400 hover:bg-slate-800">
                  Edit
                </a>
                <a href="{{ route('events.show', $event->id) }}" class="inline-flex items-center px-2 py-1 rounded text-xs font-medium text-slate-300 hover:bg-slate-800">
                  View
                </a>
                <form class="inline-block" method="POST" action="{{ route('events.destroy', $event->id) }}" onsubmit="return confirm('Are you sure you want to delete this event?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="inline-flex items-center px-2 py-1 rounded text-xs font-medium text-red-400 hover:bg-slate-800">
                    Delete
                  </button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      @endif
    </div>
  </main>
</div>
@endsection
