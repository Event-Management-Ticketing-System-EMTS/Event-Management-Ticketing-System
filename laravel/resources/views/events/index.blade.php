@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-slate-100 antialiased">
  {{-- Background glow + grid (unchanged but still a great UI element) --}}
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

  {{-- Topbar: Increased padding, distinct hover/focus, subtle shadow --}}
  <header class="sticky top-0 z-50 flex items-center justify-between px-8 py-4 border-b border-slate-800 bg-slate-900/70 backdrop-blur-xl shadow-xl">
    <div class="flex items-center gap-3">
      <div class="h-10 w-10 rounded-full bg-cyan-500/20 ring-2 ring-cyan-400/50 grid place-items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-cyan-400" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 3l9 4.5v9L12 21 3 16.5v-9L12 3zM5 8l7 3 7-3-7-3-7 3zm7 5l7-3v5l-7 3-7-3v-5l7 3z"/>
        </svg>
      </div>
      <h1 class="text-2xl font-extrabold text-white tracking-wider">EVENT <span class="text-cyan-400">HUB</span></h1>
    </div>

    <div class="flex items-center gap-3">
      @if(auth()->check() && auth()->user()->role === 'admin')
        <a href="{{ route('events.statistics') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 border border-cyan-400/30 text-sm font-medium transition duration-200 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-cyan-500/50">
          📈 Statistics
        </a>
        <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 border border-cyan-400/30 text-sm font-medium transition duration-200 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-cyan-500/50">
          ⚙️ Dashboard
        </a>
      @else
        <a href="{{ route('user.dashboard') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 border border-cyan-400/30 text-sm font-medium transition duration-200 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-cyan-500/50">
          🏠 Dashboard
        </a>
      @endif
    </div>
  </header>

  <main class="max-w-7xl mx-auto p-6 lg:p-10 space-y-8">

    {{-- Page header: Updated typography and button style --}}
    <div class="flex justify-between items-center pb-4 border-b border-slate-800">
      <h1 class="text-4xl font-extrabold text-white">Browse Events ✨</h1>
      @if(auth()->check() && auth()->user()->role === 'admin')
        <a href="{{ route('events.create') }}" class="px-5 py-2.5 rounded-full bg-gradient-to-r from-cyan-500 to-sky-500 hover:from-cyan-400 hover:to-sky-400 text-white font-semibold shadow-lg transition duration-300 hover:shadow-cyan-500/50 transform hover:-translate-y-0.5">
          + Create New Event
        </a>
      @endif
    </div>

    ---

    {{-- Search & Filters: Re-ordered for the requested flow --}}
    <form method="GET" action="{{ route('events.index') }}" class="rounded-3xl border border-cyan-400/10 bg-slate-900/70 backdrop-blur-lg p-6 shadow-2xl transition duration-300 hover:border-cyan-400/20 space-y-5">
      {{-- Input/Select Styling --}}
      @php
          $input_classes = "w-full rounded-xl bg-slate-800 border border-slate-700 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition duration-200";
          $label_classes = "block text-xs font-semibold uppercase text-cyan-400 mb-1 tracking-wider";
      @endphp

      {{-- 1. Full-width Search bar at the top --}}
      <div>
        <label class="{{ $label_classes }} text-base text-white">Search</label>
        <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Title, description, venue, city…"
               class="w-full rounded-xl bg-slate-800 border border-slate-700 px-4 py-3 text-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition duration-200">
      </div>

      {{-- 2. Filtering methods (City, Status, Dates, Prices) --}}
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 pt-3 border-t border-slate-800">
        {{-- Row 1: City, Status, and Approval Status --}}
        <div>
          <label class="{{ $label_classes }}">City</label>
          <select name="city" class="{{ $input_classes }}">
            <option value="">Any</option>
            @foreach(($cities ?? []) as $c)
              <option value="{{ $c }}" @selected(($city ?? null) === $c)>{{ $c }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="{{ $label_classes }}">Event Status</label>
          <select name="status" class="{{ $input_classes }}">
            <option value="">Any (Default: Published)</option>
            @foreach(($statuses ?? []) as $key => $label)
              <option value="{{ $key }}" @selected(($status ?? null) === $key)>{{ $label }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="{{ $label_classes }}">Approval Status</label>
          <select name="approval_status" class="{{ $input_classes }}">
            <option value="">Any (Default: Approved)</option>
            @foreach(($approvalStatuses ?? []) as $key => $label)
              <option value="{{ $key }}" @selected(($approvalStatus ?? null) === $key)>{{ $label }}</option>
            @endforeach
          </select>
        </div>

        {{-- Row 2: Date Range --}}
        <div>
          <label class="{{ $label_classes }}">From Date</label>
          <input type="date" name="date_from" value="{{ $dateFrom ?? '' }}" class="{{ $input_classes }}">
        </div>

        <div>
          <label class="{{ $label_classes }}">To Date</label>
          <input type="date" name="date_to" value="{{ $dateTo ?? '' }}" class="{{ $input_classes }}">
        </div>

        {{-- Row 3: Price Range --}}
        <div>
          <label class="{{ $label_classes }}">Min Price</label>
          <input type="number" step="0.01" name="price_min" value="{{ $priceMin ?? '' }}" placeholder="0.00" class="{{ $input_classes }}">
        </div>
        <div>
          <label class="{{ $label_classes }}">Max Price</label>
          <input type="number" step="0.01" name="price_max" value="{{ $priceMax ?? '' }}" placeholder="999.99" class="{{ $input_classes }}">
        </div>
      </div>

      {{-- 3. Action Buttons at the bottom right --}}
      <div class="flex items-center gap-3 justify-end pt-3 border-t border-slate-800">
        <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-cyan-600 to-sky-600 hover:from-cyan-500 hover:to-sky-500 font-semibold text-white shadow-md transition duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-cyan-500">
          🔍 Apply Filters
        </button>
        <a href="{{ route('events.index') }}" class="px-6 py-2.5 rounded-xl bg-slate-800 border border-slate-700 hover:bg-slate-700 font-medium transition duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-slate-500">
          Clear Filters
        </a>
      </div>
    </form>

    ---

    {{-- Sorting Controls (No change, as it uses a custom x-component) --}}
    <x-sorting-controls
      :action="route('events.index')"
      :sort-options="$sortOptions"
      :current-sort="$sortBy"
      :current-direction="$sortDirection"
      :total-count="$events->total()"
      :show-reset="!$isDefaultSort"
    />

    {{-- Flash message: Stronger visual feedback --}}
    @if (session('success'))
      <div class="rounded-xl border-l-4 border-green-500 bg-green-900/30 p-4 shadow-lg animate-fadeIn">
        <div class="flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-400 mr-3" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
          <span class="text-green-300 font-medium">{{ session('success') }}</span>
        </div>
      </div>
    @endif

    {{-- Build a helper array so sort links keep current filters --}}
    @php($qParams = request()->all())

    ---

    {{-- Events table: Modernized table design with row hover --}}
    <div class="rounded-3xl border border-cyan-400/10 bg-slate-900/70 backdrop-blur-lg shadow-2xl overflow-x-auto">
      @if($events->isEmpty())
        <div class="text-center py-12">
          <p class="text-slate-400 text-lg mb-6">😔 No events match your current filters. Try broadening your search!</p>
          @if(auth()->check() && auth()->user()->role === 'admin')
            <a href="{{ route('events.create') }}" class="px-6 py-3 rounded-full bg-gradient-to-r from-cyan-500 to-sky-500 hover:from-cyan-400 hover:to-sky-400 text-white font-semibold shadow-lg transition duration-300 hover:shadow-cyan-500/50 transform hover:-translate-y-0.5">
              🚀 Create Your First Event
            </a>
          @endif
        </div>
      @else
        <table class="min-w-full divide-y divide-slate-800" id="eventsTable">
          <thead>
            <tr class="text-left text-sm text-cyan-300 uppercase tracking-wider bg-slate-800/50">
              <th class="px-6 py-3">
                <a href="{{ route('events.index', array_merge($qParams, ['sort' => 'title', 'direction' => $sortBy === 'title' && $sortDirection === 'asc' ? 'desc' : 'asc'])) }}"
                   class="flex items-center gap-1 font-bold hover:text-cyan-200 transition-colors">
                  Event
                  @if($sortBy === 'title')
                    @if($sortDirection === 'asc')
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                    @else
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    @endif
                  @endif
                </a>
              </th>

              <th class="px-6 py-3">
                <a href="{{ route('events.index', array_merge($qParams, ['sort' => 'event_date', 'direction' => $sortBy === 'event_date' && $sortDirection === 'asc' ? 'desc' : 'asc'])) }}"
                   class="flex items-center gap-1 font-bold hover:text-cyan-200 transition-colors">
                  Date
                  @if($sortBy === 'event_date')
                    @if($sortDirection === 'asc')
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                    @else
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    @endif
                  @endif
                </a>
              </th>

              <th class="px-6 py-3 font-bold">Venue</th>

              <th class="px-6 py-3">
                <a href="{{ route('events.index', array_merge($qParams, ['sort' => 'price', 'direction' => $sortBy === 'price' && $sortDirection === 'asc' ? 'desc' : 'asc'])) }}"
                   class="flex items-center gap-1 font-bold hover:text-cyan-200 transition-colors">
                  Price
                  @if($sortBy === 'price')
                    @if($sortDirection === 'asc')
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                    @else
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    @endif
                  @endif
                </a>
              </th>

              <th class="px-6 py-3">
                <a href="{{ route('events.index', array_merge($qParams, ['sort' => 'tickets_sold', 'direction' => $sortBy === 'tickets_sold' && $sortDirection === 'asc' ? 'desc' : 'asc'])) }}"
                   class="flex items-center gap-1 font-bold hover:text-cyan-200 transition-colors">
                  Tickets
                  @if($sortBy === 'tickets_sold')
                    @if($sortDirection === 'asc')
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                    @else
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    @endif
                  @endif
                </a>
              </th>

              <th class="px-6 py-3">
                <a href="{{ route('events.index', array_merge($qParams, ['sort' => 'status', 'direction' => $sortBy === 'status' && $sortDirection === 'asc' ? 'desc' : 'asc'])) }}"
                   class="flex items-center gap-1 font-bold hover:text-cyan-200 transition-colors">
                  Status
                  @if($sortBy === 'status')
                    @if($sortDirection === 'asc')
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                    @else
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    @endif
                  @endif
                </a>
              </th>

              <th class="px-6 py-3 font-bold">Approval</th>
              <th class="px-6 py-3 text-right font-bold">Actions</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-800">
            @foreach($events as $event)
              <tr class="hover:bg-slate-800/60 transition-colors group">
                <td class="px-6 py-4">
                  <div class="font-bold text-lg text-white group-hover:text-cyan-300">{{ $event->title }}</div>
                  <div class="text-sm text-slate-400 mt-0.5">{{ Str::limit($event->description, 50) }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-slate-300">
                  <span class="inline-flex items-center gap-1 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-cyan-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    {{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm text-slate-300">{{ $event->venue }}</td>
                <td class="px-6 py-4 whitespace-nowrap font-semibold text-green-400">${{ number_format($event->price, 2) }}</td>
                <td class="px-6 py-4 text-sm text-slate-300">{{ $event->tickets_sold }}/{{ $event->total_tickets }}</td>
                <td class="px-6 py-4">
                  @if($event->status === 'published')
                    <span class="inline-flex rounded-full bg-green-500/20 px-3 py-1 text-xs font-semibold text-green-300 ring-1 ring-inset ring-green-500/50 shadow-sm">
                      <span class="h-2 w-2 mr-1.5 rounded-full bg-green-400 animate-pulse"></span> Published
                    </span>
                  @elseif($event->status === 'draft')
                    <span class="inline-flex rounded-full bg-amber-500/20 px-3 py-1 text-xs font-semibold text-amber-300 ring-1 ring-inset ring-amber-500/50 shadow-sm">
                      <span class="h-2 w-2 mr-1.5 rounded-full bg-amber-400"></span> Draft
                    </span>
                  @else
                    <span class="inline-flex rounded-full bg-red-500/20 px-3 py-1 text-xs font-semibold text-red-300 ring-1 ring-inset ring-red-500/50 shadow-sm">
                      <span class="h-2 w-2 mr-1.5 rounded-full bg-red-400"></span> Cancelled
                    </span>
                  @endif
                </td>
                <td class="px-6 py-4">
                  @if($event->approval_status === 'approved')
                    <span class="inline-flex rounded-full bg-green-500/20 px-3 py-1 text-xs font-semibold text-green-300 ring-1 ring-inset ring-green-500/50 shadow-sm">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Approved
                    </span>
                  @elseif($event->approval_status === 'pending')
                    <span class="inline-flex rounded-full bg-yellow-500/20 px-3 py-1 text-xs font-semibold text-yellow-300 ring-1 ring-inset ring-yellow-500/50 shadow-sm">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1.5 animate-spin" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v2a1 1 0 11-2 0V4a1 1 0 011-1zm6.293 3.293a1 1 0 01-1.414 1.414l-1.414-1.414a1 1 0 111.414-1.414l1.414 1.414zM16 11a1 1 0 100-2h-2a1 1 0 100 2h2zm-3.293 3.707a1 1 0 01-1.414 1.414l-1.414-1.414a1 1 0 011.414-1.414l1.414 1.414zM10 17a1 1 0 110-2h2a1 1 0 110 2h-2zM3.707 14.707a1 1 0 011.414-1.414l1.414 1.414a1 1 0 11-1.414 1.414l-1.414-1.414zM4 11a1 1 0 100-2H2a1 1 0 100 2h2zM3.707 6.293a1 1 0 011.414 1.414l1.414-1.414a1 1 0 01-1.414-1.414L3.707 6.293z" clip-rule="evenodd"/></svg> Pending
                    </span>
                  @else
                    <span class="inline-flex rounded-full bg-red-500/20 px-3 py-1 text-xs font-semibold text-red-300 ring-1 ring-inset ring-red-500/50 shadow-sm">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg> Rejected
                    </span>
                  @endif
                </td>
                {{-- Actions: Better grouping and hover effects --}}
                <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                  <a href="{{ route('events.edit', $event->id) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-cyan-500/10 text-cyan-400 hover:bg-cyan-500/20 transition duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zm-5.657 5.657l-1.414 1.414L10 14.414l-2.828 2.828-1.414-1.414L8.586 13l-4-4 4-4 4 4z"/></svg> Edit
                  </a>
                  <a href="{{ route('events.show', $event->id) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700 transition duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg> View
                  </a>
                  <form class="inline-block" method="POST" action="{{ route('events.destroy', $event->id) }}" onsubmit="return confirm('⚠️ Are you absolutely sure you want to permanently delete this event? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-500/10 text-red-400 hover:bg-red-500/20 transition duration-150">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 112 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd"/></svg> Delete
                    </button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>

        {{-- Pagination: Enhanced style --}}
        <div class="mt-6 p-4 border-t border-slate-800">
          {{ $events->links() }}
        </div>
      @endif
    </div>
  </main>
</div>
@endsection
