<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Ticket;
use App\Models\User;

/**
 * Simple Notification Service - Observer Pattern Helper
 *
 * This service creates notifications for important events.
 * It's called by the TicketObserver when tickets are cancelled.
 *
 * Perfect for beginners: Simple methods, clear purpose!
 */
class SimpleNotificationService
{
    /**
     * Notify organizer when their event's ticket is cancelled
     */
    public function notifyTicketCancellation(Ticket $ticket): void
    {
        // Get the event and its organizer
        $event = $ticket->event;
        $organizer = $event->organizer; // Assuming Event has organizer relationship
        $customer = $ticket->user;

        if (!$organizer) {
            return; // No organizer to notify
        }

        // Create a simple, clear notification
        Notification::create([
            'user_id' => $organizer->id,
            'title' => 'Ticket Cancelled',
            'message' => "Customer {$customer->name} cancelled {$ticket->quantity} ticket(s) for your event '{$event->title}'",
            'type' => Notification::TYPE_TICKET_CANCELLED,
            'is_read' => false,
            'data' => [
                'ticket_id' => $ticket->id,
                'event_id' => $event->id,
                'customer_name' => $customer->name,
                'quantity' => $ticket->quantity,
                'refund_amount' => $ticket->total_price
            ]
        ]);
    }

    /**
     * Notify organizer when someone buys their event tickets
     */
    public function notifyTicketPurchase(Ticket $ticket): void
    {
        $event = $ticket->event;
        $organizer = $event->organizer;
        $customer = $ticket->user;

        if (!$organizer) {
            return;
        }

        Notification::create([
            'user_id' => $organizer->id,
            'title' => 'New Ticket Purchase',
            'message' => "Great news! {$customer->name} just bought {$ticket->quantity} ticket(s) for your event '{$event->title}'",
            'type' => Notification::TYPE_TICKET_PURCHASED,
            'is_read' => false,
            'data' => [
                'ticket_id' => $ticket->id,
                'event_id' => $event->id,
                'customer_name' => $customer->name,
                'quantity' => $ticket->quantity,
                'revenue' => $ticket->total_price
            ]
        ]);
    }

    /**
     * Get unread notifications for a user
     */
    public function getUnreadNotifications(User $user)
    {
        return Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get all notifications for a user
     */
    public function getAllNotifications(User $user, int $limit = 20)
    {
        return Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId, User $user): bool
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $user->id)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            return true;
        }

        return false;
    }

    /**
     * Count unread notifications for a user
     */
    public function getUnreadCount(User $user): int
    {
        return Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
    }
}
