<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Event;
use App\Models\Notification;

class UserDashboardController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        // Recent tickets (latest 6, exclude cancelled)
        $tickets = Ticket::with('event')
            ->where('user_id', $user->id)
            ->where('status', '!=', Ticket::STATUS_CANCELLED)
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn ($t) => [
                'id'     => $t->id,
                'code'   => $t->id, // or $t->code if you generate codes
                'status' => $t->payment_status,        // paid/pending/refunded
                'event'  => $t->event?->title ?? '—',
                'date'   => $t->created_at,
            ])
            ->all();

        // Upcoming events (paid + not cancelled + future event date)
        $upcoming = Ticket::with('event')
            ->where('user_id', $user->id)
            ->where('status', '!=', Ticket::STATUS_CANCELLED)
            ->where('payment_status', 'paid')
            ->whereHas('event', fn ($q) => $q->whereDate('event_date', '>=', now()))
            ->orderBy(
                Event::select('event_date')->whereColumn('events.id', 'tickets.event_id')
            )
            ->limit(5)
            ->get()
            ->map(fn ($t) => [
                'id'    => $t->event?->id,
                'title' => $t->event?->title,
                'date'  => $t->event?->event_date,
                'venue' => $t->event?->venue ?? 'TBA',
            ])
            ->all();

        // Stats for dashboard header (exclude cancelled tickets)
        $stats = [
            'upcoming'      => count($upcoming),
            'tickets'       => Ticket::where('user_id', $user->id)
                                     ->where('status', '!=', Ticket::STATUS_CANCELLED)
                                     ->count(),
            'spent'         => Ticket::where('user_id', $user->id)
                                     ->where('status', '!=', Ticket::STATUS_CANCELLED)
                                     ->where('payment_status', 'paid')
                                     ->sum('total_price'),
            'notifications' => Notification::where('user_id', $user->id)
                                           ->where('is_read', false)
                                           ->count(),
        ];

        // Short notifications list for the dashboard panel (latest 6)
        $alerts = Notification::where('user_id', $user->id)
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn ($n) => [
                'title'   => $n->title,
                'time'    => $n->created_at,
                'link'    => $n->data['link'] ?? null, // optional deep-link
                'is_read' => (bool) $n->is_read,
            ])
            ->all();

        return view('user.dashboard', compact('stats', 'upcoming', 'tickets', 'alerts'));
    }
}
