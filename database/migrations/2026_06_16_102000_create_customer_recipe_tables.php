<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable()->index();
            $table->string('city')->nullable()->index();
            $table->string('address')->nullable();
            $table->text('notes')->nullable();
            $table->date('first_purchase_date')->nullable();
            $table->date('last_purchase_date')->nullable();
            $table->decimal('total_purchased', 14, 2)->default(0);
            $table->unsignedInteger('orders_count')->default(0);
            $table->timestamps();
        });

        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('expected_yield', 12, 4);
            $table->string('yield_unit', 20)->default('unidad');
            $table->boolean('active')->default(true)->index();
            $table->timestamps();

            $table->unique(['product_variant_id', 'name']);
        });

        Schema::create('recipe_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained('recipes')->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->decimal('quantity', 12, 4);
            $table->string('unit', 20);
            $table->timestamps();

            $table->unique(['recipe_id', 'inventory_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipe_ingredients');
        Schema::dropIfExists('recipes');
        Schema::dropIfExists('customers');
    }
};
