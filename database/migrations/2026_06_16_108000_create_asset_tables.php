<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_assets', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('category')->index();
            $table->date('acquired_at')->nullable();
            $table->decimal('cost', 14, 2)->default(0);
            $table->string('status')->default('available')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_asset_id')->constrained('business_assets')->cascadeOnDelete();
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->date('assigned_at')->index();
            $table->date('returned_at')->nullable()->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_assignments');
        Schema::dropIfExists('business_assets');
    }
};
