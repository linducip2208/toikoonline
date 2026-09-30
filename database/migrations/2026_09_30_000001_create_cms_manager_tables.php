<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Menu manager: header / footer / mobile
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('location', 30)->default('header'); // header, footer_shop, footer_help, mobile
            $table->string('label');
            $table->string('url');
            $table->string('icon', 50)->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('open_new_tab')->default(false);
            $table->timestamps();
        });

        // Homepage sections order (CMS versi kita)
        Schema::create('cms_sections', function (Blueprint $table) {
            $table->id();
            $table->string('key', 60)->unique(); // hero, voucher_rail, flash_deals, categories, products, banners, blog
            $table->string('title')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('data')->nullable(); // config tambahan per section (limit, dll)
            $table->timestamps();
        });

        // Page builder blocks + status
        Schema::table('pages', function (Blueprint $table) {
            $table->json('blocks')->nullable()->after('content');
            $table->boolean('status')->default(true)->after('blocks');
            $table->boolean('show_in_footer')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['blocks', 'status', 'show_in_footer']);
        });
        Schema::dropIfExists('cms_sections');
        Schema::dropIfExists('menus');
    }
};
