<?php

namespace App\Models;

use App\Enums\TradeItemDirection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TradeItem extends Model
{
    /** @use HasFactory<\Database\Factories\TradeItemFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'trade_id',
        'user_sticker_id',
        'direction',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'direction' => TradeItemDirection::class,
        ];
    }

    /**
     * Get the trade this item belongs to.
     */
    public function trade(): BelongsTo
    {
        return $this->belongsTo(Trade::class);
    }

    /**
     * Get the user sticker being traded.
     */
    public function userSticker(): BelongsTo
    {
        return $this->belongsTo(UserSticker::class);
    }

    /**
     * Check if this is an offered item.
     */
    public function isOffered(): bool
    {
        return $this->direction === TradeItemDirection::Offered;
    }

    /**
     * Check if this is a requested item.
     */
    public function isRequested(): bool
    {
        return $this->direction === TradeItemDirection::Requested;
    }
}
