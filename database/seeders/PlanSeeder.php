<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $plans = [
            ['name' => 'Pro 1 Tháng', 'duration_days' => 30, 'price' => 199000, 'is_active' => true],
            ['name' => 'Pro 6 Tháng', 'duration_days' => 180, 'price' => 999000, 'is_active' => true],
            ['name' => 'Pro 12 Tháng', 'duration_days' => 365, 'price' => 1699000, 'is_active' => true],
            ['name' => 'Pro Trial 7 Ngày', 'duration_days' => 7, 'price' => 49000, 'is_active' => false],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['name' => $plan['name']], $plan);
        }
    }
}
