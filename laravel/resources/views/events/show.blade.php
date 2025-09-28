@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white/80 backdrop-blur-md shadow-lg rounded-lg overflow-hidden">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-slate-800">{{ $event->title }}</h1>
                <div class="flex space-x-2">
                    <a href="{{ route('events.edit', $event->id) }}" class="px-4 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg transition-colors">
                        Edit Event
                    </a>
                    <form action="{{ route('events.destroy', $event->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors" onclick="return confirm('Are you sure you want to delete this event?')">
                            Delete
                        </button>
                    </form>
                    <a href="{{ route('events.index') }}" class="px-4 py-2 bg-slate-500 hover:bg-slate-600 text-white rounded-lg transition-colors">
                        Back to List
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    <div class="bg-slate-100 rounded-lg p-6">
                        <div class="mb-6">
                            @if($event->image_path)
                                <img src="{{ asset('storage/' . $event->image_path) }}" alt="{{ $event->title }}" class="w-full h-64 object-cover rounded-lg">
                            @else
                                <div class="w-full h-64 bg-slate-200 rounded-lg flex items-center justify-center">
                                    <span class="text-slate-500">No image available</span>
                                </div>
                            @endif
                        </div>

                        <h2 class="text-xl font-semibold text-slate-800 mb-2">Description</h2>
                        <p class="text-slate-600 mb-6 whitespace-pre-line">{{ $event->description }}</p>

                        <h2 class="text-xl font-semibold text-slate-800 mb-2">Event Details</h2>
                        <div class="space-y-2 text-slate-600">
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
                    <div class="bg-slate-100 rounded-lg p-6">
                        <h2 class="text-xl font-semibold text-slate-800 mb-4">Ticket Information</h2>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-600">Price:</span>
                                <span class="font-bold text-slate-800">
                                    @if($event->price > 0)
                                        ${{ number_format($event->price, 2) }}
                                    @else
                                        Free
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-600">Available Tickets:</span>
                                <span class="font-bold text-slate-800">{{ $event->total_tickets - $event->tickets_sold }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-600">Status:</span>
                                <span class="font-bold
                                    @if($event->status == 'published') text-green-600
                                    @elseif($event->status == 'cancelled') text-red-600
                                    @else text-orange-600
                                    @endif">
                                    {{ ucfirst($event->status) }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-600">Created by:</span>
                                <span class="font-bold text-slate-800">{{ $event->organizer->name ?? 'Unknown' }}</span>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button class="w-full py-3 bg-gradient-to-r from-cyan-500 to-blue-500 text-white rounded-lg font-bold hover:from-cyan-600 hover:to-blue-600 transition-all">
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
