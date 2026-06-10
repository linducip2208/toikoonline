<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->foreignId('category_id')->constrained();
            $table->decimal('unit_price', 20, 2);
            $table->longText('description')->nullable();
            $table->text('photos')->nullable();
            $table->string('thumbnail_img')->nullable();
            $table->boolean('published')->default(false);
            $table->boolean('status')->default(false);
            $table->string('condition', 50)->nullable();
            $table->string('location')->nullable();
            $table->text('tags')->nullable();
            $table->string('video_provider')->nullable();
            $table->string('video_link')->nullable();
            $table->string('slug')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_products');
    }
};
