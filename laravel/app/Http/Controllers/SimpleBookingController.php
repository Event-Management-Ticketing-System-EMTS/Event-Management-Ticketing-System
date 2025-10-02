<?php

namespace App\Http\Controllers;

use App\Services\SimpleBookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Event;
use App\Models\Ticket;

class SimpleBookingController extends Controller
{
    protected $bookingService;

    public function __construct(SimpleBookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * POST /events/{event}/purchase
     * Creates a paid ticket for the authenticated user and decrements inventory.
     */
    public function purchase(Request $request, Event $event)
{
    $data = $request->validate([
        'qty' => ['nullable','integer','min:1','max:10'],
    ]);
    $qty = (int)($data['qty'] ?? 1);

    // Guards
    if (strtolower($event->status) !== 'published') {
        return back()->with('error', 'This event is not available for purchase.');
    }
    if ($event->event_date && now()->isAfter($event->event_date)) {
        return back()->with('error', 'This event has already ended.');
    }

    try {
        DB::transaction(function () use ($event, $qty, $request) {
            // Lock the event row to avoid overselling
            $locked = Event::whereKey($event->id)->lockForUpdate()->first();

            $sold      = (int)($locked->tickets_sold ?? 0);
            $total     = (int)($locked->total_tickets ?? 0);
            $remaining = max($total - $sold, 0);

            if ($remaining < $qty) {
                abort(409, 'Not enough tickets available.');
            }

            // Calculate amounts based on your schema
            $unitPrice   = (float)($locked->price ?? 0);
            $totalPrice  = $unitPrice * $qty;

            // Create ONE ticket row with quantity (matches your model fillables)
            Ticket::create([
                'user_id'          => $request->user()->id,
                'event_id'         => $locked->id,
                'quantity'         => $qty,
                'total_price'      => $totalPrice,        // <-- IMPORTANT
                'purchase_date'    => now(),
                'status'           => Ticket::STATUS_CONFIRMED, // or 'confirmed'
                'payment_status'   => 'paid',             // replace with gateway callback later
                'payment_amount'   => $totalPrice,        // store what was charged
                'paid_at'          => now(),
                'payment_reference'=> null,               // set if you have a txn id
            ]);

            // Update counters
            $locked->tickets_sold = $sold + $qty;
            $locked->save();
        });
    } catch (\Throwable $e) {
        return back()->with('error', $e->getMessage());
    }

    return redirect()->route('tickets.my')->with('success', 'Ticket purchased successfully!');
}
    /**
     * Display all bookings with filters
     */
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'event_id', 'user_id', 'date_from', 'date_to']);
        $filters = array_filter($filters, fn ($v) => !is_null($v) && $v !== '');

        $bookings      = $this->bookingService->getAllBookings($filters, 15);
        $filterOptions = $this->bookingService->getFilterOptions();
        $stats         = $this->bookingService->getBookingStats();

        return view('bookings.index', compact('bookings', 'filterOptions', 'stats', 'filters'));
    }

    /**
     * Show booking details
     */
    public function show($id)
    {
        $booking = $this->bookingService->getBookingDetails($id);

        if (!$booking) {
            return redirect()->route('bookings.index')
                ->with('error', 'Booking not found');
        }

        return view('bookings.show', compact('booking'));
    }

    /**
     * Get bookings for a specific event (AJAX)
     */
    public function getEventBookings(Request $request, $eventId)
    {
        $bookings = $this->bookingService->getEventBookings($eventId, 10);

        return response()->json([
            'success'       => true,
            'bookings'      => $bookings->items(),
            'total'         => $bookings->total(),
            'current_page'  => $bookings->currentPage(),
            'last_page'     => $bookings->lastPage(),
        ]);
    }

    /**
     * Get bookings for a specific user (AJAX)
     */
    public function getUserBookings(Request $request, $userId)
    {
        $bookings = $this->bookingService->getUserBookings($userId, 10);

        return response()->json([
            'success'       => true,
            'bookings'      => $bookings->items(),
            'total'         => $bookings->total(),
            'current_page'  => $bookings->currentPage(),
            'last_page'     => $bookings->lastPage(),
        ]);
    }

    /**
     * Export bookings to CSV (bonus feature)
     */
    public function export(Request $request)
    {
        $filters = $request->only(['status', 'event_id', 'user_id', 'date_from', 'date_to']);
        $filters = array_filter($filters, fn ($v) => !is_null($v) && $v !== '');

        // Get all bookings without pagination for export
        $bookings = $this->bookingService->getAllBookings($filters, 999999);

        $filename = 'bookings_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        ];

        $callback = function () use ($bookings) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Booking ID',
                'Event Title',
                'Customer Name',
                'Customer Email',
                'Quantity',
                'Total Price',
                'Status',
                'Purchase Date',
                'Event Date',
                'Venue',
            ]);

            // CSV data
            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->id,
                    $booking->event->title ?? 'N/A',
                    $booking->user->name ?? 'N/A',
                    $booking->user->email ?? 'N/A',
                    $booking->quantity ?? 1,
                    $booking->total_price ?? $booking->amount ?? 0,
                    ucfirst($booking->status ?? $booking->payment_status ?? 'unknown'),
                    optional($booking->created_at)->format('Y-m-d H:i:s'),
                    optional($booking->event)->event_date ?? 'N/A',
                    optional($booking->event)->venue ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
