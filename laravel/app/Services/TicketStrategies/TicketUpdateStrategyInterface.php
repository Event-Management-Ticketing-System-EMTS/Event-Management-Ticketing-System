<?php

namespace App\Services\TicketStrategies;

use App\Models\Event;

/**
 * Strategy Pattern Interface
 * Defines contract for different ticket availability update strategies
 */
interface TicketUpdateStrategyInterface
{
    /**
     * Update event ticket availability
     */
    public function updateAvailability(Event $event): bool;

    /**
     * Check if tickets are available for purchase
     */
    public function isAvailable(Event $event, int $requestedQuantity): bool;

    /**
     * Get available ticket count
     */
    public function getAvailableCount(Event $event): int;
}
