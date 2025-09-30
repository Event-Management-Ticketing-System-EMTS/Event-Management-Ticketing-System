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
        'status'
    ];

    protected $casts = [
        'purchase_date' => 'datetime',
        'total_price' => 'decimal:2',
        'quantity' => 'integer',
    ];

    // Ticket statuses
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the event this ticket belongs to
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user who purchased this ticket
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}