<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use CrudTrait;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_banned',
        'ban_reason',
        'banned_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_banned' => 'boolean',
            'banned_at' => 'datetime',
        ];
    }

    /**
     * Get the user stickers for this user.
     */
    public function userStickers(): HasMany
    {
        return $this->hasMany(UserSticker::class);
    }

    /**
     * Get the packs for this user.
     */
    public function packs(): HasMany
    {
        return $this->hasMany(Pack::class);
    }

    /**
     * Get the activity logs for this user.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Get total stickers count.
     */
    public function getTotalStickersCountAttribute(): int
    {
        return $this->userStickers()->count();
    }

    /**
     * Get glued stickers count.
     */
    public function getGluedStickersCountAttribute(): int
    {
        return $this->userStickers()->glued()->count();
    }

    /**
     * Get duplicate stickers count (stickers that appear more than once).
     */
    public function getDuplicateStickersCountAttribute(): int
    {
        return $this->userStickers()
            ->selectRaw('sticker_id, COUNT(*) as count')
            ->groupBy('sticker_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->sum(fn ($item) => $item->count - 1);
    }

    /**
     * Get unopened packs count.
     */
    public function getUnopenedPacksCountAttribute(): int
    {
        return $this->packs()->unopened()->count();
    }

    /**
     * Ban the user.
     */
    public function ban(?string $reason = null): void
    {
        $this->update([
            'is_banned' => true,
            'ban_reason' => $reason,
            'banned_at' => now(),
        ]);

        ActivityLog::log($this, 'banned', $reason);
    }

    /**
     * Unban the user.
     */
    public function unban(): void
    {
        $this->update([
            'is_banned' => false,
            'ban_reason' => null,
            'banned_at' => null,
        ]);

        ActivityLog::log($this, 'unbanned');
    }

    /**
     * Give packs to the user.
     */
    public function givePacks(int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $this->packs()->create(['opened_at' => null]);
        }

        ActivityLog::log($this, 'packs_given', "Se otorgaron {$count} sobres", ['count' => $count]);
    }
}
