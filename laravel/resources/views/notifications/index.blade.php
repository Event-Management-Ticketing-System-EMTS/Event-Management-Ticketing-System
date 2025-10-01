{{-- Simple Notifications Page for Organizers --}}
@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h1 class="text-2xl font-bold text-gray-900">
                📬 Your Notifications
                @if($unreadCount > 0)
                    <span class="ml-2 bg-red-500 text-white text-sm px-2 py-1 rounded-full">
                        {{ $unreadCount }} new
                    </span>
                @endif
            </h1>
            <p class="text-gray-600 mt-1">Stay updated with your event activities</p>
        </div>

        <div class="p-6">
            @if($notifications->count() > 0)
                <div class="space-y-4">
                    @foreach($notifications as $notification)
                        <div class="border rounded-lg p-4 transition-colors duration-200
                                    {{ $notification->isUnread() ? 'bg-blue-50 border-blue-200' : 'bg-gray-50 border-gray-200' }}">

                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    {{-- Notification Header --}}
                                    <div class="flex items-center gap-3">
                                        {{-- Icon based on type --}}
                                        @if($notification->type === 'ticket_cancelled')
                                            <span class="text-red-500 text-xl">❌</span>
                                        @elseif($notification->type === 'ticket_purchased')
                                            <span class="text-green-500 text-xl">🎟️</span>
                                        @else
                                            <span class="text-blue-500 text-xl">📢</span>
                                        @endif

                                        <div>
                                            <h3 class="font-semibold text-gray-900">{{ $notification->title }}</h3>
                                            <p class="text-sm text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>

                                        {{-- Unread indicator --}}
                                        @if($notification->isUnread())
                                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                        @endif
                                    </div>

                                    {{-- Notification Message --}}
                                    <p class="mt-3 text-gray-700">{{ $notification->message }}</p>

                                    {{-- Extra Details --}}
                                    @if($notification->data)
                                        <div class="mt-3 text-sm text-gray-600 bg-white/50 p-3 rounded border">
                                            @if($notification->type === 'ticket_cancelled')
                                                <div class="grid grid-cols-2 gap-4">
                                                    <div><strong>Customer:</strong> {{ $notification->data['customer_name'] ?? 'N/A' }}</div>
                                                    <div><strong>Quantity:</strong> {{ $notification->data['quantity'] ?? 'N/A' }} tickets</div>
                                                    <div><strong>Refund Amount:</strong> ${{ $notification->data['refund_amount'] ?? '0.00' }}</div>
                                                </div>
                                            @elseif($notification->type === 'ticket_purchased')
                                                <div class="grid grid-cols-2 gap-4">
                                                    <div><strong>Customer:</strong> {{ $notification->data['customer_name'] ?? 'N/A' }}</div>
                                                    <div><strong>Quantity:</strong> {{ $notification->data['quantity'] ?? 'N/A' }} tickets</div>
                                                    <div><strong>Revenue:</strong> ${{ $notification->data['revenue'] ?? '0.00' }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                {{-- Mark as Read Button --}}
                                @if($notification->isUnread())
                                    <button onclick="markAsRead({{ $notification->id }})"
                                            class="ml-4 text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Mark as Read
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- Empty State --}}
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">📭</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications yet</h3>
                    <p class="text-gray-500">You'll see notifications here when customers interact with your events.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Simple JavaScript to mark notifications as read
async function markAsRead(notificationId) {
    try {
        const response = await fetch(`/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const result = await response.json();

        if (result.success) {
            // Reload page to show updated status
            window.location.reload();
        } else {
            alert('Failed to mark notification as read');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to mark notification as read');
    }
}
</script>
@endsection
