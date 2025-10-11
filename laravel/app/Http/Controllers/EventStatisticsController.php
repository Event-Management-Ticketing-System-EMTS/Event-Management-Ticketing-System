<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EventStatisticsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1) Try organizer scope
        $scopedQuery = Event::query()->where('organizer_id', $user->id);
        $scopedCount = (clone $scopedQuery)->count();

        // 2) If no rows for this organizer, FALL BACK to all events
        //    (remove this fallback if you only want per-organizer stats)
        $events = $scopedCount > 0
            ? $scopedQuery->get()
            : Event::query()->get();

        $totalEvents = $events->count();

        // --- Status counts (draft/published/cancelled) from events table ---
        $eventsByStatus = [
            'published' => $events->where('status', 'published')->count(),
            'draft'     => $events->where('status', 'draft')->count(),
            'cancelled' => $events->where('status', 'cancelled')->count(),
        ];

        // --- Approval counts (pending/approved/rejected) from events.approval_status ---
        $eventsByApproval = [
            'pending'  => $events->where('approval_status', 'pending')->count(),
            'approved' => $events->where('approval_status', 'approved')->count(),
            'rejected' => $events->where('approval_status', 'rejected')->count(),
        ];

        // --- Tickets / Revenue from events table ---
        $totalCapacity = (int) $events->sum('total_tickets');
        $ticketsSold   = (int) $events->sum('tickets_sold');

        // price is stored as string/decimal; cast to float for math
        $totalRevenue  = (float) $events->sum(function ($e) {
            $sold  = (int) ($e->tickets_sold ?? 0);
            $price = (float) ($e->price ?? 0);
            return $sold * $price;
        });

        $ticketsData = [
            'total'          => $totalCapacity,
            'sold'           => $ticketsSold,
            'available'      => max(0, $totalCapacity - $ticketsSold),
            'percentageSold' => $totalCapacity > 0 ? round(($ticketsSold / $totalCapacity) * 100) : 0,
        ];

        // --- Events by month (last 6 months) from created_at ---
        $sixMonthsAgo = Carbon::now()->subMonths(6);
        $byMonth = $events
            ->filter(fn ($e) => $e->created_at && $e->created_at >= $sixMonthsAgo)
            ->groupBy(fn ($e) => Carbon::parse($e->created_at)->format('Y-m'))
            ->sortKeys();

        $months = $byMonth->keys()->values()->all();
        $counts = $byMonth->map->count()->values()->all();

        // --- Upcoming events: published + future date (ignore approval to show data) ---
        $upcomingEvents = $events
            ->filter(function ($e) {
                if (!$e->event_date) return false;
                return ($e->status === 'published') &&
                       (Carbon::parse($e->event_date)->isToday() || Carbon::parse($e->event_date)->isFuture());
            })
            ->sortBy('event_date')
            ->take(5)
            ->values();

        // --- Top events by tickets_sold ---
        $topEvents = $events
            ->sortByDesc('tickets_sold')
            ->take(5)
            ->values()
            ->map(function ($e) {
                // for the Blade: sold and price included
                $e->sold  = (int) ($e->tickets_sold ?? 0);
                $e->price = (float) ($e->price ?? 0);
                return $e;
            });

        return view('events.statistics', [
            'totalEvents'      => $totalEvents,
            'eventsByStatus'   => $eventsByStatus,
            'eventsByApproval' => $eventsByApproval,
            'ticketsData'      => $ticketsData,
            'totalRevenue'     => $totalRevenue,
            'months'           => $months,
            'counts'           => $counts,
            'upcomingEvents'   => $upcomingEvents,
            'topEvents'        => $topEvents,
        ]);
    }
}
