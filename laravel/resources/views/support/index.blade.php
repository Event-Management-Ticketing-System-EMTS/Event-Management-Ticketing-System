@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-900 text-white">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-cyan-400">Support Messages</h1>
            <a href="{{ route('dashboard') }}" class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-lg transition-colors">
                Back to Admin Dashboard
            </a>
        </div>

        @if($messages->count() > 0)
            <div class="grid gap-6">
                @foreach($messages as $message)
                    <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <div class="flex items-center space-x-4 mb-2">
                                    <h3 class="text-xl font-semibold text-cyan-400">{{ $message->subject }}</h3>

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

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-gray-300 text-sm mb-4">
                                    <div>
                                        <span class="text-gray-400">From:</span>
                                        <p>{{ $message->user->name }} ({{ $message->user->email }})</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-400">Event:</span>
                                        <p>{{ $message->event ? $message->event->title : 'General inquiry' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-400">Date:</span>
                                        <p>{{ $message->created_at->format('M d, Y H:i') }}</p>
                                    </div>
                                </div>

                                <div class="bg-gray-700 rounded p-4 mb-4">
                                    <p class="text-gray-200">{{ $message->message }}</p>
                                </div>

                                @if($message->admin_response)
                                    <div class="bg-cyan-900 rounded p-4">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-cyan-300 font-medium">Admin Response:</span>
                                            <span class="text-cyan-400 text-sm">
                                                by {{ $message->admin->name ?? 'Admin' }} -
                                                {{ $message->admin_responded_at->format('M d, Y H:i') }}
                                            </span>
                                        </div>
                                        <p class="text-cyan-100">{{ $message->admin_response }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('admin.support.show', $message->id) }}"
                               class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                                {{ $message->admin_response ? 'View/Update' : 'Respond' }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-16">
                <div class="text-gray-400 text-6xl mb-4">📬</div>
                <h2 class="text-2xl font-semibold text-gray-300 mb-4">No Support Messages</h2>
                <p class="text-gray-400">No users have submitted support requests yet.</p>
            </div>
        @endif
    </div>
</div>
@endsection
