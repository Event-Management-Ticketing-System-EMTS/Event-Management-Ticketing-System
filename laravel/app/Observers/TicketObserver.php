<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Services\TicketAvailabilityService;

/**
 * Observer Pattern Implementation
 * Automatically updates event ticket availability when tickets are created/updated/deleted
 */
class TicketObserver
{
    protected $ticketService;

    public function __construct(TicketAvailabilityService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    /**
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {
        $this->ticketService->updateEventAvailability($ticket->event_id);
    }

    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        $this->ticketService->updateEventAvailability($ticket->event_id);
    }

    /**
     * Handle the Ticket "deleted" event.
     */
    public function deleted(Ticket $ticket): void
    {
        $this->ticketService->updateEventAvailability($ticket->event_id);
    }
}
