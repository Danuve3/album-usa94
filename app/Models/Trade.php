<?php

namespace App\Models;

use App\Enums\TradeItemDirection;
use App\Enums\TradeStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trade extends Model
{
    /** @use HasFactory<\Database\Factories\TradeFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'status',
        'expires_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => TradeStatus::class,
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Get the sender of the trade.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the receiver of the trade.
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Get all items in this trade.
     */
    public function items(): HasMany
    {
        return $this->hasMany(TradeItem::class);
    }

    /**
     * Get offered items (from sender).
     */
    public function offeredItems(): HasMany
    {
        return $this->items()->where('direction', TradeItemDirection::Offered);
    }

    /**
     * Get requested items (from receiver).
     */
    public function requestedItems(): HasMany
    {
        return $this->items()->where('direction', TradeItemDirection::Requested);
    }

    /**
     * Scope a query to only include pending trades.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', TradeStatus::Pending);
    }

    /**
     * Scope a query to only include active (non-expired pending) trades.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->pending()
            ->where(function (Builder $q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope a query to only include expired trades.
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->pending()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now());
    }

    /**
     * Scope a query to trades involving a specific user.
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where(function (Builder $q) use ($userId) {
            $q->where('sender_id', $userId)
                ->orWhere('receiver_id', $userId);
        });
    }

    /**
     * Check if the trade is pending.
     */
    public function isPending(): bool
    {
        return $this->status === TradeStatus::Pending;
    }

    /**
     * Check if the trade has expired.
     */
    public function isExpired(): bool
    {
        return $this->isPending()
            && $this->expires_at !== null
            && $this->expires_at->isPast();
    }

    /**
     * Transition to a new status if valid.
     */
    public function transitionTo(TradeStatus $newStatus): bool
    {
        if (! $this->status->canTransitionTo($newStatus)) {
            return false;
        }

        $this->status = $newStatus;
        $this->save();

        return true;
    }
}
