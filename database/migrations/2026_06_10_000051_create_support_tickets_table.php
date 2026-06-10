<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('code');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('subject', 255);
            $table->longText('details')->nullable();
            $table->longText('files')->nullable();
            $table->string('status', 10)->default('pending');
            $table->boolean('viewed')->default(false);
            $table->boolean('client_viewed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
