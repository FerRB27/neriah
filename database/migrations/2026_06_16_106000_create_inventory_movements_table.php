<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->foreignId('purchase_id')->nullable()->constrained('purchases')->nullOnDelete();
            $table->foreignId('purchase_line_id')->nullable()->constrained('purchase_lines')->nullOnDelete();
            $table->foreignId('production_order_id')->nullable()->constrained('production_orders')->nullOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->foreignId('sale_line_id')->nullable()->constrained('sale_lines')->nullOnDelete();
            $table->string('type')->index();
            $table->string('direction')->index();
            $table->decimal('quantity', 12, 4);
            $table->decimal('unit_cost', 14, 6)->default(0);
            $table->decimal('total_cost', 14, 2)->default(0);
            $table->decimal('running_quantity', 14, 4);
            $table->decimal('running_average_cost', 14, 6)->default(0);
            $table->date('movement_date')->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['inventory_item_id', 'movement_date']);
            $table->index(['type', 'movement_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
