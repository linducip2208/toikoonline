<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_coupons', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('coupon_id')->constrained('coupons');
            $table->string('coupon_code', 255);
            $table->decimal('min_buy', 20, 2);
            $table->integer('validation_days');
            $table->decimal('discount', 20, 2);
            $table->string('discount_type', 20);
            $table->integer('expiry_date');
        });

        DB::statement('ALTER TABLE user_coupons CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    }

    public function down(): void
    {
        Schema::dropIfExists('user_coupons');
    }
};
