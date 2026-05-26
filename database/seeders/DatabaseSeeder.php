<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Period;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat Periode Aktif
        $period = Period::create([
            'name' => '1447 H / 2026',
            'is_active' => true,
            'cow_patungan_cost' => 3500000,
            'goat_operational_cost' => 250000,
        ]);

        // 2. Buat Dummy Users per Role
        User::create([
            'name' => 'Ari Admin',
            'email' => 'admin@qurban.test',
            'password' => Hash::make('password'),
            'role' => 'Admin',
        ]);

        User::create([
            'name' => 'Yahya Bendahara',
            'email' => 'bendahara@qurban.test',
            'password' => Hash::make('password'),
            'role' => 'Bendahara',
        ]);

        User::create([
            'name' => 'Bang Jagal',
            'email' => 'jagal@qurban.test',
            'password' => Hash::make('password'),
            'role' => 'Jagal',
        ]);

        User::create([
            'name' => 'Gatekeeper Masjid',
            'email' => 'gatekeeper@qurban.test',
            'password' => Hash::make('password'),
            'role' => 'Gatekeeper',
        ]);
    }
}
