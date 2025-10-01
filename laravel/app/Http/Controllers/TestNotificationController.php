<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Simple Test Controller - For demonstrating the notification system
 *
 * This is just for testing - you can remove this later!
 */
class TestNotificationController extends Controller
{
    /**
     * Create a test ticket cancellation to see notifications in action
     */
    public function testCancellation()
    {
        // Find an existing ticket or create one for testing
        $ticket = Ticket::first();

        if (!$ticket) {
            return response()->json([
                'message' => 'No tickets found to test with. Create a ticket first!'
            ]);
        }

        // Change status to cancelled - this will trigger the Observer!
        $ticket->update(['status' => Ticket::STATUS_CANCELLED]);

        return response()->json([
            'message' => 'Test ticket cancelled! Check notifications to see if organizer was notified.',
            'ticket_id' => $ticket->id,
            'event_title' => $ticket->event->title ?? 'Unknown Event'
        ]);
    }

    /**
     * Show current notifications count for testing
     */
    public function showNotifications()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Please log in first']);
        }

        $notifications = $user->notifications()->limit(5)->get();
        $unreadCount = $user->unreadNotificationsCount();

        return response()->json([
            'user' => $user->name,
            'unread_count' => $unreadCount,
            'recent_notifications' => $notifications->map(function ($notification) {
                return [
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->diffForHumans()
                ];
            })
        ]);
    }
}
