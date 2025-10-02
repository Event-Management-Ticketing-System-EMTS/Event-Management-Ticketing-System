<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Repositories\EventRepository;
use App\Services\SortingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    protected $eventRepository;
    protected $sortingService;

    public function __construct(EventRepository $eventRepository, SortingService $sortingService)
    {
        $this->eventRepository = $eventRepository;
        $this->sortingService  = $sortingService;
    }

    /**
     * Display a listing of the resource (public browse)
     * Supports sorting + filtering via query params:
     * - q (keyword in title/description/venue/city)
     * - city
     * - status (published|draft|cancelled)  // mostly for admin views, but kept flexible
     * - date_from (YYYY-MM-DD)
     * - date_to   (YYYY-MM-DD)
     * - price_min (number)
     * - price_max (number)
     * - sort, direction (validated by SortingService)
     */
    public function index(Request $request)
    {
        // 1) Sorting (validated)
        $sortParams = $this->sortingService->validateEventSortParameters(
            $request->get('sort'),
            $request->get('direction')
        );

        // 2) Filters (read from query string)
        $q        = trim((string) $request->get('q', ''));
        $city     = $request->get('city');
        $status   = $request->get('status');     // optional — if omitted, we default to published+approved
        $from     = $request->get('date_from');
        $to       = $request->get('date_to');
        $minPrice = $request->get('price_min');
        $maxPrice = $request->get('price_max');

        // 3) Base query: public should only see published + approved
        $query = Event::query()
            ->when(empty($status), function ($q) {
                $q->where('status', 'published')
                  ->where('approval_status', 'approved');
            }, function ($q) use ($status) {
                // If status filter is explicitly given, respect it (useful for admin screens)
                $q->where('status', $status);
            });

        // 4) Apply filters
        $query
            ->when($q, function ($qb) use ($q) {
                $qb->where(function ($inner) use ($q) {
                    $inner->where('title', 'like', "%{$q}%")
                          ->orWhere('description', 'like', "%{$q}%")
                          ->orWhere('venue', 'like', "%{$q}%")
                          ->orWhere('city', 'like', "%{$q}%");
                });
            })
            ->when($city, fn ($qb) => $qb->where('city', $city))
            ->when($from, fn ($qb) => $qb->whereDate('event_date', '>=', $from))
            ->when($to, fn ($qb)   => $qb->whereDate('event_date', '<=', $to))
            ->when(strlen((string)$minPrice) > 0, fn ($qb) => $qb->where('price', '>=', (float) $minPrice))
            ->when(strlen((string)$maxPrice) > 0, fn ($qb) => $qb->where('price', '<=', (float) $maxPrice));

        // 5) Sorting + pagination
        $events = $query
            ->orderBy($sortParams['sort_by'], $sortParams['direction'])
            ->paginate(9)
            ->withQueryString();

        // 6) Options for filters (dropdowns)
        $cities   = Event::select('city')->whereNotNull('city')->distinct()->orderBy('city')->pluck('city');
        $statuses = ['published' => 'Published', 'draft' => 'Draft', 'cancelled' => 'Cancelled'];

        return view('events.index', [
            // Data
            'events'        => $events,
            // Sorting (for your sorting UI)
            'sortBy'        => $sortParams['sort_by'],
            'sortDirection' => $sortParams['direction'],
            'sortOptions'   => $this->sortingService->getEventSortOptions(),
            'isDefaultSort' => $this->sortingService->isDefaultSort($sortParams['sort_by'], $sortParams['direction']),
            // Filters (for the filter UI to keep state)
            'q'             => $q,
            'city'          => $city,
            'status'        => $status,
            'dateFrom'      => $from,
            'dateTo'        => $to,
            'priceMin'      => $minPrice,
            'priceMax'      => $maxPrice,
            'cities'        => $cities,
            'statuses'      => $statuses,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('events.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title'         => 'required|string|max:255',
                'description'   => 'required|string',
                'event_date'    => 'required|date|after:today',
                'start_time'    => 'required',
                'end_time'      => 'nullable',
                'venue'         => 'required|string|max:255',
                'address'       => 'nullable|string|max:255',
                'city'          => 'nullable|string|max:255',
                'total_tickets' => 'required|integer|min:1',
                'price'         => 'required|numeric|min:0',
                'status'        => 'required|in:draft,published',
                'image'         => 'nullable|image|max:2048',
            ]);

            // Normalize time inputs (store as full datetime if that’s your schema)
            if (!empty($request->start_time)) {
                $validated['start_time'] = date('Y-m-d') . ' ' . $request->start_time . ':00';
            }
            if (!empty($request->end_time)) {
                $validated['end_time'] = date('Y-m-d') . ' ' . $request->end_time . ':00';
            }

            $validated['organizer_id']    = Auth::id();
            $validated['tickets_sold']    = 0;
            $validated['approval_status'] = 'pending';

            if ($request->hasFile('image')) {
                $validated['image_path'] = $request->file('image')->store('events', 'public');
            }

            Event::create($validated);
        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['error' => 'Failed to create event: ' . $e->getMessage()]);
        }

        return redirect()->route('events.index')
            ->with('success', 'Event created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $event = Event::findOrFail($id);
        return view('events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $event = Event::findOrFail($id);
        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $event = Event::findOrFail($id);

        // If event in future, keep "after:today"; otherwise allow any date
        $dateRule = $event->event_date > now()
            ? 'required|date|after:today'
            : 'required|date';

        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'required|string',
            'event_date'    => $dateRule,
            'start_time'    => 'required',
            'end_time'      => 'nullable',
            'venue'         => 'required|string|max:255',
            'address'       => 'nullable|string|max:255',
            'city'          => 'nullable|string|max:255',
            'total_tickets' => 'required|integer|min:1',
            'price'         => 'required|numeric|min:0',
            'status'        => 'required|in:draft,published,cancelled',
            'image'         => 'nullable|image|max:2048',
        ]);

        if (!empty($request->start_time)) {
            $validated['start_time'] = date('Y-m-d') . ' ' . $request->start_time . ':00';
        }
        if (!empty($request->end_time)) {
            $validated['end_time'] = date('Y-m-d') . ' ' . $request->end_time . ':00';
        }

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('events', 'public');
        }

        $event->update($validated);

        return redirect()->route('events.index')
            ->with('success', 'Event updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return redirect()->route('events.index')
            ->with('success', 'Event deleted successfully!');
    }
}
