<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PageSeeder extends Seeder
{
    /**
     * Source directory for page images.
     */
    protected string $sourceDirectory = 'database/data/pages';

    /**
     * Target directory in public storage.
     */
    protected string $targetDirectory = 'pages';

    /**
     * Seed the album pages.
     */
    public function run(): void
    {
        $sourcePath = base_path($this->sourceDirectory);

        if (! File::isDirectory($sourcePath)) {
            $this->command->warn("Source directory not found: {$this->sourceDirectory}");
            $this->command->info('Please create the directory and add page images (1.webp, 2.webp, etc.)');

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
            ->sortBy(fn ($file) => (int) pathinfo($file->getFilename(), PATHINFO_FILENAME))
            ->values();

        if ($imageFiles->isEmpty()) {
            $this->command->warn('No valid image files found.');

            return;
        }

        // Ensure target directory exists
        Storage::disk('public')->makeDirectory($this->targetDirectory);

        $count = 0;
        foreach ($imageFiles as $file) {
            $filename = $file->getFilename();
            $pageNumber = (int) pathinfo($filename, PATHINFO_FILENAME);

            if ($pageNumber <= 0) {
                $this->command->warn("Skipping invalid filename: {$filename}");

                continue;
            }

            // Copy image to public storage
            $targetPath = "{$this->targetDirectory}/{$filename}";
            Storage::disk('public')->put($targetPath, File::get($file->getPathname()));

            // Create or update page record (idempotent)
            Page::updateOrCreate(
                ['number' => $pageNumber],
                ['image_path' => $targetPath]
            );

            $count++;
        }

        $this->command->info("Seeded {$count} album pages.");
    }
}
