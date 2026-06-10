<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('variant', 255);
            $table->string('sku', 255)->unique()->nullable();
            $table->decimal('price', 20, 2)->default(0);
            $table->integer('qty')->default(0);
            $table->foreignId('image')->nullable()->constrained('uploads')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_stocks');
    }
};
