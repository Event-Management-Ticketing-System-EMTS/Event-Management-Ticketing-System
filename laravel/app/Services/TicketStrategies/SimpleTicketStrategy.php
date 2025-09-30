<?php

namespace App\Services\TicketStrategies;

use App\Models\Event;
use App\Models\Ticket;

/**
 * Simple Strategy Implementation
 * Basic ticket availability calculation
 */
class SimpleTicketStrategy implements TicketUpdateStrategyInterface
{
    /**
     * Update event ticket availability by counting confirmed tickets
     */
    public function updateAvailability(Event $event): bool
    {
        // Count confirmed tickets for this event
        $soldTickets = Ticket::where('event_id', $event->id)
            ->where('status', Ticket::STATUS_CONFIRMED)
            ->sum('quantity');

        // Update the event's tickets_sold count
        $event->update(['tickets_sold' => $soldTickets]);

        return true;
    }

    /**
     * Check if requested quantity is available
     */
    public function isAvailable(Event $event, int $requestedQuantity): bool
    {
        return $this->getAvailableCount($event) >= $requestedQuantity;
    }

    /**
     * Get available ticket count
     */
    public function getAvailableCount(Event $event): int
    {
        return max(0, $event->total_tickets - $event->tickets_sold);
    }
}
