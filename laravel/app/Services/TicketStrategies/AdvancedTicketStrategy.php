<?php

namespace App\Services\TicketStrategies;

use App\Models\Event;
use App\Models\Ticket;

/**
 * Advanced Strategy Implementation
 * Includes pending tickets and buffer management
 */
class AdvancedTicketStrategy implements TicketUpdateStrategyInterface
{
    private const BUFFER_PERCENTAGE = 0.05; // 5% buffer for high-demand events

    /**
     * Update availability considering pending and confirmed tickets
     */
    public function updateAvailability(Event $event): bool
    {
        // Count confirmed tickets
        $confirmedTickets = Ticket::where('event_id', $event->id)
            ->where('status', Ticket::STATUS_CONFIRMED)
            ->sum('quantity');

        // Count pending tickets (hold for 15 minutes)
        $pendingTickets = Ticket::where('event_id', $event->id)
            ->where('status', Ticket::STATUS_PENDING)
            ->where('created_at', '>=', now()->subMinutes(15))
            ->sum('quantity');

        // Total sold including pending
        $totalSold = $confirmedTickets + $pendingTickets;

        // Update the event
        $event->update(['tickets_sold' => $confirmedTickets]);

        return true;
    }

    /**
     * Check availability with pending tickets consideration
     */
    public function isAvailable(Event $event, int $requestedQuantity): bool
    {
        $availableCount = $this->getAvailableCount($event);

        // Apply buffer for high-demand events (>80% sold)
        if ($event->tickets_sold / $event->total_tickets > 0.8) {
            $buffer = (int) ($event->total_tickets * self::BUFFER_PERCENTAGE);
            $availableCount -= $buffer;
        }

        return $availableCount >= $requestedQuantity;
    }

    /**
     * Get available count considering pending tickets
     */
    public function getAvailableCount(Event $event): int
    {
        $pendingTickets = Ticket::where('event_id', $event->id)
            ->where('status', Ticket::STATUS_PENDING)
            ->where('created_at', '>=', now()->subMinutes(15))
            ->sum('quantity');

        return max(0, $event->total_tickets - $event->tickets_sold - $pendingTickets);
    }
}
