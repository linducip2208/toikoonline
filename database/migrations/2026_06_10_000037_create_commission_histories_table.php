<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->foreignId('order_detail_id')->constrained('order_details');
            $table->foreignId('seller_id')->constrained('users');
            $table->decimal('admin_commission', 25, 2);
            $table->decimal('seller_earning', 25, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_histories');
    }
};
