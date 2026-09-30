<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * addresses.set_billing boolean NOT NULL memecahkan insert yang tidak
     * menyertakan kolom (mis. path lama). Longgarkan jadi nullable —
     * cast boolean model tetap memberi false saat dibaca.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE addresses MODIFY set_billing TINYINT(1) NULL DEFAULT 0');
            return;
        }
        Schema::table('addresses', function (Blueprint $table) {
            $table->boolean('set_billing_tmp')->nullable()->default(false);
        });
        DB::table('addresses')->update(['set_billing_tmp' => DB::raw('set_billing')]);
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('set_billing');
        });
        Schema::table('addresses', function (Blueprint $table) {
            $table->renameColumn('set_billing_tmp', 'set_billing');
        });
    }

    public function down(): void
    {
    }
};
