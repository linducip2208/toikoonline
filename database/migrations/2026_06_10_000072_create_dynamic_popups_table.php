<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dynamic_popups', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->boolean('status')->default(0);
            $table->string('title', 191);
            $table->text('summary');
            $table->string('banner', 191)->nullable();
            $table->string('btn_link', 191);
            $table->string('btn_text', 191)->nullable();
            $table->string('btn_text_color', 191)->nullable();
            $table->string('btn_background_color', 191)->nullable();
            $table->string('show_subscribe_form', 191)->nullable();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE dynamic_popups CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    }

    public function down(): void
    {
        Schema::dropIfExists('dynamic_popups');
    }
};
