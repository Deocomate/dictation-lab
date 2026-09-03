<?php

use App\Models\Plan;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    config([
        'services.sepay.merchant_id' => 'SP-TEST-CODE',
        'services.sepay.secret_key' => 'sepay-test-secret',
        'services.sepay.environment' => 'sandbox',
    ]);
});

function sepayPayload(Transaction $transaction, array $overrides = []): array
{
    return array_replace_recursive([
        'timestamp' => now()->timestamp,
        'notification_type' => 'ORDER_PAID',
        'order' => [
            'id' => 'order-id',
            'order_id' => 'NQD-TEST',
            'order_status' => 'CAPTURED',
            'order_currency' => 'VND',
            'order_amount' => number_format((float) $transaction->amount, 2, '.', ''),
            'order_invoice_number' => $transaction->transaction_code,
            'custom_data' => [],
            'user_agent' => 'Feature test',
            'ip_address' => '127.0.0.1',
            'order_description' => 'Feature test payment',
        ],
        'transaction' => [
            'id' => 'transaction-id',
            'payment_method' => 'BANK_TRANSFER',
            'transaction_id' => 'sepay-transaction-id',
            'transaction_type' => 'PAYMENT',
            'transaction_date' => now()->format('Y-m-d H:i:s'),
            'transaction_status' => 'APPROVED',
            'transaction_amount' => (string) (int) round((float) $transaction->amount),
            'transaction_currency' => 'VND',
        ],
        'customer' => null,
        'agreement' => null,
    ], $overrides);
}

test('user can create a sepay checkout transaction without calling payos', function () {
    $user = User::factory()->create([
        'role' => 'user',
        'status' => 'active',
        'subscription_tier' => 'free',
        'subscription_expires_at' => null,
    ]);
    $plan = Plan::query()->create([
        'name' => 'Pro 1 Month',
        'duration_days' => 30,
        'price' => 199000,
        'is_active' => true,
    ]);

    $this->actingAs($user)->post(route('client.checkout.process'), [
        'plan_id' => $plan->id,
        'payment_method' => 'sepay',
    ])->assertOk()
        ->assertViewIs('client.checkout.sepay-redirect')
        ->assertSee('https://pay-sandbox.sepay.vn/v1/checkout/init', false)
        ->assertSee('SP-TEST-CODE', false);

    $transaction = Transaction::query()->firstOrFail();

    expect($transaction->payment_method)->toBe('sepay')
        ->and($transaction->status)->toBe('pending')
        ->and($transaction->transaction_code)->toStartWith('SP-')
        ->and((int) $transaction->amount)->toBe(199000);
});

test('checkout page renders plan details and payment summary', function () {
    $user = User::factory()->create([
        'role' => 'user',
        'status' => 'active',
        'subscription_tier' => 'free',
        'subscription_expires_at' => null,
    ]);
    Plan::query()->create([
        'name' => 'Pro 1 Month',
        'duration_days' => 30,
        'price' => 199000,
        'is_active' => true,
    ]);

    $this->actingAs($user)->get(route('client.checkout'))
        ->assertOk()
        ->assertSee('Nâng cấp tài khoản Pro')
        ->assertSee('Pro 1 Month')
        ->assertDontSee('PayOS Checkout');
});

test('sepay ipn rejects invalid secret key', function () {
    $user = User::factory()->create([
        'role' => 'user',
        'status' => 'active',
        'subscription_tier' => 'free',
        'subscription_expires_at' => null,
    ]);
    $plan = Plan::query()->create(['name' => 'Pro', 'duration_days' => 30, 'price' => 199000, 'is_active' => true]);
    $transaction = Transaction::query()->create([
        'user_id' => $user->id,
        'plan_id' => $plan->id,
        'amount' => $plan->price,
        'payment_method' => 'sepay',
        'transaction_code' => 'SP-INVALID-SECRET',
        'status' => 'pending',
    ]);

    $this->postJson(route('client.checkout.sepay.ipn'), sepayPayload($transaction), [
        'X-Secret-Key' => 'wrong-secret',
    ])->assertUnauthorized();

    expect($transaction->fresh()->status)->toBe('pending')
        ->and($user->fresh()->subscription_tier)->toBe('free');
});

