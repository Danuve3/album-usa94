<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RedeemCodeUsage extends Model
{
    protected $fillable = [
        'redeem_code_id',
        'user_id',
        'packs_given',
    ];

    public function redeemCode(): BelongsTo
    {
        return $this->belongsTo(RedeemCode::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
