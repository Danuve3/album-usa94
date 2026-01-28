<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Create test users.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'user1@example.com'],
            [
                'name' => 'Usuario 1',
                'password' => Hash::make('password'),
                'is_admin' => false,
            ]
        );

        User::firstOrCreate(
            ['email' => 'user2@example.com'],
            [
                'name' => 'Usuario 2',
                'password' => Hash::make('password'),
                'is_admin' => false,
            ]
        );
    }
}
