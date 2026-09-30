<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 150);
            $table->string('city', 100)->nullable();
            $table->string('area', 100)->nullable();
            $table->string('address', 255)->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('stock_id')->nullable()->constrained('product_stocks')->nullOnDelete();
            $table->string('type', 30);
            $table->integer('qty');
            $table->integer('qty_after')->nullable();
            $table->string('ref_type', 100)->nullable();
            $table->unsignedBigInteger('ref_id')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['product_id', 'stock_id']);
            $table->index(['ref_type', 'ref_id']);
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 150);
            $table->string('contact_person', 100)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->string('status', 30)->default('draft');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('ordered_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('stock_id')->nullable()->constrained('product_stocks')->nullOnDelete();
            $table->integer('qty_ordered')->default(0);
            $table->integer('qty_received')->default(0);
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('courier', 50)->nullable();
            $table->string('tracking_number', 100)->nullable();
            $table->string('status', 30)->default('packed');
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('shipment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->foreignId('order_detail_id')->constrained('order_details')->cascadeOnDelete();
            $table->integer('qty')->default(0);
            $table->timestamps();
        });

        Schema::create('customer_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('customer_group_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_group_id')->constrained('customer_groups')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['customer_group_id', 'user_id']);
        });

        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('country', 10)->default('*');
            $table->string('state', 100)->default('*');
            $table->string('city', 100)->default('*');
            $table->string('postcode', 20)->default('*');
            $table->decimal('rate', 7, 4)->default(0);
            $table->boolean('inclusive')->default(false);
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('order_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note');
            $table->boolean('is_internal')->default(true);
            $table->timestamps();
        });

        DB::table('warehouses')->updateOrInsert(
            ['code' => 'DEFAULT'],
            [
                'name' => 'Gudang Utama',
                'city' => null,
                'area' => null,
                'address' => null,
                'is_default' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('order_notes');
        Schema::dropIfExists('tax_rates');
        Schema::dropIfExists('customer_group_user');
        Schema::dropIfExists('customer_groups');
        Schema::dropIfExists('shipment_items');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('warehouses');
    }
};
