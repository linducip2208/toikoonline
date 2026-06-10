<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('starting_bid', 20, 2);
            $table->decimal('current_bid', 20, 2)->nullable();
            $table->decimal('buy_now_price', 20, 2)->nullable();
            $table->decimal('bid_increment', 20, 2)->default(1000);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('status', 20)->default('pending');
            $table->foreignId('winner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('auction_bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 20, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auction_bids');
        Schema::dropIfExists('auctions');
    }
};
