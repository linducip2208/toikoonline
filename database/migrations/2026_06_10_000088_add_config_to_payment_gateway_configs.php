<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_gateway_configs', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_gateway_configs', 'config')) {
                $table->json('config')->nullable()->after('webhook_config');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payment_gateway_configs', function (Blueprint $table) {
            if (Schema::hasColumn('payment_gateway_configs', 'config')) {
                $table->dropColumn('config');
            }
        });
    }
};
