<?php

namespace App\Enums;

enum MarketListingStatus: string
{
    case Active = 'active';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Expired = 'expired';

    /**
     * Get the valid transitions from this status.
     *
     * @return array<MarketListingStatus>
     */
    public function validTransitions(): array
    {
        return match ($this) {
            self::Active => [self::Completed, self::Cancelled, self::Expired],
            self::Completed, self::Cancelled, self::Expired => [],
        };
    }

    /**
     * Check if a transition to the given status is valid.
     */
    public function canTransitionTo(MarketListingStatus $status): bool
    {
        return in_array($status, $this->validTransitions(), true);
    }
}
