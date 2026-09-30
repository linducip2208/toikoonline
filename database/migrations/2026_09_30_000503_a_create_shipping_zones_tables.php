<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name', 128);
            // Wildcard patterns, comma-separated, '*' = any. Null/empty = any.
            $table->string('country', 255)->nullable();
            $table->string('state', 255)->nullable();
            $table->string('city', 255)->nullable();
            $table->string('postcode', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')->constrained('shipping_zones')->cascadeOnDelete();
            $table->string('provider_format', 64)->default('table-rate');
            $table->string('courier', 64)->default('local');
            $table->string('service', 64)->default('standard');
            $table->string('name', 128);
            $table->unsignedBigInteger('base_rate')->default(0);
            $table->unsignedBigInteger('per_kg')->default(0);
            // Table-rate tiers: json list of {max_weight_kg, rate}. Rate wins over base/per_kg when matched.
            $table->json('weight_tiers')->nullable();
            $table->unsignedBigInteger('free_min_subtotal')->nullable();
            $table->string('eta', 64)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['zone_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_methods');
        Schema::dropIfExists('shipping_zones');
    }
};
