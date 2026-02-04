<?php

namespace Database\Seeders;

use App\Models\Sticker;
use App\Models\User;
use App\Models\UserSticker;
use Illuminate\Database\Seeder;

class UserStickerSeeder extends Seeder
{
    /**
     * Number of stickers glued to the album.
     */
    protected int $gluedCount = 25;

    /**
     * Number of stickers obtained but not glued.
     */
    protected int $ungluedCount = 5;

    /**
     * Number of duplicate stickers.
     */
    protected int $duplicateCount = 15;

    /**
     * Seed realistic sticker data for test users.
     */
    public function run(): void
    {
        $users = User::all();
        $allStickerIds = Sticker::pluck('id')->toArray();

        if (empty($allStickerIds)) {
            $this->command->warn('No stickers found. Please run StickerSeeder first.');

            return;
        }

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run UserSeeder first.');

            return;
        }

        $totalNeeded = $this->gluedCount + $this->ungluedCount;
        if (count($allStickerIds) < $totalNeeded) {
            $this->command->warn("Not enough stickers available. Need {$totalNeeded}, found ".count($allStickerIds).'.');

            return;
        }

        foreach ($users as $user) {
            $this->seedUserStickers($user, $allStickerIds);
        }

        $this->command->info("Seeded stickers for {$users->count()} users ({$this->gluedCount} glued, {$this->ungluedCount} unglued, {$this->duplicateCount} duplicates each).");
    }

    /**
     * Seed stickers for a single user.
     */
    protected function seedUserStickers(User $user, array $allStickerIds): void
    {
        // Shuffle to get random selection
        $shuffledIds = $allStickerIds;
        shuffle($shuffledIds);

        // Select stickers for glued (25) and unglued (5)
        $gluedStickerIds = array_slice($shuffledIds, 0, $this->gluedCount);
        $ungluedStickerIds = array_slice($shuffledIds, $this->gluedCount, $this->ungluedCount);

        // Create glued stickers
        foreach ($gluedStickerIds as $stickerId) {
            UserSticker::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'sticker_id' => $stickerId,
                    'is_glued' => true,
                ],
                [
                    'obtained_at' => now()->subDays(rand(1, 30)),
                ]
            );
        }

        // Create unglued stickers
        foreach ($ungluedStickerIds as $stickerId) {
            UserSticker::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'sticker_id' => $stickerId,
                    'is_glued' => false,
                ],
                [
                    'obtained_at' => now()->subDays(rand(1, 14)),
                ]
            );
        }

        // Create duplicates (random selection from stickers the user already has)
        $ownedStickerIds = array_merge($gluedStickerIds, $ungluedStickerIds);
        for ($i = 0; $i < $this->duplicateCount; $i++) {
            $duplicateStickerIdIndex = array_rand($ownedStickerIds);
            $duplicateStickerId = $ownedStickerIds[$duplicateStickerIdIndex];

            // Use unique timestamp to avoid constraint violation
            UserSticker::create([
                'user_id' => $user->id,
                'sticker_id' => $duplicateStickerId,
                'is_glued' => false,
                'obtained_at' => now()->subDays(rand(1, 7))->subSeconds($i + rand(1, 3600)),
            ]);
        }
    }
}
