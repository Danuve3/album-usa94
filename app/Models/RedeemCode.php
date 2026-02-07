<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RedeemCode extends Model
{
    use CrudTrait;

    protected $fillable = [
        'code',
        'packs_count',
        'max_redemptions',
        'times_redeemed',
        'expires_at',
        'user_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(RedeemCodeUsage::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->active()->notExpired()->where(function ($q) {
            $q->whereNull('max_redemptions')
                ->orWhereColumn('times_redeemed', '<', 'max_redemptions');
        });
    }

    public function isRedeemableBy(User $user): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->max_redemptions !== null && $this->times_redeemed >= $this->max_redemptions) {
            return false;
        }

        if ($this->user_id !== null && $this->user_id !== $user->id) {
            return false;
        }

        if ($this->hasBeenUsedBy($user)) {
            return false;
        }

        return true;
    }

    public function hasBeenUsedBy(User $user): bool
    {
        return $this->usages()->where('user_id', $user->id)->exists();
    }
}
