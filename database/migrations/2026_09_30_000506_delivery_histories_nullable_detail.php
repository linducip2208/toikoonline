<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * delivery_histories.order_detail_id wajib (FK non-null) padahal semua
     * penulis timeline (webhook, observer, shipment) mencatat LEVEL ORDER.
     * Akibat: setiap tulis timeline gagal (prod ikut 500). Jadikan nullable.
     */
    public function up(): void
    {
        Schema::table('delivery_histories', function (Blueprint $table) {
            $table->dropForeign(['order_detail_id']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE delivery_histories MODIFY order_detail_id BIGINT UNSIGNED NULL');
            Schema::table('delivery_histories', function (Blueprint $table) {
                $table->foreign('order_detail_id')->references('id')->on('order_details')->nullOnDelete();
            });
        } else {
            Schema::table('delivery_histories', function (Blueprint $table) {
                $table->dropColumn('order_detail_id');
            });
            Schema::table('delivery_histories', function (Blueprint $table) {
                $table->foreignId('order_detail_id')->nullable()->constrained('order_details')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        // Tidak dikembalikan ke NOT NULL (data order-level tidak punya detail).
    }
};
