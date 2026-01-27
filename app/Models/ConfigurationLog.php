<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConfigurationLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'setting_id',
        'user_id',
        'key',
        'old_value',
        'new_value',
    ];

    /**
     * Get the setting that this log belongs to.
     */
    public function setting(): BelongsTo
    {
        return $this->belongsTo(Setting::class);
    }

    /**
     * Get the user who made the change.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log a configuration change.
     */
    public static function logChange(Setting $setting, ?string $oldValue, string $newValue, ?User $user = null): self
    {
        return self::create([
            'setting_id' => $setting->id,
            'user_id' => $user?->id,
            'key' => $setting->key,
            'old_value' => $oldValue,
            'new_value' => $newValue,
        ]);
    }
}
