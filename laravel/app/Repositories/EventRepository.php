<?php

namespace App\Repositories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Collection;

class EventRepository
{
    protected $model;

    public function __construct(Event $model)
    {
        $this->model = $model;
    }

    /**
     * Get all events with sorting
     */
    public function getAllWithSorting(string $sortBy = 'created_at', string $direction = 'desc'): Collection
    {
        return $this->model
            ->orderBy($sortBy, $direction)
            ->get();
    }

    /**
     * Get events for a specific organizer with sorting
     */
    public function getByOrganizerWithSorting(int $organizerId, string $sortBy = 'created_at', string $direction = 'desc'): Collection
    {
        return $this->model
            ->where('organizer_id', $organizerId)
            ->orderBy($sortBy, $direction)
            ->get();
    }

    /**
     * Get published events only with sorting
     */
    public function getPublishedWithSorting(string $sortBy = 'created_at', string $direction = 'desc'): Collection
    {
        return $this->model
            ->where('status', 'published')
            ->orderBy($sortBy, $direction)
            ->get();
    }

    /**
     * Get published and approved events only with sorting
     */
    public function getPublishedAndApprovedWithSorting(string $sortBy = 'created_at', string $direction = 'desc'): Collection
    {
        return $this->model
            ->where('status', 'published')
            ->where('approval_status', 'approved')
            ->orderBy($sortBy, $direction)
            ->get();
    }
}

    /**












}    }            ->get();            ->orderBy($sortBy, $direction)            ->where('approval_status', 'approved')            ->where('status', 'published')        return $this->model    {    public function getPublishedAndApprovedWithSorting(string $sortBy = 'created_at', string $direction = 'desc'): Collection     */     * Get published and approved events only with sorting
