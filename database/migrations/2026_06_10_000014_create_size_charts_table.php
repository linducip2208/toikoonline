<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('size_charts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('fit_type', 191)->nullable();
            $table->string('stretch_type', 191)->nullable();
            $table->string('photos', 191)->nullable();
            $table->text('description')->nullable();
            $table->string('measurement_points', 255)->comment('JSON array of measurement_point ids');
            $table->string('size_options', 255)->comment('JSON array of attribute_value ids');
            $table->string('measurement_option', 191)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('size_charts');
    }
};
