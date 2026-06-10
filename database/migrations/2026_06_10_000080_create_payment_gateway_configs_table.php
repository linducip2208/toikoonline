<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_gateway_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('gateway_format');
            $table->string('api_key_encrypted')->nullable();
            $table->string('api_secret_encrypted')->nullable();
            $table->string('merchant_id')->nullable();
            $table->string('base_url')->nullable();
            $table->json('extra_headers')->nullable();
            $table->json('webhook_config')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_sandbox')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_gateway_configs');
    }
};
