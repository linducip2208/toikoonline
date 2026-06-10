<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable();
            $table->string('subtitle', 255)->nullable();
            $table->foreignId('photo')->nullable()->constrained('uploads')->nullOnDelete();
            $table->string('link', 255)->nullable();
            $table->integer('position')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};
