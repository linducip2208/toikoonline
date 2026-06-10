<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('tax_id')->constrained('taxes');
            $table->decimal('tax', 20, 2);
            $table->string('tax_type', 10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_taxes');
    }
};
