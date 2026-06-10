<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carriers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->foreignId('logo')->nullable()->constrained('uploads')->nullOnDelete();
            $table->boolean('active')->default(false);
            $table->string('addon_identifier', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carriers');
    }
};
