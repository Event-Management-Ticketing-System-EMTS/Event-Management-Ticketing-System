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
        $this->sortingService = $sortingService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Validate and clean sorting parameters
        $sortParams = $this->sortingService->validateEventSortParameters(
            $request->get('sort'),
            $request->get('direction')
        );

        // Get events with sorting
        $events = $this->eventRepository->getAllWithSorting(
            $sortParams['sort_by'],
            $sortParams['direction']
        );

        return view('events.index', [
            'events' => $events,
            'sortBy' => $sortParams['sort_by'],
            'sortDirection' => $sortParams['direction'],
            'sortOptions' => $this->sortingService->getEventSortOptions(),
            'isDefaultSort' => $this->sortingService->isDefaultSort($sortParams['sort_by'], $sortParams['direction'])
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
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'event_date' => 'required|date|after:today',
                'start_time' => 'required',
                'end_time' => 'nullable',
                'venue' => 'required|string|max:255',
                'address' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'total_tickets' => 'required|integer|min:1',
                'price' => 'required|numeric|min:0',
                'status' => 'required|in:draft,published',
                'image' => 'nullable|image|max:2048',
            ]);

            // Format time inputs
            if (!empty($request->start_time)) {
                $validated['start_time'] = date('Y-m-d') . ' ' . $request->start_time . ':00';
            }

            if (!empty($request->end_time)) {
                $validated['end_time'] = date('Y-m-d') . ' ' . $request->end_time . ':00';
            }

            // Set organizer_id to current user
            $validated['organizer_id'] = Auth::id();

            // Initialize tickets_sold to 0
            $validated['tickets_sold'] = 0;

            // Handle image upload if provided
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('events', 'public');
                $validated['image_path'] = $imagePath;
            }

            // Create the event
            $event = \App\Models\Event::create($validated);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to create event: ' . $e->getMessage()]);
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

        // Special rule for event date to allow editing events that might be in the past
        $dateRule = 'required|date';
        if ($event->event_date > now()) {
            $dateRule = 'required|date|after:today';
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_date' => $dateRule,
            'start_time' => 'required',
            'end_time' => 'nullable',
            'venue' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'total_tickets' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:draft,published,cancelled',
            'image' => 'nullable|image|max:2048',
        ]);

        // Format time inputs
        if (!empty($request->start_time)) {
            $validated['start_time'] = date('Y-m-d') . ' ' . $request->start_time . ':00';
        }

        if (!empty($request->end_time)) {
            $validated['end_time'] = date('Y-m-d') . ' ' . $request->end_time . ':00';
        }

        // Handle image upload if provided
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('events', 'public');
            $validated['image_path'] = $imagePath;
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
        //
    }
}
