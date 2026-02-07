<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\RedeemCode;
use App\Models\RedeemCodeUsage;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RedeemCodeService
{
    /**
     * Redeem a code for a user.
     *
     * @return array{success: bool, message: string, packs_count?: int}
     */
    public function redeem(string $code, User $user): array
    {
        $redeemCode = RedeemCode::whereRaw('LOWER(code) = ?', [strtolower(trim($code))])->first();

        if (! $redeemCode) {
            return ['success' => false, 'message' => 'El código introducido no es válido.'];
        }

        if (! $redeemCode->is_active) {
            return ['success' => false, 'message' => 'Este código está desactivado.'];
        }

        if ($redeemCode->expires_at && $redeemCode->expires_at->isPast()) {
            return ['success' => false, 'message' => 'Este código ha expirado.'];
        }

        if ($redeemCode->user_id !== null && $redeemCode->user_id !== $user->id) {
            return ['success' => false, 'message' => 'Este código no es válido para tu cuenta.'];
        }

        if ($redeemCode->hasBeenUsedBy($user)) {
            return ['success' => false, 'message' => 'Ya has canjeado este código anteriormente.'];
        }

        if ($redeemCode->max_redemptions !== null && $redeemCode->times_redeemed >= $redeemCode->max_redemptions) {
            return ['success' => false, 'message' => 'Este código ya ha alcanzado el máximo de canjes.'];
        }

        return DB::transaction(function () use ($redeemCode, $user) {
            // Lock the row to prevent race conditions
            $redeemCode = RedeemCode::lockForUpdate()->find($redeemCode->id);

            // Re-check max_redemptions after lock
            if ($redeemCode->max_redemptions !== null && $redeemCode->times_redeemed >= $redeemCode->max_redemptions) {
                return ['success' => false, 'message' => 'Este código ya ha alcanzado el máximo de canjes.'];
            }

            $redeemCode->increment('times_redeemed');

            RedeemCodeUsage::create([
                'redeem_code_id' => $redeemCode->id,
                'user_id' => $user->id,
                'packs_given' => $redeemCode->packs_count,
            ]);

            $user->givePacks($redeemCode->packs_count);

            ActivityLog::log(
                $user,
                'code_redeemed',
                "Canjeó el código '{$redeemCode->code}' y recibió {$redeemCode->packs_count} sobres",
                [
                    'code' => $redeemCode->code,
                    'packs_count' => $redeemCode->packs_count,
                    'redeem_code_id' => $redeemCode->id,
                ]
            );

            $packsText = $redeemCode->packs_count === 1 ? '1 sobre' : "{$redeemCode->packs_count} sobres";

            return [
                'success' => true,
                'message' => "¡Código canjeado! Has recibido {$packsText}.",
                'packs_count' => $redeemCode->packs_count,
            ];
        });
    }
}
