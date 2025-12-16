<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Users
        User::create([
            'name' => 'Pak Budi',
            'email' => 'owner@sahabat-farm.com',
            'password' => bcrypt('password'),
            'role' => 'OWNER',
        ]);

        User::create([
            'name' => 'Mas Joko',
            'email' => 'staff@sahabat-farm.com',
            'password' => bcrypt('password'),
            'role' => 'STAFF',
        ]);

        // 2. Call SOP Seeder for Master Data (Breeds, Categories, Diseases, Inventory)
        $this->call(SopSeeder::class);

        // 3. Call Real Time Farm Simulation
        $this->call(RealTimeFarmSeeder::class);
    }
}
