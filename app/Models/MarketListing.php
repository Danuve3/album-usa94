<?php

namespace App\Models;

use App\Enums\MarketListingStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketListing extends Model
{
    /** @use HasFactory<\Database\Factories\MarketListingFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'user_sticker_id',
        'wanted_sticker_id',
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
            'status' => MarketListingStatus::class,
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Get the user who created this listing.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user sticker being offered.
     */
    public function userSticker(): BelongsTo
    {
        return $this->belongsTo(UserSticker::class);
    }

    /**
     * Get the sticker that the user wants in exchange.
     */
    public function wantedSticker(): BelongsTo
    {
        return $this->belongsTo(Sticker::class, 'wanted_sticker_id');
    }

    /**
     * Scope a query to only include active listings.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', MarketListingStatus::Active)
            ->where(function (Builder $q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope a query to only include expired listings.
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('status', MarketListingStatus::Active)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now());
    }

    /**
     * Scope a query to filter by wanted sticker.
     */
    public function scopeWanting(Builder $query, int $stickerId): Builder
    {
        return $query->where('wanted_sticker_id', $stickerId);
    }

    /**
     * Scope a query to filter by offered sticker.
     */
    public function scopeOffering(Builder $query, int $stickerId): Builder
    {
        return $query->whereHas('userSticker', function (Builder $q) use ($stickerId) {
            $q->where('sticker_id', $stickerId);
        });
    }

    /**
     * Scope a query to exclude listings from a specific user.
     */
    public function scopeExcludeUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', '!=', $userId);
    }

    /**
     * Check if the listing is active.
     */
    public function isActive(): bool
    {
        return $this->status === MarketListingStatus::Active && ! $this->isExpired();
    }

    /**
     * Check if the listing has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    /**
     * Transition to a new status if valid.
     */
    public function transitionTo(MarketListingStatus $newStatus): bool
    {
        if (! $this->status->canTransitionTo($newStatus)) {
            return false;
        }

        $this->status = $newStatus;
        $this->save();

        return true;
    }
}
