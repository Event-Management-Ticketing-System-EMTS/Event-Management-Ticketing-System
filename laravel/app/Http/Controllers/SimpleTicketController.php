<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Services\SimpleTicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class SimpleTicketController extends Controller
{
    public function __construct(
        protected SimpleTicketService $ticketService
    ) {}

    /**
     * GET /api/events/{event}/availability
     */
    public function getAvailability($eventId)
    {
        $availability = $this->ticketService->getAvailability($eventId);

        if (!$availability) {
            return response()->json(['success' => false, 'message' => 'Event not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $availability]);
    }

    /**
     * POST /api/events/{event}/purchase
     * Creates a paid ticket for the authenticated user.
     */
    public function purchaseTickets(Request $request, $eventId)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        // 1) Ensure event exists and is purchasable
        /** @var Event|null $event */
        $event = Event::find($eventId);
        if (!$event) {
            return $this->jsonOrBack(false, 'Event not found', 404);
        }

        if (!in_array($event->status, ['published']) || $event->approval_status !== 'approved') {
            return $this->jsonOrBack(false, 'This event is not open for ticket sales.', 422);
        }

        if ($event->event_date->isPast()) {
            return $this->jsonOrBack(false, 'This event has already passed.', 422);
        }

        // 2) Check availability
        $availability = $this->ticketService->getAvailability($event->id);
        if (!$availability || ($availability['available'] ?? 0) < $data['quantity']) {
            return $this->jsonOrBack(false, 'Not enough tickets available.', 422, [
                'availability' => $availability,
            ]);
        }

        // 3) Delegate to service – should return the Ticket model or null on failure
        try {
            $ticket = $this->ticketService->purchaseTickets(
                eventId: $event->id,
                quantity: (int) $data['quantity'],
                userId: Auth::id()
            );

            if (!$ticket instanceof Ticket) {
                // service returned false/null
                return $this->jsonOrBack(false, 'Could not complete purchase. Please try again.', 500);
            }
        } catch (\Throwable $e) {
            return $this->jsonOrBack(false, 'Purchase failed: '.$e->getMessage(), 500);
        }

        // 4) Fresh availability after purchase
        $freshAvailability = $this->ticketService->getAvailability($event->id);

        return $this->jsonOrBack(true, 'Tickets purchased successfully!', 200, [
            'ticket_id'     => $ticket->id,
            'quantity'      => $ticket->quantity,
            'total_price'   => $ticket->total_price,
            'payment_status'=> $ticket->payment_status,
            'availability'  => $freshAvailability,
        ], route('tickets.my'));
    }

    /**
     * POST /api/tickets/{ticket}/cancel
     * Marks a ticket as cancelled for the current user.
     */
    public function cancelTicket(Request $request, $ticketId)
    {
        /** @var Ticket|null $ticket */
        $ticket = Ticket::with('event')
            ->where('id', $ticketId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found or not yours to cancel.'
            ], 404);
        }

        if ($ticket->status === Ticket::STATUS_CANCELLED) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket is already cancelled.'
            ], 422);
        }

        // Disallow cancelling past events
        if ($ticket->event && $ticket->event->event_date->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel a ticket for a past event.'
            ], 422);
        }

        // Cancel (Observer will handle availability + notifications)
        $ticket->update(['status' => Ticket::STATUS_CANCELLED]);

        $availability = $this->ticketService->getAvailability($ticket->event_id);

        return response()->json([
            'success' => true,
            'message' => 'Ticket cancelled successfully. Organizer has been notified.',
            'availability' => $availability,
        ]);
    }

    /**
     * GET /my-tickets
     */
    public function myTickets()
    {
        $tickets = Ticket::where('user_id', Auth::id())
            ->with('event')
            ->latest()
            ->get();

        return view('tickets.my-tickets', compact('tickets'));
    }

    /**
     * Small helper: return JSON for AJAX, or redirect back with flash for non-AJAX.
     */
    private function jsonOrBack(
        bool $success,
        string $message,
        int $status = 200,
        array $extra = [],
        ?string $redirectTo = null
    ) {
        if (request()->expectsJson() || request()->wantsJson() || request()->ajax()) {
            return response()->json(array_merge([
                'success' => $success,
                'message' => $message,
            ], $extra), $status);
        }

        // Fallback for form posts
        if ($success) {
            return redirect($redirectTo ?: url()->previous())->with('success', $message);
        }
        return redirect()->back()->withInput()->withErrors(['error' => $message]);
    }
}
