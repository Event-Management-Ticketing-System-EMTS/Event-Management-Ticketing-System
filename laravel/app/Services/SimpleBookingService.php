<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

/**
 * Simple Booking Service ⭐ BEGINNER FRIENDLY
 *
 * Handles viewing and managing all ticket bookings in the system.
 * Uses the same simple approach as our other services.
 *
 * Why Service Pattern?
 * - ✅ Clean separation: Controller just handles requests, Service handles business logic
 * - ✅ Reusable: Multiple controllers can use this service
 * - ✅ Testable: Easy to test business logic separately
 * - ✅ Simple: One service, one responsibility
 */
class SimpleBookingService
{
    /**
     * Get all bookings with filters and pagination
     */
    public function getAllBookings($filters = [], $perPage = 15)
    {
        $query = Ticket::with(['event', 'user'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['event_id'])) {
            $query->where('event_id', $filters['event_id']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get booking statistics for dashboard
     */
    public function getBookingStats()
    {
        return Cache::remember('booking_stats', 300, function () { // Cache for 5 minutes
            return [
                'total_bookings' => Ticket::count(),
                'confirmed_bookings' => Ticket::where('status', 'confirmed')->count(),
                'cancelled_bookings' => Ticket::where('status', 'cancelled')->count(),
                'pending_bookings' => Ticket::where('status', 'pending')->count(),
                'total_revenue' => Ticket::where('status', 'confirmed')->sum('total_price'),
                'recent_bookings' => Ticket::with(['event', 'user'])
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get()
            ];
        });
    }

    /**
     * Get bookings for a specific event
     */
    public function getEventBookings($eventId, $perPage = 15)
    {
        return Ticket::with(['user'])
            ->where('event_id', $eventId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get bookings for a specific user
     */
    public function getUserBookings($userId, $perPage = 15)
    {
        return Ticket::with(['event'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get booking details by ID
     */
    public function getBookingDetails($ticketId)
    {
        return Ticket::with(['event', 'user'])->find($ticketId);
    }

    /**
     * Get available filter options
     */
    public function getFilterOptions()
    {
        return [
            'statuses' => [
                'all' => 'All Statuses',
                'confirmed' => 'Confirmed',
                'pending' => 'Pending',
                'cancelled' => 'Cancelled'
            ],
            'events' => Event::orderBy('title')->pluck('title', 'id')->toArray(),
            'recent_events' => Event::orderBy('created_at', 'desc')->take(10)->pluck('title', 'id')->toArray()
        ];
    }

    /**
     * Clear cache when bookings change
     */
    public function clearCache()
    {
        Cache::forget('booking_stats');
    }
}
