<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'quantity',
        'total_price',
        'purchase_date',
        'status',
        'payment_status',
        'payment_amount',
        'paid_at',
        'payment_reference'
    ];

    protected $casts = [
        'purchase_date'   => 'datetime',
        'paid_at'         => 'datetime',
        'total_price'     => 'decimal:2',
        'payment_amount'  => 'decimal:2',
        'quantity'        => 'integer',
    ];

    // ------------------------------
    // Ticket statuses
    // ------------------------------
    public const STATUS_PENDING   = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';

    // ------------------------------
    // Relationships
    // ------------------------------

    /** The event this ticket belongs to */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /** The user who purchased this ticket */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ------------------------------
    // Payment status helpers
    // ------------------------------

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->payment_status === 'failed';
    }

    public function isRefunded(): bool
    {
        return $this->payment_status === 'refunded';
    }
}
