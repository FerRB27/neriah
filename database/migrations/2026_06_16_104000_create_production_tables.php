<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maker_id')->constrained('people')->cascadeOnDelete();
            $table->foreignId('recipe_id')->constrained('recipes')->cascadeOnDelete();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->date('produced_at')->index();
            $table->string('status')->default('draft')->index();
            $table->decimal('planned_quantity', 12, 4);
            $table->decimal('produced_quantity', 12, 4)->default(0);
            $table->decimal('real_cost_total', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('production_consumptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained('production_orders')->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->decimal('planned_quantity', 12, 4);
            $table->decimal('consumed_quantity', 12, 4);
            $table->decimal('unit_cost', 14, 6);
            $table->decimal('total_cost', 14, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_consumptions');
        Schema::dropIfExists('production_orders');
    }
};
