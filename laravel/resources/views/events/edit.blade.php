@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white/80 backdrop-blur-md shadow-lg rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-slate-800">Edit Event: {{ $event->title }}</h1>
                <a href="{{ route('events.show', $event->id) }}" class="px-4 py-2 bg-slate-500 hover:bg-slate-600 text-white rounded-lg transition-colors">
                    Cancel
                </a>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul class="list-disc pl-5">
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
                        <label for="title" class="block text-sm font-medium text-slate-700 mb-1">Event Title*</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $event->title) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500" required>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-slate-700 mb-1">Status*</label>
                        <select name="status" id="status" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500" required>
                            <option value="draft" {{ old('status', $event->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status', $event->status) == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="cancelled" {{ old('status', $event->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Description*</label>
                        <textarea name="description" id="description" rows="4" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500" required>{{ old('description', $event->description) }}</textarea>
                    </div>

                    <div>
                        <label for="event_date" class="block text-sm font-medium text-slate-700 mb-1">Event Date*</label>
                        <input type="date" name="event_date" id="event_date" value="{{ old('event_date', $event->event_date) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-slate-700 mb-1">Start Time*</label>
                            <input type="time" name="start_time" id="start_time" value="{{ old('start_time', $event->start_time) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500" required>
                        </div>
                        <div>
                            <label for="end_time" class="block text-sm font-medium text-slate-700 mb-1">End Time</label>
                            <input type="time" name="end_time" id="end_time" value="{{ old('end_time', $event->end_time) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        </div>
                    </div>

                    <div>
                        <label for="venue" class="block text-sm font-medium text-slate-700 mb-1">Venue*</label>
                        <input type="text" name="venue" id="venue" value="{{ old('venue', $event->venue) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500" required>
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-medium text-slate-700 mb-1">Address</label>
                        <input type="text" name="address" id="address" value="{{ old('address', $event->address) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-slate-700 mb-1">City</label>
                        <input type="text" name="city" id="city" value="{{ old('city', $event->city) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>

                    <div>
                        <label for="total_tickets" class="block text-sm font-medium text-slate-700 mb-1">Total Tickets*</label>
                        <input type="number" name="total_tickets" id="total_tickets" value="{{ old('total_tickets', $event->total_tickets) }}" min="1" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500" required>
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-slate-700 mb-1">Ticket Price*</label>
                        <input type="number" name="price" id="price" value="{{ old('price', $event->price) }}" min="0" step="0.01" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500" required>
                    </div>

                    <div class="md:col-span-2">
                        <label for="image" class="block text-sm font-medium text-slate-700 mb-1">Event Image</label>
                        <div class="flex items-center space-x-4">
                            @if($event->image_path)
                                <div class="w-20 h-20 bg-slate-200 rounded-lg overflow-hidden">
                                    <img src="{{ asset('storage/' . $event->image_path) }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                                </div>
                                <span class="text-sm text-slate-600">Current image</span>
                            @endif
                        </div>
                        <input type="file" name="image" id="image" class="mt-2 w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        <p class="text-xs text-slate-500 mt-1">Leave empty to keep current image. Max size: 2MB. Recommended: 1200 x 800 pixels.</p>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-cyan-500 to-blue-500 text-white rounded-lg font-bold hover:from-cyan-600 hover:to-blue-600 transition-all">
                        Update Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
