<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Event: {{ $event->title }} - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900">
    <!-- Header -->
    <header class="bg-black/20 backdrop-blur-md border-b border-blue-500/20">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                        Review Event
                    </h1>
                    <span class="px-3 py-1 rounded-full text-sm font-medium
                        {{ $event->isPending() ? 'bg-yellow-500/20 text-yellow-300' : 
                           ($event->isApproved() ? 'bg-green-500/20 text-green-300' : 'bg-red-500/20 text-red-300') }}">
                        {{ ucfirst($event->approval_status) }}
                    </span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.approvals.index') }}" class="text-blue-300 hover:text-blue-200 transition-colors">
                        ← Back to Approvals
                    </a>
                    <span class="text-gray-300">{{ Auth::user()->name }}</span>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-4xl mx-auto px-6 py-8">
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-500/20 border border-green-500/30 rounded-lg text-green-300">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 rounded-lg text-red-300">
                {{ session('error') }}
            </div>
        @endif

        <!-- Event Details -->
        <div class="bg-black/30 backdrop-blur-sm border border-blue-500/20 rounded-xl p-6 mb-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Event Information -->
                <div>
                    <h2 class="text-2xl font-bold text-white mb-4">{{ $event->title }}</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm text-gray-400">Description</label>
                            <p class="text-gray-300">{{ $event->description }}</p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm text-gray-400">Event Date</label>
                                <p class="text-white font-medium">{{ $event->event_date->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <label class="text-sm text-gray-400">Start Time</label>
                                <p class="text-white font-medium">{{ $event->start_time->format('g:i A') }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="text-sm text-gray-400">Venue</label>
                            <p class="text-white font-medium">{{ $event->venue }}</p>
                            @if($event->address)
                                <p class="text-gray-400 text-sm">{{ $event->address }}</p>
                            @endif
                            @if($event->city)
                                <p class="text-gray-400 text-sm">{{ $event->city }}</p>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm text-gray-400">Total Tickets</label>
                                <p class="text-white font-medium">{{ number_format($event->total_tickets) }}</p>
                            </div>
                            <div>
                                <label class="text-sm text-gray-400">Ticket Price</label>
                                <p class="text-green-400 font-medium">${{ number_format($event->price, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Organizer Information -->
                <div>
                    <h3 class="text-lg font-semibold text-blue-300 mb-4">Organizer Details</h3>
                    <div class="bg-blue-500/10 rounded-lg p-4 space-y-3">
                        <div>
                            <label class="text-sm text-gray-400">Name</label>
                            <p class="text-white font-medium">{{ $event->organizer->name }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-400">Email</label>
                            <p class="text-blue-300">{{ $event->organizer->email }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-400">Role</label>
                            <span class="px-2 py-1 bg-purple-500/20 text-purple-300 rounded text-sm">
                                {{ ucfirst($event->organizer->role) }}
                            </span>
                        </div>
                        <div>
                            <label class="text-sm text-gray-400">Member Since</label>
                            <p class="text-gray-300">{{ $event->organizer->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <!-- Event Status -->
                    <div class="mt-6">
                        <h4 class="text-md font-semibold text-blue-300 mb-3">Event Status</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Current Status:</span>
                                <span class="text-white">{{ ucfirst($event->status) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Created:</span>
                                <span class="text-gray-300">{{ $event->created_at->diffForHumans() }}</span>
                            </div>
                            @if($event->reviewed_at)
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Reviewed:</span>
                                    <span class="text-gray-300">{{ $event->reviewed_at->diffForHumans() }}</span>
                                </div>
                                @if($event->reviewer)
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Reviewed By:</span>
                                        <span class="text-blue-300">{{ $event->reviewer->name }}</span>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Previous Admin Comments -->
        @if($event->admin_comments)
            <div class="bg-black/30 backdrop-blur-sm border border-gray-500/20 rounded-xl p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-300 mb-4">Previous Admin Comments</h3>
                <div class="bg-gray-500/10 rounded-lg p-4">
                    <p class="text-gray-300">{{ $event->admin_comments }}</p>
                </div>
            </div>
        @endif

        <!-- Approval Actions -->
        @if($event->isPending())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Approve Form -->
                <div class="bg-black/30 backdrop-blur-sm border border-green-500/20 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-green-300 mb-4">Approve Event</h3>
                    <form action="{{ route('admin.approvals.approve', $event) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="approve_comments" class="block text-sm text-gray-400 mb-2">
                                Comments (Optional)
                            </label>
                            <textarea 
                                name="comments" 
                                id="approve_comments"
                                rows="4" 
                                class="w-full bg-black/20 border border-gray-600 rounded-lg px-3 py-2 text-white placeholder-gray-500 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition-colors"
                                placeholder="Add approval comments or notes..."></textarea>
                        </div>
                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105">
                            ✓ Approve Event
                        </button>
                    </form>
                </div>

                <!-- Reject Form -->
                <div class="bg-black/30 backdrop-blur-sm border border-red-500/20 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-red-300 mb-4">Reject Event</h3>
                    <form action="{{ route('admin.approvals.reject', $event) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="reject_comments" class="block text-sm text-gray-400 mb-2">
                                Rejection Reason <span class="text-red-400">*</span>
                            </label>
                            <textarea 
                                name="comments" 
                                id="reject_comments"
                                rows="4" 
                                required
                                class="w-full bg-black/20 border border-gray-600 rounded-lg px-3 py-2 text-white placeholder-gray-500 focus:border-red-500 focus:ring-1 focus:ring-red-500 outline-none transition-colors"
                                placeholder="Please provide a clear reason for rejection..."></textarea>
                            @error('comments')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105">
                            ✗ Reject Event
                        </button>
                    </form>
                </div>
            </div>
        @else
            <!-- Event Already Reviewed -->
            <div class="bg-black/30 backdrop-blur-sm border border-gray-500/20 rounded-xl p-6 text-center">
                <div class="mb-4">
                    @if($event->isApproved())
                        <div class="w-16 h-16 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-green-300">Event Approved</h3>
                    @else
                        <div class="w-16 h-16 bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-red-300">Event Rejected</h3>
                    @endif
                </div>
                <p class="text-gray-400">This event has already been reviewed and cannot be modified.</p>
            </div>
        @endif
    </div>
</body>
</html>