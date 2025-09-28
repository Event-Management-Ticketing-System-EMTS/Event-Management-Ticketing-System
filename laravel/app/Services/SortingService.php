<?php

namespace App\Services;

class SortingService
{
    /**
     * Available sorting options for events
     */
    public const EVENT_SORT_OPTIONS = [
        'created_at' => '📅 Date Created',
        'title' => '📝 Title',
        'event_date' => '🗓️ Event Date',
        'price' => '💰 Price',
        'total_tickets' => '🎫 Total Tickets',
        'tickets_sold' => '📊 Tickets Sold',
        'status' => '⭐ Status'
    ];

    public const ALLOWED_DIRECTIONS = ['asc', 'desc'];

    /**
     * Validate and clean sorting parameters
     */
    public function validateEventSortParameters(?string $sortBy, ?string $direction): array
    {
        return [
            'sort_by' => $this->validateSortBy($sortBy, array_keys(self::EVENT_SORT_OPTIONS)),
            'direction' => $this->validateDirection($direction),
        ];
    }

    /**
     * Get event sorting options
     */
    public function getEventSortOptions(): array
    {
        return self::EVENT_SORT_OPTIONS;
    }

    /**
     * Check if current sort is default
     */
    public function isDefaultSort(string $sortBy, string $direction): bool
    {
        return $sortBy === 'created_at' && $direction === 'desc';
    }

    /**
     * Validate sort field
     */
    private function validateSortBy(?string $sortBy, array $allowedSorts): string
    {
        return in_array($sortBy, $allowedSorts) ? $sortBy : 'created_at';
    }

    /**
     * Validate sort direction
     */
    private function validateDirection(?string $direction): string
    {
        return in_array($direction, self::ALLOWED_DIRECTIONS) ? $direction : 'desc';
    }

    /**
     * Get opposite direction (for toggle functionality)
     */
    public function getOppositeDirection(string $direction): string
    {
        return $direction === 'asc' ? 'desc' : 'asc';
    }
}
