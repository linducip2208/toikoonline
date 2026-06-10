<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refund_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('order_id')->constrained('orders');
            $table->foreignId('order_detail_id')->constrained('order_details');
            $table->string('refund_status', 20)->default('pending');
            $table->string('refund_label', 255)->nullable();
            $table->text('refund_reason')->nullable();
            $table->decimal('refund_amount', 20, 2)->nullable();
            $table->string('reject_reason', 255)->nullable();
            $table->boolean('admin_seen')->default(false);
            $table->foreignId('refund_staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refund_requests');
    }
};
