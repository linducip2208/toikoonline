<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'gst_enable')) {
                $table->boolean('gst_enable')->default(false);
            }
            if (!Schema::hasColumn('products', 'gst_rate')) {
                $table->string('gst_rate', 20)->default('0');
            }
        });

        Schema::table('order_details', function (Blueprint $table) {
            if (!Schema::hasColumn('order_details', 'gst_rate')) {
                $table->decimal('gst_rate', 20, 2)->nullable();
            }
            if (!Schema::hasColumn('order_details', 'gst_amount')) {
                $table->decimal('gst_amount', 20, 2)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'gst_enable')) {
                $table->dropColumn('gst_enable');
            }
            if (Schema::hasColumn('products', 'gst_rate')) {
                $table->dropColumn('gst_rate');
            }
        });

        Schema::table('order_details', function (Blueprint $table) {
            if (Schema::hasColumn('order_details', 'gst_rate')) {
                $table->dropColumn('gst_rate');
            }
            if (Schema::hasColumn('order_details', 'gst_amount')) {
                $table->dropColumn('gst_amount');
            }
        });
    }
};
