@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-900 text-white py-12">
    <div class="container mx-auto px-4">

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10">
            <h1 class="text-4xl font-extrabold text-cyan-400 mb-4 md:mb-0">Contact Support</h1>
            <a href="{{ route('user.dashboard') }}"
               class="inline-block bg-gray-700 hover:bg-gray-600 px-5 py-2 rounded-lg text-sm font-medium transition-colors shadow-md hover:shadow-lg">
                ← Back to Dashboard
            </a>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-900 text-green-300 p-4 rounded-lg mb-8 shadow-inner">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

            <!-- Support Form -->
            <div class="bg-gray-800 rounded-xl p-8 border border-gray-700 shadow-lg">
                <h2 class="text-2xl font-semibold text-cyan-300 mb-6">Send a Message</h2>

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
                               placeholder="What can we help you with?"
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition">
                        @error('subject')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Event (Optional) -->
                    <div>
                        <label for="event_id" class="block text-sm font-medium text-gray-300 mb-2">Related Event (Optional)</label>
                        <select id="event_id"
                                name="event_id"
                                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition">
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
                                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition">
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
                                  placeholder="Please describe your question or issue in detail..."
                                  class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit"
                                class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-3 rounded-lg transition-colors font-medium shadow-md hover:shadow-lg">
                            Send Message
                        </button>
                    </div>
                </form>
            </div>

            <!-- Help Information -->
            <div class="bg-gray-800 rounded-xl p-8 border border-gray-700 shadow-lg">
                <h3 class="text-2xl font-semibold text-cyan-300 mb-6">How can we help?</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 gap-6 text-gray-300 text-sm">
                    <div class="p-4 bg-gray-700 rounded-lg hover:bg-gray-600 transition shadow-inner">
                        <h4 class="font-semibold text-white mb-2">📅 Event Questions</h4>
                        <p>Ask about event details, schedules, or venue information.</p>
                    </div>
                    <div class="p-4 bg-gray-700 rounded-lg hover:bg-gray-600 transition shadow-inner">
                        <h4 class="font-semibold text-white mb-2">🎫 Ticket Issues</h4>
                        <p>Problems with your tickets, cancellations, or refunds.</p>
                    </div>
                    <div class="p-4 bg-gray-700 rounded-lg hover:bg-gray-600 transition shadow-inner">
                        <h4 class="font-semibold text-white mb-2">💡 General Support</h4>
                        <p>Any other questions or technical issues you're experiencing.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
