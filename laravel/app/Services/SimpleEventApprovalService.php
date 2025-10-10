<?php

namespace App\Services;

use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Simple Event Approval Service
 * Handles approve/reject operations for admin users
 */
class SimpleEventApprovalService
{
    protected $notificationService;

    public function __construct(SimpleNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * Approve an event
     * When approved, automatically publish the event so it appears in browse section
     */
    public function approve(Event $event, ?string $comments = null): bool
    {
        $admin = Auth::user();

        // Only admins can approve events
        if (!$admin || $admin->role !== 'admin') {
            return false;
        }

        $event->update([
            'approval_status' => 'approved',
            'status' => 'published', // ✅ Auto-publish when approved
            'admin_comments' => $comments,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);

        // ✅ Notify the organizer that their event is now live
        $this->notificationService->notifyEventApproval($event, $comments);

        return true;
    }

    /**
     * Reject an event
     */
    public function reject(Event $event, ?string $comments = null): bool
    {
        $admin = Auth::user();

        // Only admins can reject events
        if (!$admin || $admin->role !== 'admin') {
            return false;
        }

        $event->update([
            'approval_status' => 'rejected',
            'admin_comments' => $comments,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);

        // ✅ Notify the organizer about the rejection
        $this->notificationService->notifyEventRejection($event, $comments);

        return true;
    }

    /**
     * Get all pending events for approval
     */
    public function getPendingEvents()
    {
        return Event::with(['organizer'])
            ->where('approval_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    /**
     * Get approval statistics
     */
    public function getApprovalStats(): array
    {
        return [
            'pending' => Event::where('approval_status', 'pending')->count(),
            'approved' => Event::where('approval_status', 'approved')->count(),
            'rejected' => Event::where('approval_status', 'rejected')->count(),
            'total' => Event::count(),
        ];
    }
}
