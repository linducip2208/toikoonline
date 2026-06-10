<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('club_point_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_point_id')->constrained('club_points')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->integer('points');
            $table->boolean('converted')->default(0);
            $table->timestamps();
        });

        DB::statement('ALTER TABLE club_point_details CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    }

    public function down(): void
    {
        Schema::dropIfExists('club_point_details');
    }
};
