<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $plans = Plan::query()->orderBy('id')->get();
        $clients = User::query()->where('role', 'user')->orderBy('id')->get();

        foreach ($clients as $index => $client) {
            $plan = $plans[$index % max($plans->count(), 1)] ?? null;
            $status = match (true) {
                $index % 5 === 0 => 'pending',
                $index % 7 === 0 => 'failed',
                default => 'success',
            };

            Transaction::updateOrCreate(
                ['transaction_code' => 'TXN-'.str_pad((string) ($index + 1), 5, '0', STR_PAD_LEFT)],
                [
                    'user_id' => $client->id,
                    'plan_id' => $plan?->id,
                    'amount' => $plan?->price ?? 199000,
                    'payment_method' => $index % 2 === 0 ? 'vietqr' : 'card',
                    'status' => $status,
                    'created_at' => now()->subDays(rand(1, 45)),
                    'updated_at' => now()->subDays(rand(0, 15)),
                ]
            );
        }
    }
}
