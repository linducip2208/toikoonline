<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('note_type', 50);
            $table->longText('description');
            $table->tinyInteger('seller_access')->default(0);
            $table->nullableTimestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
