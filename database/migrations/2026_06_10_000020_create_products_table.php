<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('added_by', 6)->default('admin');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->string('photos', 2000)->nullable();
            $table->string('thumbnail_img', 100)->nullable();
            $table->string('short_video', 255)->nullable();
            $table->string('short_video_thumbnail', 255)->nullable();
            $table->string('video_provider', 20)->nullable();
            $table->longText('video_link')->nullable();
            $table->string('tags', 500)->nullable();
            $table->longText('description')->nullable();
            $table->decimal('unit_price', 20, 2);
            $table->decimal('purchase_price', 20, 2)->nullable();
            $table->boolean('variant_product')->default(false);
            $table->string('attributes', 1000)->default('[]');
            $table->mediumText('choice_options')->nullable();
            $table->string('colors', 1000)->nullable();
            $table->text('variations')->nullable();
            $table->boolean('todays_deal')->default(false);
            $table->boolean('published')->default(true);
            $table->boolean('approved')->default(true);
            $table->string('stock_visibility', 10)->default('quantity');
            $table->string('unit', 50)->nullable();
            $table->integer('min_qty')->default(1);
            $table->integer('max_qty')->default(10);
            $table->integer('low_stock_qty')->default(0);
            $table->decimal('discount', 20, 2)->default(0);
            $table->string('discount_type', 10)->nullable();
            $table->integer('discount_start_date')->nullable();
            $table->integer('discount_end_date')->nullable();
            $table->decimal('tax', 20, 2)->default(0);
            $table->string('tax_type', 10)->nullable();
            $table->string('shipping_type', 20)->default('flat_rate');
            $table->decimal('shipping_cost', 20, 2)->default(0);
            $table->integer('num_of_sale')->default(0);
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_description', 1000)->nullable();
            $table->foreignId('meta_img')->nullable()->constrained('uploads')->nullOnDelete();
            $table->string('slug', 255)->unique();
            $table->decimal('rating', 3, 2)->default(0);
            $table->string('barcode', 255)->nullable();
            $table->boolean('digital')->default(false);
            $table->string('file_name', 255)->nullable();
            $table->string('file_path', 255)->nullable();
            $table->string('external_link', 500)->nullable();
            $table->string('external_link_btn', 50)->default('Buy Now');
            $table->boolean('wholesale_product')->default(false);
            $table->boolean('cash_on_delivery')->default(true);
            $table->foreignId('warranty_id')->nullable()->constrained('warranties')->nullOnDelete();
            $table->foreignId('warranty_note_id')->nullable()->constrained('notes')->nullOnDelete();
            $table->foreignId('refund_note_id')->nullable()->constrained('notes')->nullOnDelete();
            $table->foreignId('shipping_note_id')->nullable()->constrained('notes')->nullOnDelete();
            $table->foreignId('delivery_note_id')->nullable()->constrained('notes')->nullOnDelete();
            $table->boolean('is_quantity_multiplied')->default(false);
            $table->integer('est_shipping_days')->default(0);
            $table->decimal('weight', 8, 2)->default(0);
            $table->decimal('height', 8, 2)->default(0);
            $table->decimal('width', 8, 2)->default(0);
            $table->decimal('length', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
