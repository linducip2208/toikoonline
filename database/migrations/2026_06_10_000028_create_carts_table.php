<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->string('variation', 255)->nullable();
            $table->decimal('price', 20, 2)->default(0);
            $table->decimal('tax', 20, 2)->default(0);
            $table->decimal('shipping_cost', 20, 2)->default(0);
            $table->integer('quantity')->default(1);
            $table->integer('owner_id')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
