<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_types', function (Blueprint $table) {
            $table->id();
            $table->string('user_type', 20)->default('customer');
            $table->string('type', 100);
            $table->string('name', 100);
            $table->string('image', 100)->nullable();
            $table->string('default_text', 255);
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        DB::statement('ALTER TABLE notification_types CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_types');
    }
};
