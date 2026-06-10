<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50);
            $table->string('title', 255)->nullable();
            $table->string('slug', 255)->nullable()->unique();
            $table->longText('content')->nullable();
            $table->text('meta_title')->nullable();
            $table->string('meta_description', 1000)->nullable();
            $table->string('keywords', 1000)->nullable();
            $table->string('meta_image', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
