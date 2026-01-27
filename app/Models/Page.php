<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    use CrudTrait;

    /** @use HasFactory<\Database\Factories\PageFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'number',
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
        ];
    }

    /**
     * Get the stickers for this page.
     */
    public function stickers(): HasMany
    {
        return $this->hasMany(Sticker::class, 'page_number', 'number');
    }

    /**
     * Scope a query to order by page number.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('number');
    }
}
