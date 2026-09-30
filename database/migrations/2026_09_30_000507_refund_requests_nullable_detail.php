<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * refund_requests.order_detail_id wajib padahal refund bisa level-order
     * (pelanggan mengajukan per order, bukan per item). Jadikan nullable —
     * RefundService sudah null-safe ($detail?->...).
     */
    public function up(): void
    {
        Schema::table('refund_requests', function (Blueprint $table) {
            try {
                $table->dropForeign(['order_detail_id']);
            } catch (\Throwable) {
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE refund_requests MODIFY order_detail_id BIGINT UNSIGNED NULL');
            Schema::table('refund_requests', function (Blueprint $table) {
                $table->foreign('order_detail_id')->references('id')->on('order_details')->nullOnDelete();
            });
        } else {
            Schema::table('refund_requests', function (Blueprint $table) {
                $table->dropColumn('order_detail_id');
            });
            Schema::table('refund_requests', function (Blueprint $table) {
                $table->foreignId('order_detail_id')->nullable()->constrained('order_details')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        // Tidak dikembalikan ke NOT NULL (refund level-order valid).
    }
};
