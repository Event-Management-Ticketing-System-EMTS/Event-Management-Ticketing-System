<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Notification Model
 *
 * Stores notifications for users (e.g., booking, cancellations, event updates).
 */
class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'is_read',
        'data',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'data'    => 'array',   // Store extra metadata (event_id, ticket_id, etc.)
    ];

    // Notification types
    public const TYPE_TICKET_CANCELLED  = 'ticket_cancelled';
    public const TYPE_TICKET_PURCHASED  = 'ticket_purchased';
    public const TYPE_EVENT_UPDATE      = 'event_update';
    public const TYPE_SYSTEM            = 'system'; // fallback type

    /**
     * Get the user that owns the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update(['is_read' => true]);
        }
    }

    /**
     * Mark notification as unread.
     */
    public function markAsUnread(): void
    {
        if ($this->is_read) {
            $this->update(['is_read' => false]);
        }
    }

    /**
     * Check if notification is unread.
     */
    public function isUnread(): bool
    {
        return !$this->is_read;
    }

    /**
     * Scope for unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for recent notifications (latest first).
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
