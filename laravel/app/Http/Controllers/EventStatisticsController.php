<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EventStatisticsController extends Controller
{
    /**
     * Display event statistics for the organizer
     */
    public function index()
    {
        // Get current authenticated user
        $user = Auth::user();

        // Get all events organized by the current user
        $events = Event::where('organizer_id', $user->id)->get();

        // Count events by status
        $eventsByStatus = [
            'published' => $events->where('status', 'published')->count(),
            'draft' => $events->where('status', 'draft')->count(),
            'cancelled' => $events->where('status', 'cancelled')->count(),
        ];

        // Calculate ticket statistics
        $totalTickets = $events->sum('total_tickets');
        $soldTickets = $events->sum('tickets_sold');
        $availableTickets = $totalTickets - $soldTickets;
        $ticketsData = [
            'total' => $totalTickets,
            'sold' => $soldTickets,
            'available' => $availableTickets,
            'percentageSold' => $totalTickets > 0 ? round(($soldTickets / $totalTickets) * 100) : 0
        ];

        // Calculate revenue
        $totalRevenue = $events->sum(function ($event) {
            return $event->tickets_sold * $event->price;
        });

        // Events by month (for the last 6 months)
        $sixMonthsAgo = Carbon::now()->subMonths(6);
        $eventsByMonth = DB::table('events')
            ->select(DB::raw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count'))
            ->where('organizer_id', $user->id)
            ->where('created_at', '>=', $sixMonthsAgo)
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Format data for chart
        $months = [];
        $counts = [];
        foreach ($eventsByMonth as $eventMonth) {
            $date = Carbon::createFromDate($eventMonth->year, $eventMonth->month, 1);
            $months[] = $date->format('M Y');
            $counts[] = $eventMonth->count;
        }

        // Get upcoming events
        $upcomingEvents = Event::where('organizer_id', $user->id)
            ->where('event_date', '>=', Carbon::today())
            ->where('status', 'published')
            ->orderBy('event_date', 'asc')
            ->take(5)
            ->get();

        // Best performing events (most tickets sold)
        $topEvents = Event::where('organizer_id', $user->id)
            ->where('status', 'published')
            ->orderBy('tickets_sold', 'desc')
            ->take(5)
            ->get();

        return view('events.statistics', compact(
            'events',
            'eventsByStatus',
            'ticketsData',
            'totalRevenue',
            'months',
            'counts',
            'upcomingEvents',
            'topEvents'
        ));
    }
}
