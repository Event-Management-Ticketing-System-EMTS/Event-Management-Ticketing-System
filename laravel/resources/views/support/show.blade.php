@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-900 text-white">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-cyan-400">Support Message</h1>
            <a href="{{ route('admin.support.index') }}" class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-lg transition-colors">
                Back to All Messages
            </a>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-900 text-green-300 p-4 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Message Details -->
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700 mb-6">
            <div class="flex items-center space-x-4 mb-4">
                <h2 class="text-2xl font-semibold text-cyan-400">{{ $message->subject }}</h2>

                <!-- Status Badge -->
                <span class="px-3 py-1 rounded-full text-sm font-medium
                    @if($message->status === 'open')
                        bg-red-900 text-red-300
                    @elseif($message->status === 'in_progress')
                        bg-yellow-900 text-yellow-300
                    @else
                        bg-green-900 text-green-300
                    @endif
                ">
                    {{ ucfirst(str_replace('_', ' ', $message->status)) }}
                </span>

                <!-- Priority Badge -->
                <span class="px-2 py-1 rounded text-xs font-medium
                    @if($message->priority === 'high')
                        bg-red-700 text-red-200
                    @elseif($message->priority === 'medium')
                        bg-yellow-700 text-yellow-200
                    @else
                        bg-gray-700 text-gray-200
                    @endif
                ">
                    {{ ucfirst($message->priority) }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-gray-300 text-sm mb-6">
                <div>
                    <span class="text-gray-400">From:</span>
                    <p class="font-medium">{{ $message->user->name }}</p>
                    <p class="text-gray-400">{{ $message->user->email }}</p>
                </div>
                <div>
                    <span class="text-gray-400">Related Event:</span>
                    <p>{{ $message->event ? $message->event->title : 'General inquiry' }}</p>
                    @if($message->event)
                        <p class="text-gray-400">{{ $message->event->event_date->format('M d, Y') }}</p>
                    @endif
                </div>
                <div>
                    <span class="text-gray-400">Submitted:</span>
                    <p>{{ $message->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>

            <div class="bg-gray-700 rounded p-4">
                <h3 class="text-cyan-300 font-medium mb-2">User Message:</h3>
                <p class="text-gray-200 whitespace-pre-wrap">{{ $message->message }}</p>
            </div>
        </div>

        <!-- Existing Admin Response -->
        @if($message->admin_response)
            <div class="bg-cyan-900 rounded-lg p-6 border border-cyan-700 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-cyan-300 font-medium">Previous Response</h3>
                    <span class="text-cyan-400 text-sm">
                        by {{ $message->admin->name ?? 'Admin' }} -
                        {{ $message->admin_responded_at->format('M d, Y H:i') }}
                    </span>
                </div>
                <p class="text-cyan-100 whitespace-pre-wrap">{{ $message->admin_response }}</p>
            </div>
        @endif

        <!-- Response Form -->
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <h3 class="text-xl font-semibold text-cyan-300 mb-4">
                {{ $message->admin_response ? 'Update Response' : 'Send Response' }}
            </h3>

            <form action="{{ route('admin.support.respond', $message->id) }}" method="POST" class="space-y-6">
                @csrf

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                    <select id="status"
                            name="status"
                            required
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                        <option value="open" {{ $message->status === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ $message->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ $message->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    </select>
                    @error('status')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Response -->
                <div>
                    <label for="admin_response" class="block text-sm font-medium text-gray-300 mb-2">Your Response</label>
                    <textarea id="admin_response"
                            name="admin_response"
                            rows="6"
                            required
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                            placeholder="Type your response to the user...">{{ old('admin_response', $message->admin_response) }}</textarea>
                    @error('admin_response')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit"
                            class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-2 rounded-lg transition-colors font-medium">
                        Send Response
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
