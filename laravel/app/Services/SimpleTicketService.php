<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Support\Facades\Cache;

/**
 * Simple Ticket Service - Uses Observer Pattern
 *
 * This service automatically updates ticket availability
 * whenever tickets are created, updated, or deleted.
 * It's triggered by the TicketObserver.
 */
class SimpleTicketService
{
    /**
     * Get current ticket availability for an event
     */
    public function getAvailability($eventId)
    {
        // Try to get from cache first (faster!)
        $cacheKey = "event_tickets_{$eventId}";

        return Cache::remember($cacheKey, 60, function () use ($eventId) {
            $event = Event::find($eventId);
            if (!$event) {
                return null;
            }

            $soldTickets = Ticket::where('event_id', $eventId)
                ->where('status', 'confirmed')
                ->count();

            $available = $event->capacity - $soldTickets;
            $percentage = $event->capacity > 0 ? ($available / $event->capacity) * 100 : 0;

            return [
                'total_capacity' => $event->capacity,
                'sold_tickets' => $soldTickets,
                'available_tickets' => max(0, $available),
                'availability_percentage' => round($percentage, 1),
                'is_sold_out' => $available <= 0
            ];
        });
    }

    /**
     * Purchase tickets for an event
     */
    public function purchaseTickets($eventId, $quantity, $userId)
    {
        $availability = $this->getAvailability($eventId);

        if (!$availability || $availability['available_tickets'] < $quantity) {
            return false; // Not enough tickets
        }

        // Create ticket records
        for ($i = 0; $i < $quantity; $i++) {
            Ticket::create([
                'event_id' => $eventId,
                'user_id' => $userId,
                'status' => 'confirmed',
                'purchase_date' => now()
            ]);
        }

        // Clear cache so next request gets fresh data
        $this->clearAvailabilityCache($eventId);

        return true;
    }

    /**
     * Update availability (called by Observer)
     */
    public function updateAvailability($eventId)
    {
        $this->clearAvailabilityCache($eventId);
        // Recalculate by calling getAvailability
        $this->getAvailability($eventId);
    }

    /**
     * Clear cached data for an event
     */
    private function clearAvailabilityCache($eventId)
    {
        Cache::forget("event_tickets_{$eventId}");
    }
}
