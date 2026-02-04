<?php

namespace Database\Seeders;

use App\Enums\StickerRarity;
use App\Models\Sticker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class StickerSeeder extends Seeder
{
    /**
     * Source directory for sticker images.
     */
    protected string $sourceDirectory = 'database/data/stickers';

    /**
     * JSON file with sticker data.
     */
    protected string $dataFile = 'database/data/stickers.json';

    /**
     * Target directory in public storage.
     */
    protected string $targetDirectory = 'stickers';

    /**
     * Default sticker dimensions.
     */
    protected int $defaultWidth = 100;

    protected int $defaultHeight = 140;

    /**
     * Seed the album stickers.
     */
    public function run(): void
    {
        $dataPath = base_path($this->dataFile);

        if (! File::exists($dataPath)) {
            $this->command->error("Data file not found: {$this->dataFile}");

            return;
        }

        $stickersData = json_decode(File::get($dataPath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error('Invalid JSON in stickers data file.');

            return;
        }

        // Index sticker data by number for quick lookup
        $stickersData = collect($stickersData)->keyBy('number');

        $sourcePath = base_path($this->sourceDirectory);
        $imageFiles = collect();

        if (File::isDirectory($sourcePath)) {
            $files = File::files($sourcePath);
            $imageFiles = collect($files)
                ->filter(fn ($file) => in_array(
                    strtolower($file->getExtension()),
                    ['webp', 'jpg', 'jpeg', 'png', 'gif']
                ))
                ->keyBy(fn ($file) => (int) pathinfo($file->getFilename(), PATHINFO_FILENAME));
        }

        // Ensure target directory exists
        Storage::disk('public')->makeDirectory($this->targetDirectory);

        $count = 0;
        foreach ($stickersData as $stickerNumber => $data) {
            $file = $imageFiles->get($stickerNumber);
            $imagePath = null;
            $isHorizontal = false;
            $width = $this->defaultWidth;
            $height = $this->defaultHeight;

            if ($file) {
                $filename = $file->getFilename();

                // Copy image to public storage
                $targetPath = "{$this->targetDirectory}/{$filename}";
                Storage::disk('public')->put($targetPath, File::get($file->getPathname()));
                $imagePath = $targetPath;

                // Detect orientation from image dimensions
                $imageSize = @getimagesize($file->getPathname());
                if ($imageSize !== false) {
                    $width = $imageSize[0];
                    $height = $imageSize[1];
                    $isHorizontal = $width > $height;
                }
            }

            // Create or update sticker record (idempotent)
            Sticker::updateOrCreate(
                ['number' => $stickerNumber],
                [
                    'name' => $data['name'] ?: "Sticker {$stickerNumber}",
                    'page_number' => $data['page_number'],
                    'position_x' => $data['position_x'] ?? 0,
                    'position_y' => $data['position_y'] ?? 0,
                    'width' => $width,
                    'height' => $height,
                    'is_horizontal' => $isHorizontal,
                    'rarity' => StickerRarity::tryFrom($data['rarity'] ?? 'common') ?? StickerRarity::Common,
                    'image_path' => $imagePath,
                ]
            );

            $count++;
        }

        $withImages = $imageFiles->count();
        $this->command->info("Seeded {$count} stickers ({$withImages} with images).");
    }
}
