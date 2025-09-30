<?php

namespace App\Http\Controllers;

use App\Services\TicketAvailabilityService;
use App\Services\TicketStrategies\AdvancedTicketStrategy;
use App\Services\TicketStrategies\SimpleTicketStrategy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    protected $ticketService;

    public function __construct(TicketAvailabilityService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    /**
     * Check ticket availability for an event
     */
    public function checkAvailability(Request $request, int $eventId)
    {
        $quantity = $request->get('quantity', 1);

        $available = $this->ticketService->isAvailable($eventId, $quantity);
        $availableCount = $this->ticketService->getAvailableCount($eventId);

        return response()->json([
            'available' => $available,
            'available_count' => $availableCount,
            'requested_quantity' => $quantity
        ]);
    }

    /**
     * Purchase tickets (simple validation)
     */
    public function purchase(Request $request)
    {
        $request->validate([
            'event_id' => 'required|integer|exists:events,id',
            'quantity' => 'required|integer|min:1|max:10'
        ]);

        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to purchase tickets'
            ], 401);
        }

        // Use advanced strategy for purchases
        $this->ticketService->setStrategy(new AdvancedTicketStrategy());

        $result = $this->ticketService->purchaseTickets(
            $request->event_id,
            Auth::id(),
            $request->quantity
        );

        return response()->json($result);
    }

    /**
     * Confirm ticket purchase
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|integer|exists:tickets,id'
        ]);

        $success = $this->ticketService->confirmPurchase($request->ticket_id);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Ticket confirmed!' : 'Confirmation failed'
        ]);
    }

    /**
     * Get real-time availability (for AJAX updates)
     */
    public function realTimeAvailability(int $eventId)
    {
        // Use simple strategy for quick checks
        $this->ticketService->setStrategy(new SimpleTicketStrategy());

        $availableCount = $this->ticketService->getAvailableCount($eventId);

        return response()->json([
            'event_id' => $eventId,
            'available_count' => $availableCount,
            'timestamp' => now()->toISOString()
        ]);
    }
}
