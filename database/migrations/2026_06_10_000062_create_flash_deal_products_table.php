<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flash_deal_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flash_deal_id')->constrained('flash_deals')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('discount', 20, 2)->default(0);
            $table->string('discount_type', 20)->nullable();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE flash_deal_products CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    }

    public function down(): void
    {
        Schema::dropIfExists('flash_deal_products');
    }
};
