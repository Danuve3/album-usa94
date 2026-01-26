<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Default system settings.
     *
     * @var array<string, array{value: mixed, type: string}>
     */
    protected array $settings = [
        'packs_per_day' => [
            'value' => 5,
            'type' => 'integer',
        ],
        'stickers_per_pack' => [
            'value' => 5,
            'type' => 'integer',
        ],
        'shiny_probability' => [
            'value' => 0.05,
            'type' => 'float',
        ],
    ];

    /**
     * Seed the system settings.
     */
    public function run(): void
    {
        foreach ($this->settings as $key => $config) {
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => (string) $config['value'],
                    'type' => $config['type'],
                ]
            );
        }

        Setting::clearCache();

        $this->command->info('Seeded '.count($this->settings).' system settings.');
    }
}
