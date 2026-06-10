<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('guest_id')->nullable();
            $table->foreignId('seller_id')->nullable()->constrained('users')->nullOnDelete();
            $table->longText('shipping_address')->nullable();
            $table->longText('billing_address')->nullable();
            $table->longText('additional_info')->nullable();
            $table->string('shipping_method', 255)->nullable();
            $table->string('delivery_status', 20)->default('pending');
            $table->string('payment_type', 20)->nullable();
            $table->string('payment_status', 20)->default('unpaid');
            $table->longText('payment_details')->nullable();
            $table->string('code', 255)->nullable()->unique();
            $table->bigInteger('date')->nullable();
            $table->decimal('coupon_discount', 20, 2)->default(0);
            $table->decimal('discount', 20, 2)->default(0);
            $table->string('discount_type', 10)->nullable();
            $table->decimal('grand_total', 20, 2)->default(0);
            $table->string('coupon_code', 255)->nullable();
            $table->decimal('tax_amount', 20, 2)->default(0);
            $table->boolean('commission_calculated')->default(false);
            $table->boolean('manual_payment')->default(false);
            $table->text('manual_payment_data')->nullable();
            $table->boolean('view')->default(false);
            $table->boolean('delivery_viewed')->default(false);
            $table->boolean('payment_status_viewed')->default(false);
            $table->foreignId('assign_delivery_boy')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pickup_point_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('carrier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('order_from', 20)->default('web');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
