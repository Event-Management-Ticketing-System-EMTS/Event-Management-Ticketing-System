<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Event extends Model
{
    use HasFactory;

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
        'event_date'   => 'date',
        'start_time'   => 'datetime',
        'end_time'     => 'datetime',
        'reviewed_at'  => 'datetime',
        'price'        => 'decimal:2',
        'tickets_sold' => 'integer',
        'total_tickets'=> 'integer',
    ];

    /**
     * Relationships
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Accessors
     */
    public function getAvailableTicketsAttribute(): int
    {
        return max(0, $this->total_tickets - $this->tickets_sold);
    }

    public function getAvailabilityPercentageAttribute(): float
    {
        return $this->total_tickets > 0
            ? round(($this->available_tickets / $this->total_tickets) * 100, 2)
            : 0;
    }

    /**
     * Status helpers
     */
    public function hasAvailableTickets(int $quantity = 1): bool
    {
        return $this->available_tickets >= $quantity;
    }

    public function isPending(): bool
    {
        return $this->approval_status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }

    /**
     * Query scopes for cleaner controllers
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('approval_status', 'approved');
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->whereDate('event_date', '>=', now());
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (!$term) return $query;

        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
              ->orWhere('venue', 'like', "%{$term}%")
              ->orWhere('city', 'like', "%{$term}%");
        });
    }
}
