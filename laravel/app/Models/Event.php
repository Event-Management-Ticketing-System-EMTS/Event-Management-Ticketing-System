<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'event_date',
        'start_time',
        'end_time',
        'venue',
        'address',
        'city',
        'total_tickets',
        'tickets_sold',
        'price',
        'status',
        'approval_status',
        'admin_comments',
        'reviewed_by',
        'reviewed_at',
        'organizer_id',
        'image_path',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'event_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'reviewed_at' => 'datetime',
        'price' => 'decimal:2',
        'tickets_sold' => 'integer',
        'total_tickets' => 'integer',
    ];

    /**
     * Get the organizer of the event.
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    /**
     * Get the admin who reviewed this event.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get all tickets for this event
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get available ticket count
     */
    public function getAvailableTicketsAttribute(): int
    {
        return max(0, $this->total_tickets - $this->tickets_sold);
    }

    /**
     * Check if event has available tickets
     */
    public function hasAvailableTickets(int $quantity = 1): bool
    {
        return $this->available_tickets >= $quantity;
    }

    /**
     * Get ticket availability percentage
     */
    public function getAvailabilityPercentageAttribute(): float
    {
        if ($this->total_tickets == 0) {
            return 0;
        }

        return round(($this->available_tickets / $this->total_tickets) * 100, 2);
    }

    /**
     * Check if event is pending approval
     */
    public function isPending(): bool
    {
        return $this->approval_status === 'pending';
    }

    /**
     * Check if event is approved
     */
    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    /**
     * Check if event is rejected
     */
    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }
}
