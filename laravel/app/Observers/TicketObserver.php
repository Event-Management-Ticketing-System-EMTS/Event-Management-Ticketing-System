<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Services\SimpleBookingService;
use App\Services\SimpleNotificationService;
use App\Services\SimpleTicketService;

/**
 * TicketObserver
 *
 * Keeps inventory in sync and sends notifications on ticket lifecycle
 * events (purchase, cancellation, refund). Also clears cached booking
 * views/statistics after relevant changes.
 */
class TicketObserver
{
    public function __construct(
        protected SimpleTicketService $ticketService,
        protected SimpleNotificationService $notificationService,
        protected SimpleBookingService $bookingService,
    ) {}

    /**
     * When a ticket is created:
     *  - refresh availability
     *  - if paid & confirmed on create, notify purchase
     *  - clear cached booking reports
     */
    public function created(Ticket $ticket): void
    {
        // keep availability fresh
        $this->ticketService->updateAvailability($ticket->event_id);

        // notify purchase only if the record is already confirmed & paid
        if (
            $ticket->status === Ticket::STATUS_CONFIRMED &&
            $ticket->payment_status === 'paid'
        ) {
            $this->notificationService->notifyTicketPurchase($ticket);
        }

        // clear cached aggregates
        $this->bookingService->clearCache();
    }

    /**
     * When a ticket is updated:
     *  - refresh availability on status/payment/quantity changes
     *  - notify cancellation
     *  - notify purchase when payment flips to "paid"
     *  - optionally notify refund (guarded if method not present)
     *  - clear cached booking reports
     */
    public function updated(Ticket $ticket): void
    {
        if (
            $ticket->wasChanged('status') ||
            $ticket->wasChanged('payment_status') ||
            $ticket->wasChanged('quantity')
        ) {
            $this->ticketService->updateAvailability($ticket->event_id);
        }

        // cancellation → notify organizer + buyer
        if ($ticket->wasChanged('status') && $ticket->status === Ticket::STATUS_CANCELLED) {
            $this->notificationService->notifyTicketCancellation($ticket);
        }

        // payment turned PAID → purchase confirmation
        if ($ticket->wasChanged('payment_status') && $ticket->payment_status === 'paid') {
            $this->notificationService->notifyTicketPurchase($ticket);
        }

        // payment turned REFUNDED → notify if your service supports it
        if ($ticket->wasChanged('payment_status') && $ticket->payment_status === 'refunded') {
            if (method_exists($this->notificationService, 'notifyTicketRefund')) {
                //$this->notificationService->notifyTicketRefund($ticket);
            }
        }

        $this->bookingService->clearCache();
    }

    /**
     * When a ticket is deleted:
     *  - refresh availability
     *  - treat as cancellation for notification purposes
     *  - clear cached booking reports
     */
    public function deleted(Ticket $ticket): void
    {
        $this->ticketService->updateAvailability($ticket->event_id);
        $this->notificationService->notifyTicketCancellation($ticket);
        $this->bookingService->clearCache();
    }
}
