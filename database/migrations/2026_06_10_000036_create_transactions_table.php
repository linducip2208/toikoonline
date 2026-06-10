<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('gateway', 255)->nullable();
            $table->string('payment_type', 255)->nullable();
            $table->text('additional_content')->nullable();
            $table->string('mpesa_request', 255)->nullable();
            $table->string('mpesa_receipt', 255)->nullable();
            $table->integer('status')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
