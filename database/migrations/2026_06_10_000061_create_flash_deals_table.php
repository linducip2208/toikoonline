<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flash_deals', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable();
            $table->integer('start_date')->nullable();
            $table->integer('end_date')->nullable();
            $table->boolean('status')->default(0);
            $table->boolean('featured')->default(0);
            $table->string('background_color', 255)->nullable();
            $table->string('text_color', 255)->nullable();
            $table->string('banner', 255)->nullable();
            $table->string('slug', 255)->nullable()->unique();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE flash_deals CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    }

    public function down(): void
    {
        Schema::dropIfExists('flash_deals');
    }
};
