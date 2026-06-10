<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seller_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->longText('variation')->nullable();
            $table->decimal('price', 20, 2)->nullable();
            $table->decimal('coupon_discount', 20, 2)->default(0);
            $table->decimal('tax', 20, 2)->default(0);
            $table->decimal('shipping_cost', 20, 2)->default(0);
            $table->integer('quantity')->nullable();
            $table->string('payment_status', 10)->default('unpaid');
            $table->string('delivery_status', 20)->default('pending');
            $table->integer('refund_days')->default(0);
            $table->boolean('reviewed')->default(false);
            $table->string('shipping_type', 255)->nullable();
            $table->foreignId('pickup_point_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_referral_code', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
