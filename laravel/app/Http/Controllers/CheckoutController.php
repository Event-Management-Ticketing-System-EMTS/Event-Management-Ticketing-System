<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CheckoutController extends Controller
{
    public function show(Request $request, Event $event)
    {
        // Quantity from query (default 1)
        $qty = max(1, (int) $request->query('qty', 1));

        // Basic guards
        if ($event->status !== 'published') {
            return redirect()->route('events.index')->with('error', 'Event is not available.');
        }
        if (Carbon::parse($event->event_date)->isPast()) {
            return back()->with('error', 'This event has already taken place.');
        }

        // Determine available stock (supports both schemas)
        $available = isset($event->available_tickets)
            ? (int) $event->available_tickets
            : (int) (($event->total_tickets ?? 0) - ($event->tickets_sold ?? 0));

        if ($available < 1) {
            return back()->with('error', 'This event is sold out.');
        }
        if ($qty > $available) {
            return back()->with('error', 'Not enough tickets left.');
        }

        return view('checkout.show', [
            'event' => $event,
            'qty'   => $qty,
            'total' => $event->price * $qty,
        ]);
    }

    public function process(Request $request, Event $event)
    {
        $data = $request->validate([
            'payment_method' => 'required|in:card,wallet,cash',
            'qty'            => 'required|integer|min:1',
        ]);

        $qty = (int) $data['qty'];
        $ticketId = null;

        try {
            DB::transaction(function () use ($event, $qty, &$ticketId) {
                // Lock the event row to avoid race conditions
                /** @var \App\Models\Event $ev */
                $ev = Event::whereKey($event->getKey())->lockForUpdate()->first();

                if ($ev->status !== 'published') {
                    abort(422, 'Event is not available.');
                }
                if (Carbon::parse($ev->event_date)->isPast()) {
                    abort(422, 'This event has already taken place.');
                }

                // Compute availability (supports both schemas)
                $available = isset($ev->available_tickets)
                    ? (int) $ev->available_tickets
                    : (int) (($ev->total_tickets ?? 0) - ($ev->tickets_sold ?? 0));

                if ($available < $qty) {
                    abort(422, 'Not enough tickets left.');
                }

                // Create the ticket (same fields you used before)
                $ticket = Ticket::create([
                    'user_id'        => auth()->id(),
                    'event_id'       => $ev->id,
                    'quantity'       => $qty,
                    'total_price'    => $ev->price * $qty,
                    'status'         => 'confirmed',
                    'payment_status' => 'paid',
                    'purchase_date'  => now(),
                ]);
                $ticketId = $ticket->id;

                // Update stock
                // If event has an 'available_tickets' column -> decrement it
                // Else, if it has 'tickets_sold' -> increment it
                $attrs = $ev->getAttributes();
                if (array_key_exists('available_tickets', $attrs)) {
                    $ev->decrement('available_tickets', $qty);
                } elseif (array_key_exists('tickets_sold', $attrs)) {
                    $ev->increment('tickets_sold', $qty);
                }
            });
        } catch (\Throwable $e) {
            report($e);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'Payment failed. Please try again.',
                ], 422);
            }

            return back()->with('error', 'Payment failed. Please try again.');
        }

        // Success responses
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Payment successful. Tickets booked!',
                'ticketId' => $ticketId,
                'redirect' => route('tickets.my'), // adjust if your route name differs
            ]);
        }

        return redirect()
            ->route('tickets.my') // adjust if needed
            ->with('success', 'Payment successful. Tickets booked!');
    }
}
