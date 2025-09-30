<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\SimpleTicketService;
use Illuminate\Http\Request;

/**
 * Simple Ticket Controller
 * Handles ticket availability and purchases
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
            auth()->id()
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
}
