<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Fixed demo accounts ──────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'alice@toolshed.test'],
            [
                'name'              => 'Alice Admin',
                'password'          => Hash::make('Password1!'),
                'birth_year'        => 1985,
                'role'              => 'admin',
                'email_verified_at' => now(),
            ],
        );

        User::firstOrCreate(
            ['email' => 'bob@toolshed.test'],
            [
                'name'              => 'Bob Builder',
                'password'          => Hash::make('Password1!'),
                'birth_year'        => 1990,
                'role'              => 'staff',
                'email_verified_at' => now(),
            ],
        );

        User::firstOrCreate(
            ['email' => 'carol@toolshed.test'],
            [
                'name'              => 'Carol Contractor',
                'password'          => Hash::make('Password1!'),
                'birth_year'        => 1978,
                'role'              => 'renter',
                'email_verified_at' => now(),
            ],
        );

        // ── Random members ───────────────────────────────────────────────────
        $existingCount = User::count();
        $needed = max(0, 20 - $existingCount);
        if ($needed > 0) {
            User::factory($needed)->create();
        }
    }
}
