<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_channels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('promotional_price', 12, 2);
            $table->date('starts_at')->index();
            $table->date('ends_at')->index();
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('seller_id')->nullable()->constrained('people')->nullOnDelete();
            $table->foreignId('maker_id')->nullable()->constrained('people')->nullOnDelete();
            $table->foreignId('sales_channel_id')->nullable()->constrained('sales_channels')->nullOnDelete();
            $table->date('sold_at')->index();
            $table->string('status')->default('draft')->index();
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount_total', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->decimal('visible_profit', 14, 2)->default(0);
            $table->decimal('hidden_profit', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('sale_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->foreignId('promotion_id')->nullable()->constrained('promotions')->nullOnDelete();
            $table->decimal('quantity', 12, 4);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('standard_unit_cost', 14, 6)->default(0);
            $table->decimal('real_unit_cost', 14, 6)->default(0);
            $table->decimal('line_total', 14, 2);
            $table->decimal('visible_profit', 14, 2)->default(0);
            $table->decimal('hidden_profit', 14, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_lines');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('sales_channels');
    }
};
