<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the users table with a default superadmin account.
     */
    public function run(): void
    {
        $this->call(AdminAndClientSeeder::class);
    }
}
