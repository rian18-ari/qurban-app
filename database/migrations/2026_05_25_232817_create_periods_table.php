<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('periods', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., '1446 H' or '2026'
            $table->boolean('is_active')->default(false); // Only one should be active ideally
            $table->decimal('cow_patungan_cost', 12, 2)->default(0); // Biaya patungan sapi (e.g., 3.500.000)
            $table->decimal('goat_operational_cost', 12, 2)->default(0); // Biaya operasional kambing (e.g., 200.000)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periods');
    }
};
