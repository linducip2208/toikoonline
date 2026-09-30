<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * addresses.set_billing warisan string(20) padahal dipakai sebagai
     * boolean di model (casts) dan query (->where('set_billing', true)).
     * Normalisasi nilai lalu jadikan boolean — tanpa hapus data.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::table('addresses')->whereIn('set_billing', ['1', 'true', 'yes'])->update(['set_billing' => '1']);
            DB::table('addresses')->where('set_billing', '<>', '1')->orWhereNull('set_billing')->update(['set_billing' => '0']);
            DB::statement('ALTER TABLE addresses MODIFY set_billing TINYINT(1) NOT NULL DEFAULT 0');
            return;
        }

        // SQLite: kolom baru -> salin -> tukar nama (data dipertahankan).
        Schema::table('addresses', function (Blueprint $table) {
            $table->boolean('set_billing_new')->default(false);
        });
        DB::table('addresses')->whereIn('set_billing', ['1', 'true', 'yes'])->update(['set_billing_new' => true]);
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('set_billing');
        });
        Schema::table('addresses', function (Blueprint $table) {
            $table->renameColumn('set_billing_new', 'set_billing');
        });
    }

    public function down(): void
    {
        // Tidak dikembalikan (boolean adalah bentuk yang benar).
    }
};
