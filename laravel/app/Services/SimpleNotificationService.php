<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Ticket;
use App\Models\User;

class SimpleNotificationService
{
    /* ---------- Helpers ---------- */

    private function make(array $attrs): void
    {
        Notification::create($attrs);
    }

    private function baseData(Ticket $ticket): array
    {
        return [
            'ticket_id' => $ticket->id,
            'event_id'  => $ticket->event_id,
            'quantity'  => (int) $ticket->quantity,
            'total'     => (float) $ticket->total_price,
            'paid'      => (string) $ticket->payment_status,
        ];
    }

    /* ---------- Organizer notifications ---------- */

    public function notifyTicketPurchase(Ticket $ticket): void
    {
        $event     = $ticket->event;
        $organizer = $event?->organizer;
        $buyer     = $ticket->user;

        if (!$event || !$organizer || !$buyer) {
            return;
        }

        $this->make([
            'user_id' => $organizer->id,
            'title'   => 'New Ticket Purchase',
            'message' => "{$buyer->name} bought {$ticket->quantity} ticket(s) for '{$event->title}'.",
            'type'    => Notification::TYPE_TICKET_PURCHASED,
            'is_read' => false,
            'data'    => $this->baseData($ticket) + ['buyer' => $buyer->only('id', 'name', 'email')],
        ]);

        // also notify buyer (see below)
        $this->notifyBuyerPurchase($ticket);
    }

    public function notifyTicketCancellation(Ticket $ticket): void
    {
        $event     = $ticket->event;
        $organizer = $event?->organizer;
        $buyer     = $ticket->user;

        if (!$event || !$organizer || !$buyer) {
            return;
        }

        $this->make([
            'user_id' => $organizer->id,
            'title'   => 'Ticket Cancelled',
            'message' => "{$buyer->name} cancelled {$ticket->quantity} ticket(s) for '{$event->title}'.",
            'type'    => Notification::TYPE_TICKET_CANCELLED,
            'is_read' => false,
            'data'    => $this->baseData($ticket) + ['buyer' => $buyer->only('id', 'name', 'email')],
        ]);

        // also notify buyer (see below)
        $this->notifyBuyerCancellation($ticket);
    }

    /* ---------- Buyer notifications (new) ---------- */

    public function notifyBuyerPurchase(Ticket $ticket): void
    {
        $event = $ticket->event;
        $buyer = $ticket->user;

        if (!$event || !$buyer) return;

        $this->make([
            'user_id' => $buyer->id,
            'title'   => 'Purchase Confirmed',
            'message' => "You purchased {$ticket->quantity} ticket(s) for '{$event->title}'.",
            'type'    => Notification::TYPE_TICKET_PURCHASED,
            'is_read' => false,
            'data'    => $this->baseData($ticket) + [
                'event_title' => $event->title,
                'event_date'  => (string) $event->event_date,
                'venue'       => $event->venue,
            ],
        ]);
    }

    public function notifyBuyerCancellation(Ticket $ticket): void
    {
        $event = $ticket->event;
        $buyer = $ticket->user;

        if (!$event || !$buyer) return;

        $this->make([
            'user_id' => $buyer->id,
            'title'   => 'Ticket Cancelled',
            'message' => "You cancelled {$ticket->quantity} ticket(s) for '{$event->title}'.",
            'type'    => Notification::TYPE_TICKET_CANCELLED,
            'is_read' => false,
            'data'    => $this->baseData($ticket) + [
                'event_title' => $event->title,
                'event_date'  => (string) $event->event_date,
                'venue'       => $event->venue,
            ],
        ]);
    }

    /* ---------- Event approval notifications ---------- */

    public function notifyEventApproval(\App\Models\Event $event, ?string $comments = null): void
    {
        $organizer = $event->organizer;

        if (!$organizer) {
            return;
        }

        $message = "Your event '{$event->title}' has been approved and is now published!";
        if ($comments) {
            $message .= " Admin comments: {$comments}";
        }

        $this->make([
            'user_id' => $organizer->id,
            'title'   => 'Event Approved ✅',
            'message' => $message,
            'type'    => Notification::TYPE_EVENT_APPROVED,
            'is_read' => false,
            'data'    => [
                'event_id'    => $event->id,
                'event_title' => $event->title,
                'event_date'  => (string) $event->event_date,
                'venue'       => $event->venue,
                'admin_comments' => $comments,
            ],
        ]);
    }

    public function notifyEventRejection(\App\Models\Event $event, ?string $comments = null): void
    {
        $organizer = $event->organizer;

        if (!$organizer) {
            return;
        }

        $message = "Your event '{$event->title}' has been rejected.";
        if ($comments) {
            $message .= " Reason: {$comments}";
        }

        $this->make([
            'user_id' => $organizer->id,
            'title'   => 'Event Rejected ❌',
            'message' => $message,
            'type'    => Notification::TYPE_EVENT_REJECTED,
            'is_read' => false,
            'data'    => [
                'event_id'    => $event->id,
                'event_title' => $event->title,
                'event_date'  => (string) $event->event_date,
                'venue'       => $event->venue,
                'admin_comments' => $comments,
            ],
        ]);
    }

    /* ---------- Queries (unchanged) ---------- */

    public function getUnreadNotifications(User $user)
    {
        return Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->latest()
            ->get();
    }

    public function getAllNotifications(User $user, int $limit = 20)
    {
        return Notification::where('user_id', $user->id)
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function markAsRead(int $notificationId, User $user): bool
    {
        $n = Notification::where('id', $notificationId)->where('user_id', $user->id)->first();
        if (!$n) return false;
        $n->markAsRead();
        return true;
    }

    public function getUnreadCount(User $user): int
    {
        return Notification::where('user_id', $user->id)->where('is_read', false)->count();
    }
}
