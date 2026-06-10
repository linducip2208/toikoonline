<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('frequently_bought_products', 'id')) {
            DB::statement('ALTER TABLE frequently_bought_products ADD COLUMN id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY FIRST');
        }
        if (!Schema::hasColumn('frequently_bought_products', 'created_at')) {
            Schema::table('frequently_bought_products', fn(Blueprint $t) => $t->timestamps());
        }

        if (!Schema::hasColumn('user_coupons', 'id')) {
            DB::statement('ALTER TABLE user_coupons ADD COLUMN id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY FIRST');
        }
        if (!Schema::hasColumn('user_coupons', 'created_at')) {
            Schema::table('user_coupons', fn(Blueprint $t) => $t->timestamps());
        }

        Schema::table('dynamic_popups', function (Blueprint $table) {
            if (Schema::hasColumn('dynamic_popups', 'id') && !$this->columnIsAutoIncrement('dynamic_popups', 'id')) {
                DB::statement('ALTER TABLE dynamic_popups MODIFY id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY');
            }
        });

        if (!Schema::hasColumn('compares', 'updated_at')) {
            Schema::table('compares', fn(Blueprint $t) => $t->timestamp('updated_at')->nullable()->after('created_at'));
        }
        if (!Schema::hasColumn('wishlists', 'updated_at')) {
            Schema::table('wishlists', fn(Blueprint $t) => $t->timestamp('updated_at')->nullable()->after('created_at'));
        }
    }

    private function columnIsAutoIncrement(string $table, string $column): bool
    {
        $result = DB::select("SHOW COLUMNS FROM `{$table}` WHERE Field = ?", [$column]);
        return !empty($result) && stripos($result[0]->Extra ?? '', 'auto_increment') !== false;
    }
};
