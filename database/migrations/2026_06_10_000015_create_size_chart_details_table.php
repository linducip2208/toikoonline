<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('size_chart_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('size_chart_id');
            $table->foreign('size_chart_id')->references('id')->on('size_charts')->cascadeOnDelete();
            $table->foreignId('measurement_point_id')->constrained('measurement_points')->cascadeOnDelete();
            $table->foreignId('attribute_value_id')->constrained('attribute_values')->cascadeOnDelete();
            $table->string('inch_value', 191)->nullable();
            $table->string('cen_value', 191)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('size_chart_details');
    }
};
