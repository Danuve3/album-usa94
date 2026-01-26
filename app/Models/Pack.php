<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pack extends Model
{
    /** @use HasFactory<\Database\Factories\PackFactory> */
    use HasFactory;

    /**
     * Number of stickers contained in each pack.
     */
    public const STICKERS_PER_PACK = 5;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'opened_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'opened_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns this pack.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include unopened packs.
     */
    public function scopeUnopened(Builder $query): Builder
    {
        return $query->whereNull('opened_at');
    }

    /**
     * Scope a query to only include opened packs.
     */
    public function scopeOpened(Builder $query): Builder
    {
        return $query->whereNotNull('opened_at');
    }

    /**
     * Scope a query to only include packs created today.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }
}
