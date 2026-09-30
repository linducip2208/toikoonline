<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Total ongkir + resi di orders (sebelumnya ongkir hanya tersebar di order_details)
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('shipping_cost', 12, 2)->default(0)->after('tax_amount');
            $table->string('courier', 50)->nullable()->after('shipping_method');
            $table->string('tracking_number', 100)->nullable()->after('courier');
        });

        // Timeline lacak: label + catatan bebas (sebelumnya hanya FK status)
        Schema::table('delivery_histories', function (Blueprint $table) {
            $table->string('status', 150)->nullable()->after('delivery_status');
            $table->text('note')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_histories', function (Blueprint $table) {
            $table->dropColumn(['status', 'note']);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_cost', 'courier', 'tracking_number']);
        });
    }
};
