<?php

namespace App\Services;

use App\Models\Pack;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PackDeliveryService
{
    /**
     * Deliver any pending packs to the user based on time elapsed.
     *
     * This implements "just-in-time" delivery: instead of a scheduler,
     * packs are calculated and delivered when the user accesses the app.
     *
     * Uses database locking to prevent race conditions when multiple
     * components try to deliver packs simultaneously.
     */
    public function deliverPendingPacks(User $user): int
    {
        $intervalMinutes = Setting::get('pack_delivery_interval_minutes', 240);
        $packsPerDelivery = Setting::get('packs_per_delivery', 1);

        // Use a database transaction with row locking to prevent race conditions
        return DB::transaction(function () use ($user, $intervalMinutes, $packsPerDelivery) {
            // Lock the user row to prevent concurrent deliveries
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();

            $lastReceived = $lockedUser->last_pack_received_at ?? $lockedUser->created_at;
            $now = now();

            // Calculate complete intervals passed
            $minutesSinceLastPack = $lastReceived->diffInMinutes($now);
            $pendingDeliveries = (int) floor($minutesSinceLastPack / $intervalMinutes);

            if ($pendingDeliveries <= 0) {
                return 0;
            }

            $packsToDeliver = $pendingDeliveries * $packsPerDelivery;

            // Create the packs
            for ($i = 0; $i < $packsToDeliver; $i++) {
                Pack::create([
                    'user_id' => $lockedUser->id,
                    'opened_at' => null,
                ]);
            }

            // Update timestamp to the last completed "slot", not to now()
            // This ensures the countdown restarts from the correct time
            $lockedUser->last_pack_received_at = $lastReceived->copy()->addMinutes($pendingDeliveries * $intervalMinutes);
            $lockedUser->save();

            // Refresh the original user model with the updated data
            $user->refresh();

            return $packsToDeliver;
        });
    }

    /**
     * Calculate seconds remaining until the next pack delivery.
     */
    public function getSecondsUntilNextPack(User $user): int
    {
        $intervalMinutes = Setting::get('pack_delivery_interval_minutes', 240);

        $lastReceived = $user->last_pack_received_at ?? $user->created_at;
        $nextPackAt = $lastReceived->copy()->addMinutes($intervalMinutes);

        $now = now();

        if ($nextPackAt->lte($now)) {
            return 0;
        }

        return (int) $now->diffInSeconds($nextPackAt);
    }

    /**
     * Get the next pack delivery time for a user.
     */
    public function getNextPackAt(User $user): ?\Carbon\Carbon
    {
        $intervalMinutes = Setting::get('pack_delivery_interval_minutes', 240);

        $lastReceived = $user->last_pack_received_at ?? $user->created_at;

        return $lastReceived->copy()->addMinutes($intervalMinutes);
    }
}
