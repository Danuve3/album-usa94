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
     * Target directory in public storage.
     */
    protected string $targetDirectory = 'stickers';

    /**
     * Total number of stickers in the album.
     */
    protected int $totalStickers = 444;

    /**
     * Stickers per page (approximate for initial distribution).
     */
    protected int $stickersPerPage = 7;

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
        $sourcePath = base_path($this->sourceDirectory);

        if (! File::isDirectory($sourcePath)) {
            $this->command->warn("Source directory not found: {$this->sourceDirectory}");
            $this->command->info('Please create the directory and add sticker images (1.webp, 2.webp, etc.)');

            return;
        }

        $files = File::files($sourcePath);

        if (empty($files)) {
            $this->command->warn('No image files found in source directory.');

            return;
        }

        // Filter and sort image files by numeric filename
        $imageFiles = collect($files)
            ->filter(fn ($file) => in_array(
                strtolower($file->getExtension()),
                ['webp', 'jpg', 'jpeg', 'png', 'gif']
            ))
            ->keyBy(fn ($file) => (int) pathinfo($file->getFilename(), PATHINFO_FILENAME));

        if ($imageFiles->isEmpty()) {
            $this->command->warn('No valid image files found.');

            return;
        }

        // Ensure target directory exists
        Storage::disk('public')->makeDirectory($this->targetDirectory);

        $count = 0;
        for ($stickerNumber = 1; $stickerNumber <= $this->totalStickers; $stickerNumber++) {
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

            // Calculate page number based on sticker number
            $pageNumber = $this->calculatePageNumber($stickerNumber);

            // Create or update sticker record (idempotent)
            Sticker::updateOrCreate(
                ['number' => $stickerNumber],
                [
                    'name' => "Sticker {$stickerNumber}",
                    'page_number' => $pageNumber,
                    'position_x' => 0,
                    'position_y' => 0,
                    'width' => $width,
                    'height' => $height,
                    'is_horizontal' => $isHorizontal,
                    'rarity' => StickerRarity::Common,
                    'image_path' => $imagePath,
                ]
            );

            $count++;
        }

        $withImages = $imageFiles->count();
        $this->command->info("Seeded {$count} stickers ({$withImages} with images).");
    }

    /**
     * Calculate the page number for a given sticker number.
     *
     * This provides a simple initial distribution.
     * Page assignments can be adjusted later via the admin panel.
     */
    protected function calculatePageNumber(int $stickerNumber): int
    {
        // Simple calculation: distribute stickers across pages
        // Stickers 1-7 go to page 1, 8-14 to page 2, etc.
        return (int) ceil($stickerNumber / $this->stickersPerPage);
    }
}
