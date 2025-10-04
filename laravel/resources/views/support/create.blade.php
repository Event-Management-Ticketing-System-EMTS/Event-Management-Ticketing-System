@extends('layouts.app')

@section('content')
{{-- BACKGROUND: Added subtle gradient for depth and replaced single color background --}}
<div class="min-h-screen bg-gradient-to-br from-slate-950 via-gray-900 to-slate-950 text-white py-12 antialiased">
    <div class="container mx-auto px-4 max-w-6xl">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12 border-b border-cyan-400/20 pb-4">
            <h1 class="text-5xl font-extrabold text-white mb-4 md:mb-0 tracking-tight">
                Get <span class="text-cyan-400">Support</span> 🛠️
            </h1>
            <a href="{{ route('user.dashboard') }}"
               class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-700 px-5 py-2.5 rounded-full text-sm font-semibold transition-all shadow-lg border border-cyan-400/30 transform hover:scale-[1.02]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                Dashboard
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-600/20 border-l-4 border-green-400 text-green-300 p-4 rounded-xl mb-10 shadow-lg animate-fadeIn">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

            <div class="lg:col-span-2 bg-slate-900 rounded-3xl p-8 border border-cyan-400/30 shadow-2xl transition-all duration-500 hover:border-cyan-400/50">
                <h2 class="text-3xl font-bold text-cyan-300 mb-8">Send a New Request</h2>

                <form action="{{ route('support.store') }}" method="POST" class="space-y-8">
                    @csrf

                    @php
                        // Define common classes for form elements
                        $label_classes = "block text-sm font-semibold uppercase text-cyan-400 mb-2 tracking-wider";
                        $input_classes = "w-full px-5 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all placeholder-slate-500";
                        $error_classes = "text-red-400 text-sm mt-2 font-medium";
                    @endphp

                    <div>
                        <label for="subject" class="{{ $label_classes }}">Subject</label>
                        <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required
                               placeholder="What can we help you with? (e.g., Refund request for Event X)"
                               class="{{ $input_classes }}">
                        @error('subject')
                            <p class="{{ $error_classes }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        <div>
                            <label for="event_id" class="{{ $label_classes }}">Related Event (Optional)</label>
                            {{-- Increased py padding for better aesthetics --}}
                            <select id="event_id" name="event_id"
                                    class="{{ $input_classes }}">
                                <option value="">No specific event</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>
                                        {{ $event->title }} - {{ $event->event_date->format('M d, Y') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('event_id')
                                <p class="{{ $error_classes }}">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="priority" class="{{ $label_classes }}">Priority</label>
                            {{-- Increased py padding for better aesthetics --}}
                            <select id="priority" name="priority" required
                                    class="{{ $input_classes }}">
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>🟢 Low - General question</option>
                                <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>🟡 Medium - Need assistance</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>🔴 High - Urgent issue</option>
                            </select>
                            @error('priority')
                                <p class="{{ $error_classes }}">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="message" class="{{ $label_classes }}">Detailed Message</label>
                        <textarea id="message" name="message" rows="7" required
                                  placeholder="Please describe your question or issue in detail. The more information you provide, the faster we can help."
                                  class="{{ $input_classes }}">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="{{ $error_classes }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit"
                                class="bg-gradient-to-r from-cyan-500 to-sky-500 hover:from-cyan-400 hover:to-sky-400 text-white px-8 py-3 rounded-full transition-all font-bold shadow-lg shadow-cyan-500/30 hover:shadow-xl transform hover:-translate-y-1">
                            🚀 Submit Request
                        </button>
                    </div>
                </form>
            </div>

            <div class="lg:col-span-1 bg-slate-900 rounded-3xl p-8 border border-slate-700 shadow-2xl space-y-6">
                <h3 class="text-3xl font-bold text-white mb-6">Quick Links</h3>
                
                {{-- Card 1: Event Questions --}}
                <div class="p-5 bg-slate-800 rounded-2xl border-l-4 border-cyan-500 hover:bg-slate-700 transition transform hover:scale-[1.03] shadow-lg cursor-pointer">
                    <h4 class="font-extrabold text-white text-lg mb-1 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-cyan-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" /></svg>
                        Event Details
                    </h4>
                    <p class="text-slate-400 text-sm">Questions about event schedules, venue access, or performers.</p>
                </div>
                
                {{-- Card 2: Ticket Issues --}}
                <div class="p-5 bg-slate-800 rounded-2xl border-l-4 border-yellow-500 hover:bg-slate-700 transition transform hover:scale-[1.03] shadow-lg cursor-pointer">
                    <h4 class="font-extrabold text-white text-lg mb-1 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-3.707-9.293a1 1 0 00-1.414 1.414L8 12.586l7.293-7.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293z" clip-rule="evenodd" /></svg>
                        Orders & Refunds
                    </h4>
                    <p class="text-slate-400 text-sm">Issues with purchasing, ticket retrieval, or refund status updates.</p>
                </div>
                
                {{-- Card 3: General Support --}}
                <div class="p-5 bg-slate-800 rounded-2xl border-l-4 border-pink-500 hover:bg-slate-700 transition transform hover:scale-[1.03] shadow-lg cursor-pointer">
                    <h4 class="font-extrabold text-white text-lg mb-1 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-pink-400" viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L14 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>
                        Technical & Account
                    </h4>
                    <p class="text-slate-400 text-sm">Report a bug, account login problems, or feedback on our platform.</p>
                </div>

                {{-- Added contact note --}}
                <div class="pt-4 border-t border-slate-700 text-center">
                    <p class="text-xs text-slate-500 font-medium">We aim to respond to all **High** priority requests within 4 hours.</p>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection