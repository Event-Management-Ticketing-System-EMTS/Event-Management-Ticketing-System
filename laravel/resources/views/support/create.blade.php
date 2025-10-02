@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-900 text-white">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-cyan-400">Contact Support</h1>
            <a href="{{ route('user.dashboard') }}" class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-lg transition-colors">
                Back to Dashboard
            </a>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-900 text-green-300 p-4 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Support Form -->
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <h2 class="text-xl font-semibold text-cyan-300 mb-4">Send a Message</h2>

            <form action="{{ route('support.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Subject -->
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-300 mb-2">Subject</label>
                    <input type="text"
                           id="subject"
                           name="subject"
                           value="{{ old('subject') }}"
                           required
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                           placeholder="What can we help you with?">
                    @error('subject')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Event (Optional) -->
                <div>
                    <label for="event_id" class="block text-sm font-medium text-gray-300 mb-2">Related Event (Optional)</label>
                    <select id="event_id"
                            name="event_id"
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                        <option value="">No specific event</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>
                                {{ $event->title }} - {{ $event->event_date->format('M d, Y') }}
                            </option>
                        @endforeach
                    </select>
                    @error('event_id')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-300 mb-2">Priority</label>
                    <select id="priority"
                            name="priority"
                            required
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low - General question</option>
                        <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium - Need assistance</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High - Urgent issue</option>
                    </select>
                    @error('priority')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Message -->
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-300 mb-2">Message</label>
                    <textarea id="message"
                              name="message"
                              rows="6"
                              required
                              class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                              placeholder="Please describe your question or issue in detail...">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit"
                            class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-2 rounded-lg transition-colors font-medium">
                        Send Message
                    </button>
                </div>
            </form>
        </div>

        <!-- Help Information -->
        <div class="mt-8 bg-gray-800 rounded-lg p-6 border border-gray-700">
            <h3 class="text-lg font-semibold text-cyan-300 mb-3">How can we help?</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-300">
                <div>
                    <h4 class="font-medium text-white mb-2">📅 Event Questions</h4>
                    <p>Ask about event details, schedules, or venue information.</p>
                </div>
                <div>
                    <h4 class="font-medium text-white mb-2">🎫 Ticket Issues</h4>
                    <p>Problems with your tickets, cancellations, or refunds.</p>
                </div>
                <div>
                    <h4 class="font-medium text-white mb-2">💡 General Support</h4>
                    <p>Any other questions or technical issues you're experiencing.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
