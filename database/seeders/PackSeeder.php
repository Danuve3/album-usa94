<?php

namespace Database\Seeders;

use App\Models\Pack;
use App\Models\User;
use Illuminate\Database\Seeder;

class PackSeeder extends Seeder
{
    private const INITIAL_PACKS_PER_USER = 5;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            for ($i = 0; $i < self::INITIAL_PACKS_PER_USER; $i++) {
                Pack::create([
                    'user_id' => $user->id,
                    'opened_at' => null,
                ]);
            }
        }
    }
}
