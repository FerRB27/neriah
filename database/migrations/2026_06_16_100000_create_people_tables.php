<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('address')->nullable();
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('person_role_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->string('role');
            $table->timestamps();

            $table->unique(['person_id', 'role']);
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('person_role_assignments');
        Schema::dropIfExists('people');
    }
};
