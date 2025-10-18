<?php

namespace App\Http\Controllers;

use App\Services\SimpleNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;


/**
 * Simple Notification Controller
 *
 * Handles notification display and management for users.
 * Perfect for beginners - simple CRUD operations!
 */
class SimpleNotificationController extends Controller
{
    protected $notificationService;

    public function __construct(SimpleNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Show all notifications for the current user
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = $this->notificationService->getAllNotifications($user);
        $unreadCount = $this->notificationService->getUnreadCount($user);

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Get unread notifications (for AJAX requests)
     */
    public function getUnread(): JsonResponse
    {
        $user = Auth::user();
        $notifications = $this->notificationService->getUnreadNotifications($user);
        $count = $this->notificationService->getUnreadCount($user);

        return response()->json([
            'notifications' => $notifications,
            'count' => $count
        ]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Request $request, $id): JsonResponse
    {
        $user = Auth::user();
        $success = $this->notificationService->markAsRead($id, $user);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Notification not found'
        ], 404);
    }

    /**
     * Get notification count for header badge
     */
    public function getCount(): JsonResponse
    {
        $user = Auth::user();

        $count = $this->notificationService->getUnreadCount($user);

        return response()->json(['count' => $count]);
    }
}
