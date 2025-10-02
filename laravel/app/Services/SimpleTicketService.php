<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Support\Facades\Cache;

class SimpleTicketService
{
    /**
     * Return cached availability for an event.
     * Structure:
     * [
     *   'total_tickets'         => int,
     *   'sold_quantity'         => int,   // paid & not-cancelled sum(quantity)
     *   'available_tickets'     => int,
     *   'availability_percent'  => float,
     *   'is_sold_out'           => bool,
     * ]
     */
    public function getAvailability(int $eventId): ?array
    {
        $cacheKey = "event_tickets_{$eventId}";

        return Cache::remember($cacheKey, 60, function () use ($eventId) {
            $event = Event::find($eventId);
            if (!$event) {
                return null;
            }

            // Sum *quantities* of paid + not-cancelled tickets
            $soldQty = (int) Ticket::where('event_id', $eventId)
                ->where('status', '!=', Ticket::STATUS_CANCELLED)
                ->where('payment_status', 'paid')
                ->sum('quantity');

            $total     = (int) $event->total_tickets;
            $available = max(0, $total - $soldQty);
            $percent   = $total > 0 ? round(($available / $total) * 100, 1) : 0.0;

            return [
                'total_tickets'        => $total,
                'sold_quantity'        => $soldQty,
                'available_tickets'    => $available,
                'availability_percent' => $percent,
                'is_sold_out'          => $available <= 0,
            ];
        });
    }

    /**
     * Create a single ticket row for this purchase.
     * Returns true on success, false if insufficient inventory.
     */
    public function purchaseTickets(int $eventId, int $quantity, int $userId): bool
    {
        $availability = $this->getAvailability($eventId);
        if (!$availability) {
            return false;
        }
        if ($quantity < 1) {
            return false;
        }
        if ($availability['available_tickets'] < $quantity) {
            return false; // not enough inventory
        }

        $event = Event::find($eventId);
        if (!$event) {
            return false;
        }

        // Calculate prices
        $unit  = (float) $event->price;
        $total = $unit * $quantity;

        // Create ONE ticket row with quantity
        Ticket::create([
            'event_id'         => $eventId,
            'user_id'          => $userId,
            'quantity'         => $quantity,
            'total_price'      => $total,
            'purchase_date'    => now(),
            'status'           => Ticket::STATUS_CONFIRMED, // align with observer
            'payment_status'   => 'paid',                   // mark as paid in this simple flow
            'payment_amount'   => $total,
            'paid_at'          => now(),
            'payment_reference'=> 'OFFLINE-' . strtoupper(str()->random(10)), // simple placeholder
        ]);

        // Optionally keep a snapshot on the Event (if you use tickets_sold there)
        $this->syncEventSold($eventId);

        // Bust cache
        $this->clearAvailabilityCache($eventId);

        return true;
    }

    /**
     * Observer will call this after create/update/delete to refresh counts and cache.
     */
    public function updateAvailability(int $eventId): void
    {
        $this->syncEventSold($eventId);
        $this->clearAvailabilityCache($eventId);
        // Prime cache (optional)
        $this->getAvailability($eventId);
    }

    /**
     * Public helper if you need to clear externally.
     */
    public function clearAvailabilityCache(int $eventId): void
    {
        Cache::forget("event_tickets_{$eventId}");
    }

    /**
     * Keep events.tickets_sold in sync with actual paid, non-cancelled quantities.
     * Safe even if you don’t always use this column, but nice for admin reports.
     */
    protected function syncEventSold(int $eventId): void
    {
        $event = Event::find($eventId);
        if (!$event) {
            return;
        }

        $soldQty = (int) Ticket::where('event_id', $eventId)
            ->where('status', '!=', Ticket::STATUS_CANCELLED)
            ->where('payment_status', 'paid')
            ->sum('quantity');

        // Save quietly to avoid recursion through observers
        $event->forceFill(['tickets_sold' => $soldQty])->saveQuietly();
    }
}
