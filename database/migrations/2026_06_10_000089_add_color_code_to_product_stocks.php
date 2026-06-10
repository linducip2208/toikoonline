<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_stocks', function (Blueprint $table) {
            if (!Schema::hasColumn('product_stocks', 'color_code')) {
                $table->string('color_code', 30)->nullable()->after('image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_stocks', function (Blueprint $table) {
            if (Schema::hasColumn('product_stocks', 'color_code')) {
                $table->dropColumn('color_code');
            }
        });
    }
};
