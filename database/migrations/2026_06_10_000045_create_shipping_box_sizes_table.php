<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_box_sizes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('courier_type', 255);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->float('length');
            $table->float('breadth');
            $table->float('height');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_box_sizes');
    }
};
