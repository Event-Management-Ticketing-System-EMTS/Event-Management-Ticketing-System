<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Services\SimpleTicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Simple Ticket Controller
 * Handles ticket availability, purchase, and cancellation
 */
class SimpleTicketController extends Controller
{
    protected $ticketService;

    public function __construct(SimpleTicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    /**
     * Get ticket availability for an event
     */
    public function getAvailability($eventId)
    {
        $availability = $this->ticketService->getAvailability($eventId);

        if (!$availability) {
            return response()->json(['error' => 'Event not found'], 404);
        }

        return response()->json($availability);
    }

    /**
     * Purchase tickets for an event
     */
    public function purchaseTickets(Request $request, $eventId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:10'
        ]);

        $success = $this->ticketService->purchaseTickets(
            $eventId,
            $request->quantity,
            Auth::id()
        );

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Tickets purchased successfully!',
                'availability' => $this->ticketService->getAvailability($eventId)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Not enough tickets available!'
        ], 400);
    }

    /**
     * Cancel a ticket (sets status to cancelled)
     */
    public function cancelTicket(Request $request, $ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found or not yours to cancel'
            ], 404);
        }

        if ($ticket->status === Ticket::STATUS_CANCELLED) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket is already cancelled'
            ], 400);
        }

        // ✅ Cancel ticket
        $ticket->update(['status' => Ticket::STATUS_CANCELLED]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket cancelled successfully. Organizer has been notified.',
            'availability' => $this->ticketService->getAvailability($ticket->event_id)
        ]);
    }

    /**
     * Show user's tickets
     */
    public function myTickets()
    {
        $tickets = Ticket::where('user_id', Auth::id())
            ->with('event')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('tickets.my-tickets', compact('tickets'));
    }
}
