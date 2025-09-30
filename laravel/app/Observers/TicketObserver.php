<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Services\SimpleTicketService;

/**
 * Simple Ticket Observer - Observer Pattern
 *
 * This automatically updates ticket availability whenever:
 * - A new ticket is created (someone buys tickets)
 * - A ticket is updated (status changes)
 * - A ticket is deleted (refund/cancellation)
 */
class TicketObserver
{
    protected $ticketService;

    public function __construct(SimpleTicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    /**
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {
        // Automatically update availability when ticket is created
        $this->ticketService->updateAvailability($ticket->event_id);
    }

    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        // Update availability when ticket status changes
        $this->ticketService->updateAvailability($ticket->event_id);
    }

    /**
     * Handle the Ticket "deleted" event.
     */
    public function deleted(Ticket $ticket): void
    {
        // Update availability when ticket is deleted (refund)
        $this->ticketService->updateAvailability($ticket->event_id);
    }
}
