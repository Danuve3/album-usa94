<?php

namespace App\Enums;

enum TradeStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';

    /**
     * Get the valid transitions from this status.
     *
     * @return array<TradeStatus>
     */
    public function validTransitions(): array
    {
        return match ($this) {
            self::Pending => [self::Accepted, self::Rejected, self::Cancelled],
            self::Accepted, self::Rejected, self::Cancelled => [],
        };
    }

    /**
     * Check if a transition to the given status is valid.
     */
    public function canTransitionTo(TradeStatus $status): bool
    {
        return in_array($status, $this->validTransitions(), true);
    }
}
