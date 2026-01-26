<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSticker extends Model
{
    /** @use HasFactory<\Database\Factories\UserStickerFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'sticker_id',
        'is_glued',
        'obtained_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_glued' => 'boolean',
            'obtained_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns this sticker.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the sticker.
     */
    public function sticker(): BelongsTo
    {
        return $this->belongsTo(Sticker::class);
    }

    /**
     * Scope a query to only include glued stickers.
     */
    public function scopeGlued(Builder $query): Builder
    {
        return $query->where('is_glued', true);
    }

    /**
     * Scope a query to only include unglued stickers.
     */
    public function scopeUnglued(Builder $query): Builder
    {
        return $query->where('is_glued', false);
    }

    /**
     * Scope a query to only include duplicate stickers (more than one of the same sticker for a user).
     */
    public function scopeDuplicates(Builder $query): Builder
    {
        return $query->whereIn('sticker_id', function ($subquery) {
            $subquery->select('sticker_id')
                ->from('user_stickers')
                ->whereColumn('user_stickers.user_id', 'user_stickers.user_id')
                ->groupBy('sticker_id', 'user_id')
                ->havingRaw('COUNT(*) > 1');
        });
    }
}
