<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('gateway_id')->nullable()->constrained('payment_gateway_configs')->nullOnDelete();
            $table->string('order_code')->index();
            $table->unsignedBigInteger('amount');
            $table->string('currency', 8)->default('IDR');
            $table->string('status', 32)->default('intent');
            $table->string('idempotency_key')->unique();
            $table->string('gateway_reference')->nullable()->index();
            $table->string('redirect_url', 1024)->nullable();
            $table->json('raw')->nullable();
            $table->unsignedInteger('failed_signature_count')->default(0);
            $table->timestamps();
            $table->unique(['order_code', 'gateway_id']);
        });

        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_transaction_id')->nullable()->constrained('payment_transactions')->nullOnDelete();
            $table->string('gateway_id_raw', 64)->nullable();
            $table->string('event', 64)->default('webhook');
            $table->boolean('signature_valid')->default(false);
            $table->string('mapped_status', 32)->nullable();
            $table->string('gateway_status_raw', 64)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('webhook_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('url', 1024);
            $table->string('event', 64)->index();
            $table->string('secret', 255);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('webhook_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('webhook_subscriptions')->cascadeOnDelete();
            $table->string('event', 64)->index();
            $table->json('payload')->nullable();
            $table->string('status', 32)->default('pending');
            $table->unsignedInteger('attempts')->default(0);
            $table->text('response')->nullable();
            $table->timestamp('next_retry_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_deliveries');
        Schema::dropIfExists('webhook_subscriptions');
        Schema::dropIfExists('payment_logs');
        Schema::dropIfExists('payment_transactions');
    }
};
