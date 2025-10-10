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
      <h1 class="text-xl font-bold text-cyan-300">Edit Event: {{ $event->title }}</h1>
    </div>

    <div class="flex items-center gap-4">
      <a href="{{ route('events.show', $event->id) }}" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 border border-cyan-400/20 text-sm transition">
        Cancel
      </a>
    </div>
  </header>

  <main class="max-w-4xl mx-auto p-6">
    <div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-6 shadow-lg">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-cyan-300">Edit Event Details</h2>
      </div>

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-400/30 bg-red-400/10 p-4">
                  <div class="flex items-center mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400 mr-2" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <h3 class="text-red-400 font-medium">Please fix the following errors:</h3>
                  </div>
                  <ul class="list-disc list-inside pl-2 text-red-300 text-sm">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
            @endif

            <form action="{{ route('events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-slate-300 mb-1">Event Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title', $event->title) }}"
                          class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500" required>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-slate-300 mb-1">Status <span class="text-red-500">*</span></label>
                        <select name="status" id="status"
                          class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500" required>
                            <option value="draft" {{ old('status', $event->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status', $event->status) == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="cancelled" {{ old('status', $event->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-slate-300 mb-1">Description <span class="text-red-500">*</span></label>
                        <textarea name="description" id="description" rows="4"
                          class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500" required>{{ old('description', $event->description) }}</textarea>
                    </div>

                    <div>
                        <label for="event_date" class="block text-sm font-medium text-slate-300 mb-1">Event Date <span class="text-red-500">*</span></label>
                        <input type="date" name="event_date" id="event_date" value="{{ old('event_date', $event->event_date) }}"
                          class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-slate-300 mb-1">Start Time <span class="text-red-500">*</span></label>
                            <input type="time" name="start_time" id="start_time"
                              value="{{ old('start_time', $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('H:i') : '') }}"
                              class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500" required>
                        </div>
                        <div>
                            <label for="end_time" class="block text-sm font-medium text-slate-300 mb-1">End Time</label>
                            <input type="time" name="end_time" id="end_time"
                              value="{{ old('end_time', $event->end_time ? \Carbon\Carbon::parse($event->end_time)->format('H:i') : '') }}"
                              class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500">
                        </div>
                    </div>

                    <div>
                        <label for="venue" class="block text-sm font-medium text-slate-300 mb-1">Venue <span class="text-red-500">*</span></label>
                        <input type="text" name="venue" id="venue" value="{{ old('venue', $event->venue) }}"
                          class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500" required>
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-medium text-slate-300 mb-1">Address</label>
                        <input type="text" name="address" id="address" value="{{ old('address', $event->address) }}"
                          class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500">
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-slate-300 mb-1">City</label>
                        <input type="text" name="city" id="city" value="{{ old('city', $event->city) }}"
                          class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500">
                    </div>

                    <div>
                        <label for="total_tickets" class="block text-sm font-medium text-slate-300 mb-1">Total Tickets <span class="text-red-500">*</span></label>
                        <input type="number" name="total_tickets" id="total_tickets" value="{{ old('total_tickets', $event->total_tickets) }}" min="1"
                          class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500" required>
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-slate-300 mb-1">Ticket Price <span class="text-red-500">*</span></label>
                        <input type="number" name="price" id="price" value="{{ old('price', $event->price) }}" min="0" step="0.01"
                          class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500" required>
                    </div>                                        <div class="md:col-span-2">
                        <label for="image" class="block text-sm font-medium text-slate-300 mb-1">Event Image</label>
                        <div class="flex items-center space-x-4">
                            @if($event->image_path)
                                <div class="w-20 h-20 bg-slate-700 rounded-lg overflow-hidden">
                                    <img src="{{ asset('storage/' . $event->image_path) }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                                </div>
                                <span class="text-sm text-slate-300">Current image</span>
                            @endif
                        </div>
                        <input type="file" id="image" name="image" accept="image/*"
                          class="mt-2 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100 file:mr-4 file:rounded file:border-0
                          file:bg-cyan-600 file:px-4 file:py-2 file:text-white hover:file:bg-cyan-500">
                        <p class="text-xs text-slate-400 mt-1">Leave empty to keep current image. Max size: 2MB. Recommended: 1200 x 800 pixels.</p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-slate-800">
                    <a href="{{ route('events.show', $event->id) }}" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 rounded-lg bg-gradient-to-r from-cyan-500 to-sky-500 hover:from-cyan-400 hover:to-sky-400 text-white font-medium shadow-md">
                        Update Event
                    </button>
                </div>
            </form>
        </div>
    </div>
  </main>
</div>
@endsection
