<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TransactionService
{
    public function getTransactions(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Transaction::query()->with(['user', 'plan']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function updateStatus(Transaction $transaction, string $status): Transaction
    {
        $transaction->update(['status' => $status]);

        if ($status === 'success' && $transaction->user && $transaction->plan) {
            $expiresAt = now();
            if ($transaction->user->subscription_expires_at && $transaction->user->subscription_expires_at->isFuture()) {
                $expiresAt = $transaction->user->subscription_expires_at;
            }

            $transaction->user->update([
                'subscription_tier' => 'pro',
                'subscription_expires_at' => $expiresAt->copy()->addDays($transaction->plan->duration_days),
            ]);
        }

        return $transaction->fresh(['user', 'plan']);
    }

    public function deleteTransaction(Transaction $transaction): void
    {
        $transaction->delete();
    }
}
