<?php

namespace App\Services\Client;

use App\Models\Plan;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Str;

class CheckoutService
{
    /**
     * Get an active plan by ID.
     */
    public function getPlan(int $planId): Plan
    {
        return Plan::where('is_active', true)->findOrFail($planId);
    }

    /**
     * Get all active plans for checkout page.
     */
    public function getActivePlans()
    {
        return Plan::where('is_active', true)->orderBy('price')->get();
    }

    /**
     * Create a pending transaction.
     */
    public function createPendingTransaction(User $user, Plan $plan, string $paymentMethod, string $codePrefix = 'TXN'): Transaction
    {
        return Transaction::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'amount' => $plan->price,
            'payment_method' => $paymentMethod,
            'transaction_code' => strtoupper($codePrefix).'-'.strtoupper(Str::random(12)),
            'status' => 'pending',
        ]);
    }

    /**
     * Confirm a transaction and activate the user's subscription.
     */
    public function confirmTransaction(Transaction $transaction, ?array $gatewayPayload = null): Transaction
    {
        if ($transaction->status === 'success') {
            return $transaction->fresh(['user', 'plan']);
        }

        $transaction->update([
            'status' => 'success',
            'paid_at' => now(),
            'gateway_payload' => $gatewayPayload ?? $transaction->gateway_payload,
        ]);

        $plan = $transaction->plan;
        $user = $transaction->user;

        if ($plan !== null && $user !== null) {
            $expiresAt = $user->subscription_expires_at && $user->subscription_expires_at->isFuture()
                ? $user->subscription_expires_at->copy()->addDays($plan->duration_days)
                : now()->addDays($plan->duration_days);

            $user->update([
                'subscription_tier' => 'pro',
                'subscription_expires_at' => $expiresAt,
            ]);
        }

        return $transaction->fresh();
    }

    /**
     * Mark a transaction as failed.
     */
    public function failTransaction(Transaction $transaction, ?array $gatewayPayload = null): Transaction
    {
        if ($transaction->status === 'success') {
            return $transaction->fresh();
        }

        $transaction->update([
            'status' => 'failed',
            'gateway_payload' => $gatewayPayload ?? $transaction->gateway_payload,
        ]);

        return $transaction->fresh();
    }
}
