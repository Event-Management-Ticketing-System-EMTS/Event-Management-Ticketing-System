<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Simple Notification Model
 *
 * Stores notifications for users (especially organizers)
 * about important events like ticket cancellations
 */
class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'is_read',
        'data'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'data' => 'array', // Store extra data as JSON
    ];

    // Notification types
    public const TYPE_TICKET_CANCELLED = 'ticket_cancelled';
    public const TYPE_TICKET_PURCHASED = 'ticket_purchased';
    public const TYPE_EVENT_UPDATE = 'event_update';

    /**
     * Get the user that owns the notification
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): void
    {
        $this->update(['is_read' => true]);
    }

    /**
     * Check if notification is unread
     */
    public function isUnread(): bool
    {
        return !$this->is_read;
    }
}
