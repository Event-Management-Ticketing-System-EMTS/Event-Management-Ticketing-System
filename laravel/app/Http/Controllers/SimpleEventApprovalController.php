<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\SimpleEventApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Simple Event Approval Controller
 * Handles admin approval/rejection of events
 */
class SimpleEventApprovalController extends Controller
{
    protected $approvalService;

    public function __construct(SimpleEventApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
    }

    /**
     * Check if user is admin
     */
    private function checkAdminAccess()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Admin access required');
        }
    }

    /**
     * Show pending events for approval
     */
    public function index()
    {
        $this->checkAdminAccess();

        $pendingEvents = $this->approvalService->getPendingEvents();
        $stats = $this->approvalService->getApprovalStats();

        return view('admin.approvals.index', compact('pendingEvents', 'stats'));
    }

    /**
     * Show single event for approval
     */
    public function show(Event $event)
    {
        $this->checkAdminAccess();

        $event->load(['organizer', 'reviewer']);

        return view('admin.approvals.show', compact('event'));
    }
    /**
     * Approve an event
     */
    public function approve(Request $request, Event $event)
    {
        $this->checkAdminAccess();

        $request->validate([
            'comments' => 'nullable|string|max:1000',
        ]);

        $success = $this->approvalService->approve($event, $request->comments);

        if ($success) {
            return redirect()->route('admin.approvals.index')
                ->with('success', "Event '{$event->title}' has been approved!");
        }

        return back()->with('error', 'Failed to approve event.');
    }

    /**
     * Reject an event
     */
    public function reject(Request $request, Event $event)
    {
        $this->checkAdminAccess();

        $request->validate([
            'comments' => 'required|string|max:1000',
        ]);

        $success = $this->approvalService->reject($event, $request->comments);

        if ($success) {
            return redirect()->route('admin.approvals.index')
                ->with('success', "Event '{$event->title}' has been rejected.");
        }

        return back()->with('error', 'Failed to reject event.');
    }
}
