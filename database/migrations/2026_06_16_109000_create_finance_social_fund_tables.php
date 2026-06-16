<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('founder_capital_movements', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index();
            $table->decimal('amount', 14, 2);
            $table->date('movement_date')->index();
            $table->string('concept');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('financial_profit_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->decimal('profit_amount', 14, 2);
            $table->decimal('social_fund_amount', 14, 2)->default(0);
            $table->decimal('reinvestment_amount', 14, 2)->default(0);
            $table->decimal('founder_recovery_amount', 14, 2)->default(0);
            $table->decimal('reserve_amount', 14, 2)->default(0);
            $table->date('allocated_at')->index();
            $table->timestamps();
        });

        Schema::create('social_fund_beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('social_fund_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_profit_allocation_id')->nullable()->constrained('financial_profit_allocations')->nullOnDelete();
            $table->foreignId('social_fund_beneficiary_id')->nullable()->constrained('social_fund_beneficiaries')->nullOnDelete();
            $table->string('type')->index();
            $table->decimal('amount', 14, 2);
            $table->date('movement_date')->index();
            $table->string('evidence_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_fund_movements');
        Schema::dropIfExists('social_fund_beneficiaries');
        Schema::dropIfExists('financial_profit_allocations');
        Schema::dropIfExists('founder_capital_movements');
    }
};
