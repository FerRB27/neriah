<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('input_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('inputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('input_category_id')->constrained('input_categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('unit', 20);
            $table->decimal('minimum_stock', 12, 4)->default(0);
            $table->boolean('active')->default(true)->index();
            $table->timestamps();

            $table->unique(['input_category_id', 'name']);
        });

        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_category_id')->constrained('product_categories')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('standard_cost', 12, 4)->default(0);
            $table->decimal('base_price', 12, 2)->default(0);
            $table->decimal('commission_amount', 12, 2)->default(0);
            $table->decimal('maker_payment_amount', 12, 2)->default(0);
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->string('name');
            $table->string('unit_label', 30)->default('unidad');
            $table->unsignedInteger('units_per_variant')->default(1);
            $table->decimal('weight_grams', 10, 2)->nullable();
            $table->decimal('price', 12, 2);
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('input_id')->nullable()->constrained('inputs')->nullOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->string('sku')->unique();
            $table->string('name');
            $table->string('item_type')->index();
            $table->string('unit', 20);
            $table->decimal('minimum_stock', 12, 4)->default(0);
            $table->decimal('current_stock', 14, 4)->default(0);
            $table->decimal('average_cost', 14, 6)->default(0);
            $table->boolean('active')->default(true)->index();
            $table->timestamps();

            $table->index(['item_type', 'active']);
            $table->index(['input_id', 'product_variant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('inputs');
        Schema::dropIfExists('input_categories');
    }
};
