<?php

namespace App\Models;

use App\Models\Setting;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

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
        'avatar',
        'password',
        'is_admin',
        'is_banned',
        'ban_reason',
        'banned_at',
        'last_pack_received_at',
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
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'unopened_packs_count',
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
            'is_admin' => 'boolean',
            'is_banned' => 'boolean',
            'banned_at' => 'datetime',
            'last_pack_received_at' => 'datetime',
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
     * Get the market listings for this user.
     */
    public function marketListings(): HasMany
    {
        return $this->hasMany(MarketListing::class);
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

    /**
     * Get the next pack delivery time.
     */
    public function getNextPackAt(): ?Carbon
    {
        $intervalMinutes = Setting::get('pack_delivery_interval_minutes', 240);
        $lastReceived = $this->last_pack_received_at ?? $this->created_at;

        return $lastReceived->copy()->addMinutes($intervalMinutes);
    }

    /**
     * Get the avatar URL.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return Storage::url($this->avatar);
        }

        // Default avatar using UI Avatars
        $name = urlencode($this->name);

        return "https://ui-avatars.com/api/?name={$name}&background=10b981&color=fff&size=128";
    }
}
