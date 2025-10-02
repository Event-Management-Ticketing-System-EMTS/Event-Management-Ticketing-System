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

        // Recent tickets (latest 6)
        $tickets = Ticket::with('event')
            ->where('user_id', $user->id)
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn ($t) => [
                'id'    => $t->id,
                'code'  => $t->id, // or $t->code if you store codes
                'status'=> $t->payment_status,                    // paid/pending/refunded
                'event' => $t->event?->title ?? '—',
                'date'  => $t->created_at,
            ])
            ->all();

        // Upcoming events for THIS user (only paid tickets; future events)
        $upcoming = Ticket::with('event')
            ->where('user_id', $user->id)
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
                'date'  => $t->event?->event_date,  // keep as Carbon/date
                'venue' => $t->event?->venue ?? 'TBA',
            ])
            ->all();

        // Stats for header cards
        $stats = [
            'upcoming'      => count($upcoming),
            'tickets'       => Ticket::where('user_id', $user->id)->count(),
            'spent'         => Ticket::where('user_id', $user->id)
                                     ->where('payment_status', 'paid')
                                     ->sum('total_price'), // matches your schema
            'notifications' => Notification::where('user_id', $user->id)
                                           ->where('is_read', false) // your model uses is_read
                                           ->count(),
        ];

        return view('user.dashboard', compact('stats', 'upcoming', 'tickets'));
    }
}
