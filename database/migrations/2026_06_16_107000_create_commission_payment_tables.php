<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('sale_line_id')->nullable()->constrained('sale_lines')->cascadeOnDelete();
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->string('type')->index();
            $table->decimal('amount', 14, 2);
            $table->string('status')->default('pending')->index();
            $table->date('earned_at')->index();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->decimal('amount', 14, 2);
            $table->string('concept');
            $table->string('status')->default('pending')->index();
            $table->date('week_start')->index();
            $table->date('week_end')->index();
            $table->date('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('commission_entries');
    }
};
