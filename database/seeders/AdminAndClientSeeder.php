<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminAndClientSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Super Admin',
                'role' => 'superadmin',
                'status' => 'active',
                'subscription_tier' => 'pro',
                'subscription_expires_at' => now()->addYears(3),
                'password' => Hash::make('Admin@123'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin.content@ielts-helper.local'],
            [
                'name' => 'Content Admin',
                'role' => 'admin',
                'status' => 'active',
                'subscription_tier' => 'free',
                'password' => Hash::make('Admin@123'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin.ops@ielts-helper.local'],
            [
                'name' => 'Operation Admin',
                'role' => 'admin',
                'status' => 'active',
                'subscription_tier' => 'free',
                'password' => Hash::make('Admin@123'),
            ]
        );

        $proEmails = [];
        for ($index = 1; $index <= 25; $index++) {
            $email = "student{$index}@ielts-helper.local";
            $isPro = $index <= 10;
            $isLocked = $index % 9 === 0;

            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => "Student {$index}",
                    'role' => 'user',
                    'status' => $isLocked ? 'locked' : 'active',
                    'subscription_tier' => $isPro ? 'pro' : 'free',
                    'subscription_expires_at' => $isPro ? now()->addDays(20 + $index) : null,
                    'password' => Hash::make('Student@123'),
                    'email_verified_at' => now(),
                ]
            );

            if ($isPro) {
                $proEmails[] = $email;
            }
        }
    }
}
