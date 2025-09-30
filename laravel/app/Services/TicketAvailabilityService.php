<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Ticket;
use App\Services\TicketStrategies\TicketUpdateStrategyInterface;
use App\Services\TicketStrategies\SimpleTicketStrategy;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Service Layer Pattern with Strategy Pattern
 * Manages ticket availability with real-time updates
 */
class TicketAvailabilityService
{
    protected $strategy;

    public function __construct(TicketUpdateStrategyInterface $strategy = null)
    {
        // Default to simple strategy if none provided
        $this->strategy = $strategy ?? new SimpleTicketStrategy();
    }

    /**
     * Set the ticket update strategy (Strategy Pattern)
     */
    public function setStrategy(TicketUpdateStrategyInterface $strategy): void
    {
        $this->strategy = $strategy;
    }

    /**
     * Update event availability using current strategy
     */
    public function updateEventAvailability(int $eventId): bool
    {
        try {
            $event = Event::find($eventId);

            if (!$event) {
                Log::warning("Event not found: {$eventId}");
                return false;
            }

            // Use strategy to update availability
            $result = $this->strategy->updateAvailability($event);

            // Clear cache for real-time updates
            $this->clearAvailabilityCache($eventId);

            // Log the update for monitoring
            Log::info("Ticket availability updated for event {$eventId}");

            return $result;
        } catch (\Exception $e) {
            Log::error("Failed to update ticket availability: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if tickets are available (with caching for performance)
     */
    public function isAvailable(int $eventId, int $quantity = 1): bool
    {
        $cacheKey = "event_availability_{$eventId}_{$quantity}";

        return Cache::remember($cacheKey, 60, function () use ($eventId, $quantity) {
            $event = Event::find($eventId);

            if (!$event) {
                return false;
            }

            return $this->strategy->isAvailable($event, $quantity);
        });
    }

    /**
     * Get available ticket count with caching
     */
    public function getAvailableCount(int $eventId): int
    {
        $cacheKey = "event_available_count_{$eventId}";

        return Cache::remember($cacheKey, 30, function () use ($eventId) {
            $event = Event::find($eventId);

            if (!$event) {
                return 0;
            }

            return $this->strategy->getAvailableCount($event);
        });
    }

    /**
     * Purchase tickets with availability check
     */
    public function purchaseTickets(int $eventId, int $userId, int $quantity): array
    {
        try {
            // Check availability first
            if (!$this->isAvailable($eventId, $quantity)) {
                return [
                    'success' => false,
                    'message' => 'Not enough tickets available'
                ];
            }

            $event = Event::find($eventId);

            // Create pending ticket reservation
            $ticket = Ticket::create([
                'event_id' => $eventId,
                'user_id' => $userId,
                'quantity' => $quantity,
                'total_price' => $event->price * $quantity,
                'purchase_date' => now(),
                'status' => Ticket::STATUS_PENDING
            ]);

            // Observer pattern will automatically update availability

            return [
                'success' => true,
                'message' => 'Tickets reserved successfully',
                'ticket_id' => $ticket->id
            ];
        } catch (\Exception $e) {
            Log::error("Ticket purchase failed: " . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Purchase failed. Please try again.'
            ];
        }
    }

    /**
     * Confirm ticket purchase
     */
    public function confirmPurchase(int $ticketId): bool
    {
        try {
            $ticket = Ticket::find($ticketId);

            if (!$ticket || $ticket->status !== Ticket::STATUS_PENDING) {
                return false;
            }

            $ticket->update(['status' => Ticket::STATUS_CONFIRMED]);

            // Observer will automatically update availability

            return true;
        } catch (\Exception $e) {
            Log::error("Ticket confirmation failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear availability cache for real-time updates
     */
    private function clearAvailabilityCache(int $eventId): void
    {
        $patterns = [
            "event_availability_{$eventId}_*",
            "event_available_count_{$eventId}"
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
    }
}
