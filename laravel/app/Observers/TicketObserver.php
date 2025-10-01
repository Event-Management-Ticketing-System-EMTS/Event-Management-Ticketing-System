<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Services\SimpleTicketService;
use App\Services\SimpleNotificationService;
use App\Services\SimpleBookingService;

/**
 * Simple Ticket Observer - Observer Pattern ⭐ BEGINNER FRIENDLY
 *
 * This automatically handles ticket events:
 * - Updates ticket availability (existing feature)
 * - Sends notifications to organizers (notification feature!)
 * - Clears booking cache (new booking feature!)
 *
 * Perfect example of Observer Pattern: "When something happens, automatically do multiple things"
 */
class TicketObserver
{
    protected $ticketService;
    protected $notificationService;
    protected $bookingService;

    public function __construct(
        SimpleTicketService $ticketService,
        SimpleNotificationService $notificationService,
        SimpleBookingService $bookingService
    ) {
        $this->ticketService = $ticketService;
        $this->notificationService = $notificationService;
        $this->bookingService = $bookingService;
    }

    /**
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {
        // 1. Update availability (existing functionality)
        $this->ticketService->updateAvailability($ticket->event_id);

        // 2. Notify organizer about new purchase (notification functionality!)
        if ($ticket->status === Ticket::STATUS_CONFIRMED) {
            $this->notificationService->notifyTicketPurchase($ticket);
        }

        // 3. Clear booking cache (new booking functionality!)
        $this->bookingService->clearCache();
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

        // 3. Clear booking cache when any ticket changes
        $this->bookingService->clearCache();
    }

    /**
     * Handle the Ticket "deleted" event.
     */
    public function deleted(Ticket $ticket): void
    {
        // 1. Update availability when ticket is deleted (hard delete/refund)
        $this->ticketService->updateAvailability($ticket->event_id);

        // 2. Notify organizer about deletion (this counts as cancellation)
        $this->notificationService->notifyTicketCancellation($ticket);

        // 3. Clear booking cache when ticket is deleted
        $this->bookingService->clearCache();
    }
}
