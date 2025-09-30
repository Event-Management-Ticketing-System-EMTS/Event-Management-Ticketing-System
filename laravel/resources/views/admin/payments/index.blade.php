@extends('layouts.app')

@section('title', 'Payment Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Payment Management</h1>
        <div class="text-sm text-gray-600">
            Total Revenue: <span class="font-bold text-green-600">${{ number_format($stats['total_revenue'], 2) }}</span>
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Payment Statistics --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-blue-100 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['total_tickets'] }}</div>
            <div class="text-sm text-blue-800">Total Tickets</div>
        </div>

        <div class="bg-green-100 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $stats['paid_tickets'] }}</div>
            <div class="text-sm text-green-800">Paid</div>
        </div>

        <div class="bg-yellow-100 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending_payments'] }}</div>
            <div class="text-sm text-yellow-800">Pending</div>
        </div>

        <div class="bg-red-100 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-red-600">{{ $stats['failed_payments'] }}</div>
            <div class="text-sm text-red-800">Failed</div>
        </div>

        <div class="bg-gray-100 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-gray-600">{{ $stats['refunded_tickets'] }}</div>
            <div class="text-sm text-gray-800">Refunded</div>
        </div>
    </div>

    {{-- Pending Payments Section --}}
    @if($pendingPayments->count() > 0)
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="bg-yellow-500 text-white px-6 py-4 rounded-t-lg">
            <h2 class="text-xl font-semibold">Pending Payments ({{ $pendingPayments->count() }})</h2>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ticket ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pendingPayments as $ticket)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #{{ $ticket->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $ticket->user->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $ticket->event->title ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($ticket->total_price, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $ticket->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                {{-- Mark as Paid Form --}}
                                <form method="POST" action="{{ route('admin.payments.mark-paid', $ticket) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="payment_amount" value="{{ $ticket->total_price }}">
                                    <input type="hidden" name="payment_reference" value="Admin Manual Payment">
                                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-xs">
                                        Mark Paid
                                    </button>
                                </form>

                                {{-- Mark as Failed Form --}}
                                <form method="POST" action="{{ route('admin.payments.mark-failed', $ticket) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="reason" value="Payment failed - admin review">
                                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">
                                        Mark Failed
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Failed Payments Section --}}
    @if($failedPayments->count() > 0)
    <div class="bg-white rounded-lg shadow">
        <div class="bg-red-500 text-white px-6 py-4 rounded-t-lg">
            <h2 class="text-xl font-semibold">Failed Payments ({{ $failedPayments->count() }})</h2>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ticket ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Failed Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($failedPayments as $ticket)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #{{ $ticket->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $ticket->user->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $ticket->event->title ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($ticket->total_price, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $ticket->updated_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $ticket->payment_reference ?? 'No reason provided' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                {{-- Retry Payment Form --}}
                                <form method="POST" action="{{ route('admin.payments.retry', $ticket) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs">
                                        Reset to Pending
                                    </button>
                                </form>

                                {{-- Mark as Paid Form --}}
                                <form method="POST" action="{{ route('admin.payments.mark-paid', $ticket) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="payment_amount" value="{{ $ticket->total_price }}">
                                    <input type="hidden" name="payment_reference" value="Admin Manual Payment - Recovery">
                                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-xs">
                                        Mark Paid
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- No Payments Message --}}
    @if($pendingPayments->count() == 0 && $failedPayments->count() == 0)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm">
                    Great! No pending or failed payments need your attention right now.
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- Quick Actions --}}
    <div class="mt-8 text-center">
        <a href="{{ route('dashboard') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Dashboard
        </a>
    </div>
</div>
@endsection
