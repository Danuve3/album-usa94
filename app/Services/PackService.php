<?php

namespace App\Services;

use App\Enums\StickerRarity;
use App\Models\Pack;
use App\Models\Setting;
use App\Models\Sticker;
use App\Models\UserSticker;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PackService
{
    /**
     * Default probability percentage for shiny stickers.
     */
    private const DEFAULT_SHINY_PROBABILITY = 5;

    /**
     * Open a pack and generate random stickers for the user.
     *
     * @return Collection<int, UserSticker>
     *
     * @throws \InvalidArgumentException If the pack is already opened
     * @throws \RuntimeException If there are no stickers available
     */
    public function open(Pack $pack): Collection
    {
        if ($pack->opened_at !== null) {
            throw new \InvalidArgumentException('Pack is already opened.');
        }

        $stickersCount = Sticker::count();
        if ($stickersCount === 0) {
            throw new \RuntimeException('No stickers available in the database.');
        }

        return DB::transaction(function () use ($pack) {
            $userStickers = new Collection;
            $shinyProbability = Setting::get('shiny_probability', self::DEFAULT_SHINY_PROBABILITY);
            $openedAt = now();

            // Get existing sticker IDs for this user to detect duplicates
            $existingStickerIds = UserSticker::where('user_id', $pack->user_id)
                ->pluck('sticker_id')
                ->toArray();

            for ($i = 0; $i < Pack::STICKERS_PER_PACK; $i++) {
                $isShiny = random_int(1, 100) <= $shinyProbability;
                $sticker = $this->getRandomSticker($isShiny);

                // Check if this sticker is a duplicate
                $isDuplicate = in_array($sticker->id, $existingStickerIds);

                $userSticker = UserSticker::create([
                    'user_id' => $pack->user_id,
                    'sticker_id' => $sticker->id,
                    'is_glued' => false,
                    'obtained_at' => $openedAt->copy()->addSeconds($i),
                ]);

                // Add custom attribute to indicate if it's a duplicate
                $userSticker->is_duplicate = $isDuplicate;

                // Add to existing IDs for subsequent checks within same pack
                $existingStickerIds[] = $sticker->id;

                $userStickers->push($userSticker);
            }

            $pack->update(['opened_at' => $openedAt]);

            return $userStickers;
        });
    }

    /**
     * Get a random sticker, optionally filtering by shiny rarity.
     */
    private function getRandomSticker(bool $shiny): Sticker
    {
        $query = Sticker::query();

        if ($shiny) {
            $shinySticker = $query->where('rarity', StickerRarity::Shiny)->inRandomOrder()->first();
            if ($shinySticker) {
                return $shinySticker;
            }
        }

        return Sticker::inRandomOrder()->first();
    }
}
