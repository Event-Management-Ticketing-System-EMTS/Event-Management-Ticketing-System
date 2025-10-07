<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function show(Request $request, Event $event)
    {
        // qty comes from GET, defaults to 1
        $qty = max(1, (int) $request->query('qty', 1));

        // basic guards
        if ($event->status !== 'published') {
            return redirect()->route('events.index')->with('error', 'Event is not available.');
        }
        if (isset($event->available_tickets) && $qty > $event->available_tickets) {
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

        // Optional: re-check capacity atomically
        try {
            DB::transaction(function () use ($event, $qty) {

                // if you track available tickets, enforce here
                if (isset($event->available_tickets) && $qty > $event->available_tickets) {
                    abort(422, 'Not enough tickets left.');
                }

                // Create the ticket (same fields you use today)
                Ticket::create([
                    'user_id'         => auth()->id(),
                    'event_id'        => $event->id,
                    'quantity'        => $qty,
                    'total_price'     => $event->price * $qty,
                    'status'          => 'confirmed',
                    'payment_status'  => 'paid',
                    'purchase_date'   => now(),
                ]);

                // Decrement stock if you track it
                if (isset($event->available_tickets)) {
                    $event->decrement('available_tickets', $qty);
                }
            });
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Payment failed. Please try again.');
        }

        // “Simulate” success: we’d call gateway here; now we just redirect like before
        return redirect()->route('tickets.my')
            ->with('success', 'Payment successful. Tickets booked!');
    }
}
