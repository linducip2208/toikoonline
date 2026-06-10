<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('type', 10)->default('real');
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('custom_reviewer_name', 100)->nullable();
            $table->string('custom_reviewer_image', 100)->nullable();
            $table->integer('rating')->default(0);
            $table->mediumText('comment');
            $table->string('photos', 191)->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('viewed')->default(false);
            $table->boolean('created_at_is_custom')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
