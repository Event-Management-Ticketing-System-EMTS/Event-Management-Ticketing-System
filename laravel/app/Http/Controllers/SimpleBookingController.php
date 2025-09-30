<?php

namespace App\Http\Controllers;

use App\Services\SimpleBookingService;
use Illuminate\Http\Request;

/**
 * Simple Booking Controller ⭐ BEGINNER FRIENDLY
 *
 * Handles viewing and managing ticket bookings.
 * Uses the same simple patterns as our other controllers.
 *
 * Pattern: Controller → Service → Model
 * - Controller: Handle requests, validate input, return responses
 * - Service: Business logic, data processing, caching
 * - Model: Database operations
 */
class SimpleBookingController extends Controller
{
    protected $bookingService;

    public function __construct(SimpleBookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Display all bookings with filters
     */
    public function index(Request $request)
    {
        // Get filters from request
        $filters = $request->only(['status', 'event_id', 'user_id', 'date_from', 'date_to']);

        // Remove empty filters
        $filters = array_filter($filters, function ($value) {
            return !is_null($value) && $value !== '';
        });

        // Get bookings with filters
        $bookings = $this->bookingService->getAllBookings($filters, 15);

        // Get filter options for dropdowns
        $filterOptions = $this->bookingService->getFilterOptions();

        // Get stats for dashboard cards
        $stats = $this->bookingService->getBookingStats();

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
            'success' => true,
            'bookings' => $bookings->items(),
            'total' => $bookings->total(),
            'current_page' => $bookings->currentPage(),
            'last_page' => $bookings->lastPage()
        ]);
    }

    /**
     * Get bookings for a specific user (AJAX)
     */
    public function getUserBookings(Request $request, $userId)
    {
        $bookings = $this->bookingService->getUserBookings($userId, 10);

        return response()->json([
            'success' => true,
            'bookings' => $bookings->items(),
            'total' => $bookings->total(),
            'current_page' => $bookings->currentPage(),
            'last_page' => $bookings->lastPage()
        ]);
    }

    /**
     * Export bookings to CSV (bonus feature)
     */
    public function export(Request $request)
    {
        $filters = $request->only(['status', 'event_id', 'user_id', 'date_from', 'date_to']);
        $filters = array_filter($filters, function ($value) {
            return !is_null($value) && $value !== '';
        });

        // Get all bookings without pagination for export
        $bookings = $this->bookingService->getAllBookings($filters, 999999);

        $filename = 'bookings_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

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
                'Venue'
            ]);

            // CSV data
            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->id,
                    $booking->event->title ?? 'N/A',
                    $booking->user->name ?? 'N/A',
                    $booking->user->email ?? 'N/A',
                    $booking->quantity,
                    $booking->total_price,
                    ucfirst($booking->status),
                    $booking->created_at->format('Y-m-d H:i:s'),
                    optional($booking->event)->event_date ?? 'N/A',
                    optional($booking->event)->venue ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
