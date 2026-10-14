<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
        public function run(): void
    {
        $this->call([
            UserSeeder::class,       // ← NEW: staff + supervisor users
            RoomSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}