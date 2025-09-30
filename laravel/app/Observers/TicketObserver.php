<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Services\SimpleTicketService;
use App\Services\SimpleNotificationService;

/**
 * Simple Ticket Observer - Observer Pattern ⭐ BEGINNER FRIENDLY
 *
 * This automatically handles ticket events:
 * - Updates ticket availability (existing feature)
 * - Sends notifications to organizers (new feature!)
 *
 * Perfect example of Observer Pattern: "When something happens, automatically do multiple things"
 */
class TicketObserver
{
    protected $ticketService;
    protected $notificationService;

    public function __construct(
        SimpleTicketService $ticketService,
        SimpleNotificationService $notificationService
    ) {
        $this->ticketService = $ticketService;
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {
        // 1. Update availability (existing functionality)
        $this->ticketService->updateAvailability($ticket->event_id);

        // 2. Notify organizer about new purchase (new functionality!)
        if ($ticket->status === Ticket::STATUS_CONFIRMED) {
            $this->notificationService->notifyTicketPurchase($ticket);
        }
    }

    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        // 1. Update availability when ticket status changes
        $this->ticketService->updateAvailability($ticket->event_id);

        // 2. Check if ticket was cancelled and notify organizer
        if ($ticket->wasChanged('status') && $ticket->status === Ticket::STATUS_CANCELLED) {
            $this->notificationService->notifyTicketCancellation($ticket);
        }
    }

    /**
     * Handle the Ticket "deleted" event.
     */
    public function deleted(Ticket $ticket): void
    {
        // Update availability when ticket is deleted (hard delete/refund)
        $this->ticketService->updateAvailability($ticket->event_id);

        // Notify organizer about deletion (this counts as cancellation)
        $this->notificationService->notifyTicketCancellation($ticket);
    }
}
