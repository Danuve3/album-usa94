<?php

namespace App\Models;

use App\Enums\StickerRarity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sticker extends Model
{
    /** @use HasFactory<\Database\Factories\StickerFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'number',
        'name',
        'page_number',
        'position_x',
        'position_y',
        'width',
        'height',
        'is_horizontal',
        'rarity',
        'image_path',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'number' => 'integer',
            'page_number' => 'integer',
            'position_x' => 'integer',
            'position_y' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'is_horizontal' => 'boolean',
            'rarity' => StickerRarity::class,
        ];
    }

    /**
     * Get the user stickers for this sticker.
     */
    public function userStickers(): HasMany
    {
        return $this->hasMany(UserSticker::class);
    }
}
