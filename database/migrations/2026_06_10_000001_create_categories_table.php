<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name', 100);
            $table->string('slug', 255)->unique();
            $table->decimal('commision_rate', 8, 2)->default(0);
            $table->string('banner', 100)->nullable();
            $table->string('icon', 100)->nullable();
            $table->boolean('featured')->default(false);
            $table->boolean('top')->default(false);
            $table->boolean('digital')->default(false);
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('filter_attributes', 1000)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
