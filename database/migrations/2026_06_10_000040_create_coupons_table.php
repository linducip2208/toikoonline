<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type', 255);
            $table->string('code', 255)->unique();
            $table->longText('details');
            $table->decimal('discount', 20, 2);
            $table->string('discount_type', 100);
            $table->integer('start_date')->nullable();
            $table->integer('end_date')->nullable();
            $table->decimal('min_buy', 20, 2)->default(0);
            $table->decimal('max_discount', 20, 2)->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
