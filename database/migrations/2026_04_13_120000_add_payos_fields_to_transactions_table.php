<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('gateway_order_code')->nullable()->unique()->after('transaction_code');
            $table->text('checkout_url')->nullable()->after('gateway_order_code');
            $table->text('qr_code')->nullable()->after('checkout_url');
            $table->json('gateway_payload')->nullable()->after('qr_code');
            $table->timestamp('paid_at')->nullable()->after('gateway_payload');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['gateway_order_code', 'checkout_url', 'qr_code', 'gateway_payload', 'paid_at']);
        });
    }
};
