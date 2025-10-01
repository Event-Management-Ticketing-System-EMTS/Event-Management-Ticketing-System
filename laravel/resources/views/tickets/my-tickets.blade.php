@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-900 text-white">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-cyan-400">My Tickets</h1>
            <a href="{{ route('user.dashboard') }}" class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-lg transition-colors">
                Back to Dashboard
            </a>
        </div>

        @if($tickets->count() > 0)
            <div class="grid gap-6">
                @foreach($tickets as $ticket)
                    <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold text-cyan-400 mb-2">{{ $ticket->event->title }}</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-gray-300">
                                    <div>
                                        <span class="text-gray-400">Event Date:</span>
                                        <p>{{ \Carbon\Carbon::parse($ticket->event->event_date)->format('M d, Y') }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-400">Venue:</span>
                                        <p>{{ $ticket->event->venue }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-400">Unit Price:</span>
                                        <p>${{ number_format($ticket->event->price, 2) }}</p>
                                    </div>
                                </div>

                                <div class="mt-4 flex items-center space-x-4">
                                    <div>
                                        <span class="text-gray-400">Quantity:</span>
                                        <span class="text-white font-semibold">{{ $ticket->quantity }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-400">Total Price:</span>
                                        <span class="text-cyan-400 font-semibold">${{ number_format($ticket->total_price, 2) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-400">Purchased:</span>
                                        <span class="text-white">{{ $ticket->purchase_date->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col items-end space-y-3">
                                <!-- Status Badge -->
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    @if($ticket->status === 'confirmed')
                                        bg-green-900 text-green-300
                                    @elseif($ticket->status === 'pending')
                                        bg-yellow-900 text-yellow-300
                                    @elseif($ticket->status === 'cancelled')
                                        bg-red-900 text-red-300
                                    @else
                                        bg-gray-700 text-gray-300
                                    @endif
                                ">
                                    {{ ucfirst($ticket->status) }}
                                </span>

                                <!-- Payment Status -->
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    @if($ticket->payment_status === 'paid')
                                        bg-green-900 text-green-300
                                    @elseif($ticket->payment_status === 'pending')
                                        bg-yellow-900 text-yellow-300
                                    @elseif($ticket->payment_status === 'failed')
                                        bg-red-900 text-red-300
                                    @else
                                        bg-gray-700 text-gray-300
                                    @endif
                                ">
                                    Payment: {{ ucfirst($ticket->payment_status) }}
                                </span>

                                <!-- Cancel Button -->
                                @if($ticket->status !== 'cancelled' && $ticket->event->event_date > now())
                                    <button
                                        onclick="cancelTicket({{ $ticket->id }})"
                                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors text-sm"
                                    >
                                        Cancel Ticket
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-16">
                <div class="text-gray-400 text-6xl mb-4">🎫</div>
                <h2 class="text-2xl font-semibold text-gray-300 mb-4">No Tickets Yet</h2>
                <p class="text-gray-400 mb-8">You haven't purchased any tickets yet. Browse events to get started!</p>
                <a href="{{ route('events.index') }}" class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-3 rounded-lg transition-colors">
                    Browse Events
                </a>
            </div>
        @endif
    </div>
</div>

<script>
function cancelTicket(ticketId) {
    if (!confirm('Are you sure you want to cancel this ticket?')) {
        return;
    }

    fetch(`/api/tickets/${ticketId}/cancel`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || 'Failed to cancel ticket');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while canceling the ticket');
    });
}
</script>
@endsection
