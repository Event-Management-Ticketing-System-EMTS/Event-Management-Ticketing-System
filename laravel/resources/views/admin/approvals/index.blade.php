<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Approvals - Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900">
    <!-- Header -->
    <header class="bg-black/20 backdrop-blur-md border-b border-blue-500/20">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                        Event Approvals
                    </h1>
                    <span class="px-3 py-1 bg-yellow-500/20 text-yellow-300 rounded-full text-sm font-medium">
                        Admin Dashboard
                    </span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="text-blue-300 hover:text-blue-200 transition-colors">
                        ← Back to Dashboard
                    </a>
                    <span class="text-gray-300">{{ Auth::user()->name }}</span>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-6 py-8">
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

        <!-- Approval Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-black/30 backdrop-blur-sm border border-blue-500/20 rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">Pending Approval</p>
                        <p class="text-3xl font-bold text-yellow-400">{{ $stats['pending'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-black/30 backdrop-blur-sm border border-green-500/20 rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">Approved</p>
                        <p class="text-3xl font-bold text-green-400">{{ $stats['approved'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-black/30 backdrop-blur-sm border border-red-500/20 rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">Rejected</p>
                        <p class="text-3xl font-bold text-red-400">{{ $stats['rejected'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-black/30 backdrop-blur-sm border border-blue-500/20 rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">Total Events</p>
                        <p class="text-3xl font-bold text-blue-400">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Events List -->
        <div class="bg-black/30 backdrop-blur-sm border border-blue-500/20 rounded-xl p-6">
            <h2 class="text-xl font-semibold text-blue-300 mb-6">Pending Events for Approval</h2>

            @if($pendingEvents->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-700">
                                <th class="text-left py-3 px-4 text-gray-300">Event</th>
                                <th class="text-left py-3 px-4 text-gray-300">Organizer</th>
                                <th class="text-left py-3 px-4 text-gray-300">Date</th>
                                <th class="text-left py-3 px-4 text-gray-300">Tickets</th>
                                <th class="text-left py-3 px-4 text-gray-300">Price</th>
                                <th class="text-left py-3 px-4 text-gray-300">Created</th>
                                <th class="text-left py-3 px-4 text-gray-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingEvents as $event)
                                <tr class="border-b border-gray-800 hover:bg-blue-500/5 transition-colors">
                                    <td class="py-4 px-4">
                                        <div>
                                            <h3 class="font-semibold text-white">{{ $event->title }}</h3>
                                            <p class="text-sm text-gray-400 truncate max-w-xs">{{ $event->description }}</p>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="text-blue-300">{{ $event->organizer->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $event->organizer->email }}</div>
                                    </td>
                                    <td class="py-4 px-4 text-gray-300">
                                        {{ $event->event_date->format('M d, Y') }}
                                    </td>
                                    <td class="py-4 px-4 text-gray-300">
                                        {{ number_format($event->total_tickets) }}
                                    </td>
                                    <td class="py-4 px-4 text-green-400">
                                        ${{ number_format($event->price, 2) }}
                                    </td>
                                    <td class="py-4 px-4 text-gray-400 text-sm">
                                        {{ $event->created_at->diffForHumans() }}
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.approvals.show', $event) }}" 
                                               class="px-3 py-1 bg-blue-500/20 text-blue-300 rounded hover:bg-blue-500/30 transition-colors text-sm">
                                                Review
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $pendingEvents->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-300 mb-2">No Pending Approvals</h3>
                    <p class="text-gray-500">All events have been reviewed. Great job!</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>