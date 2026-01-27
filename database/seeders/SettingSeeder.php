<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Default system settings.
     *
     * @var array<string, array{value: mixed, type: string, name: string, description: string, group: string}>
     */
    protected array $settings = [
        'packs_per_day' => [
            'value' => 5,
            'type' => 'integer',
            'name' => 'Sobres por día',
            'description' => 'Cantidad de sobres que recibe cada usuario diariamente.',
            'group' => 'packs',
        ],
        'stickers_per_pack' => [
            'value' => 5,
            'type' => 'integer',
            'name' => 'Cromos por sobre',
            'description' => 'Cantidad de cromos que contiene cada sobre al abrirlo.',
            'group' => 'packs',
        ],
        'shiny_probability' => [
            'value' => 0.05,
            'type' => 'float',
            'name' => 'Probabilidad de cromo brillante',
            'description' => 'Probabilidad (0-1) de obtener un cromo brillante. Ejemplo: 0.05 = 5%.',
            'group' => 'stickers',
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
                    'name' => $config['name'],
                    'description' => $config['description'],
                    'group' => $config['group'],
                ]
            );
        }

        Setting::clearCache();

        $this->command->info('Seeded '.count($this->settings).' system settings.');
    }
}
