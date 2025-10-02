<?php

namespace App\Http\Controllers;

use App\Models\SupportMessage;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    /**
     * Show support form for users
     */
    public function create()
    {
        $events = Event::where('status', 'published')
            ->where('approval_status', 'approved')
            ->orderBy('event_date', 'asc')
            ->get();

        return view('support.create', compact('events'));
    }

    /**
     * Store a new support message
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'event_id' => 'nullable|exists:events,id',
            'priority' => 'required|in:low,medium,high'
        ]);

        SupportMessage::create([
            'user_id' => Auth::id(),
            'event_id' => $request->event_id,
            'subject' => $request->subject,
            'message' => $request->message,
            'priority' => $request->priority,
            'status' => 'open'
        ]);

        return redirect()->route('support.create')
            ->with('success', 'Your message has been sent! An admin will respond soon.');
    }

    /**
     * Show all support messages (Admin only)
     */
    public function index()
    {
        $messages = SupportMessage::with(['user', 'event', 'admin'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('support.index', compact('messages'));
    }

    /**
     * Show single support message
     */
    public function show($id)
    {
        $message = SupportMessage::with(['user', 'event', 'admin'])->findOrFail($id);

        return view('support.show', compact('message'));
    }

    /**
     * Respond to support message (Admin only)
     */
    public function respond(Request $request, $id)
    {
        $request->validate([
            'admin_response' => 'required|string|max:1000',
            'status' => 'required|in:open,in_progress,resolved'
        ]);

        $message = SupportMessage::findOrFail($id);

        $message->update([
            'admin_response' => $request->admin_response,
            'admin_responded_at' => now(),
            'admin_id' => Auth::id(),
            'status' => $request->status
        ]);

        return redirect()->route('support.show', $id)
            ->with('success', 'Response sent successfully!');
    }
}
