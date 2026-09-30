<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->string('badge_text', 30)->nullable()->after('icon');
            $table->string('badge_color', 20)->nullable()->after('badge_text');
            $table->json('visibility')->nullable()->after('badge_color');
            $table->string('language_code', 10)->nullable()->after('visibility');
            $table->boolean('mega_menu')->default(false)->after('language_code');
        });
    }

    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn(['badge_text', 'badge_color', 'visibility', 'language_code', 'mega_menu']);
        });
    }
};
