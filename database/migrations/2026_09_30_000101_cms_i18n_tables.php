<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('entity_translations')) {
            Schema::create('entity_translations', function (Blueprint $table) {
                $table->id();
                $table->string('translatable_type', 255);
                $table->unsignedBigInteger('translatable_id');
                $table->string('locale', 10);
                $table->string('tkey', 100);
                $table->text('tvalue')->nullable();
                $table->string('status', 20)->default('draft');
                $table->timestamps();
                $table->unique(['translatable_type', 'translatable_id', 'locale', 'tkey'], 'entity_trans_unique');
                $table->index(['translatable_type', 'translatable_id']);
                $table->index('locale');
            });
        }

        if (! Schema::hasTable('redirects')) {
            Schema::create('redirects', function (Blueprint $table) {
                $table->id();
                $table->string('from_path', 500)->unique();
                $table->string('to_path', 500);
                $table->unsignedSmallInteger('code')->default(301);
                $table->unsignedBigInteger('hits')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('themes')) {
            Schema::create('themes', function (Blueprint $table) {
                $table->id();
                $table->string('key', 60)->unique();
                $table->string('name');
                $table->string('version', 20)->default('1.0.0');
                $table->json('settings')->nullable();
                $table->boolean('is_active')->default(false);
                $table->timestamps();
            });
        }

        DB::table('themes')->updateOrInsert(
            ['key' => 'default'],
            ['name' => 'Default Storefront', 'version' => '1.0.0', 'settings' => json_encode(['primary_color' => '#4f46e5', 'layout' => 'standard']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]
        );

        if (! Schema::hasTable('media_folders')) {
            Schema::create('media_folders', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('page_revisions')) {
            Schema::create('page_revisions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('page_id');
                $table->string('title', 255)->nullable();
                $table->string('slug', 255)->nullable();
                $table->longText('content')->nullable();
                $table->json('blocks')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
                $table->index('page_id');
            });
        }

        if (Schema::hasTable('pages') && ! Schema::hasColumn('pages', 'published_at')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->timestamp('published_at')->nullable()->after('status');
            });
        }

        if (Schema::hasTable('uploads')) {
            Schema::table('uploads', function (Blueprint $table) {
                if (! Schema::hasColumn('uploads', 'folder_id')) {
                    $table->unsignedBigInteger('folder_id')->nullable()->after('user_id');
                }
                if (! Schema::hasColumn('uploads', 'alt_text')) {
                    $table->string('alt_text', 255)->nullable()->after('external_link');
                }
                if (! Schema::hasColumn('uploads', 'caption')) {
                    $table->string('caption', 500)->nullable()->after('alt_text');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('uploads')) {
            Schema::table('uploads', function (Blueprint $table) {
                $cols = [];
                if (Schema::hasColumn('uploads', 'folder_id')) {
                    $cols[] = 'folder_id';
                }
                if (Schema::hasColumn('uploads', 'alt_text')) {
                    $cols[] = 'alt_text';
                }
                if (Schema::hasColumn('uploads', 'caption')) {
                    $cols[] = 'caption';
                }
                if ($cols !== []) {
                    $table->dropColumn($cols);
                }
            });
        }
        if (Schema::hasTable('pages') && Schema::hasColumn('pages', 'published_at')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->dropColumn('published_at');
            });
        }
        Schema::dropIfExists('page_revisions');
        Schema::dropIfExists('media_folders');
        Schema::dropIfExists('themes');
        Schema::dropIfExists('redirects');
        Schema::dropIfExists('entity_translations');
    }
};