test('sepay ipn confirms transaction and upgrades subscription', function () {
    $this->travelTo(now()->startOfSecond());

    $user = User::factory()->create([
        'role' => 'user',
        'status' => 'active',
        'subscription_tier' => 'free',
        'subscription_expires_at' => null,
    ]);
    $plan = Plan::query()->create(['name' => 'Pro', 'duration_days' => 30, 'price' => 199000, 'is_active' => true]);
    $transaction = Transaction::query()->create([
        'user_id' => $user->id,
        'plan_id' => $plan->id,
        'amount' => $plan->price,
        'payment_method' => 'sepay',
        'transaction_code' => 'SP-SUCCESS',
        'status' => 'pending',
    ]);

    $this->postJson(route('client.checkout.sepay.ipn'), sepayPayload($transaction), [
        'X-Secret-Key' => 'sepay-test-secret',
    ])->assertOk()
        ->assertJsonPath('transaction_id', $transaction->id);

    $transaction->refresh();
    $user->refresh();

    expect($transaction->status)->toBe('success')
        ->and($transaction->paid_at?->timestamp)->toBe(now()->timestamp)
        ->and($transaction->gateway_payload['source'])->toBe('sepay_ipn')
        ->and($user->subscription_tier)->toBe('pro')
        ->and($user->subscription_expires_at?->toDateString())->toBe(now()->addDays(30)->toDateString());
});

test('sepay duplicate success ipn does not extend subscription twice', function () {
    $user = User::factory()->create([
        'role' => 'user',
        'status' => 'active',
        'subscription_tier' => 'free',
        'subscription_expires_at' => null,
    ]);
    $plan = Plan::query()->create(['name' => 'Pro', 'duration_days' => 30, 'price' => 199000, 'is_active' => true]);
    $transaction = Transaction::query()->create([
        'user_id' => $user->id,
        'plan_id' => $plan->id,
        'amount' => $plan->price,
        'payment_method' => 'sepay',
        'transaction_code' => 'SP-DUPLICATE',
        'status' => 'pending',
    ]);

    $payload = sepayPayload($transaction);

    $this->postJson(route('client.checkout.sepay.ipn'), $payload, ['X-Secret-Key' => 'sepay-test-secret'])
        ->assertOk();
    $firstExpiry = $user->fresh()->subscription_expires_at;

    $this->postJson(route('client.checkout.sepay.ipn'), $payload, ['X-Secret-Key' => 'sepay-test-secret'])
        ->assertOk();

    expect($user->fresh()->subscription_expires_at?->timestamp)->toBe($firstExpiry?->timestamp);
});

test('sepay amount mismatch keeps transaction pending', function () {
    $user = User::factory()->create([
        'role' => 'user',
        'status' => 'active',
        'subscription_tier' => 'free',
        'subscription_expires_at' => null,
    ]);
    $plan = Plan::query()->create(['name' => 'Pro', 'duration_days' => 30, 'price' => 199000, 'is_active' => true]);
    $transaction = Transaction::query()->create([
        'user_id' => $user->id,
        'plan_id' => $plan->id,
        'amount' => $plan->price,
        'payment_method' => 'sepay',
        'transaction_code' => 'SP-AMOUNT-MISMATCH',
        'status' => 'pending',
    ]);

    $this->postJson(route('client.checkout.sepay.ipn'), sepayPayload($transaction, [
        'order' => ['order_amount' => '1000.00'],
        'transaction' => ['transaction_amount' => '1000'],
    ]), ['X-Secret-Key' => 'sepay-test-secret'])->assertOk();

    expect($transaction->fresh()->status)->toBe('pending')
        ->and($user->fresh()->subscription_tier)->toBe('free');
});

test('pending and failed checkout views render with transaction data', function () {
    $user = User::factory()->create([
        'role' => 'user',
        'status' => 'active',
        'subscription_tier' => 'free',
        'subscription_expires_at' => null,
    ]);
    $plan = Plan::query()->create(['name' => 'Pro', 'duration_days' => 30, 'price' => 199000, 'is_active' => true]);
    $transaction = Transaction::query()->create([
        'user_id' => $user->id,
        'plan_id' => $plan->id,
        'amount' => $plan->price,
        'payment_method' => 'sepay',
        'transaction_code' => 'SP-VIEWS',
        'status' => 'pending',
    ]);

    $this->actingAs($user)->get(route('client.checkout.pending', $transaction))
        ->assertOk()
        ->assertSee('SP-VIEWS');

    $transaction->update(['status' => 'failed']);

    $this->actingAs($user)->get(route('client.checkout.failed', $transaction))
        ->assertOk()
        ->assertSee(route('client.checkout'), false);
});
