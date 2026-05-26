<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('animal_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g., "Sapi 01", "Kambing 05"
            $table->enum('type', ['Cow', 'Goat']);
            $table->integer('sequence_number');
            $table->boolean('is_full')->default(false); // True if it's a full cow (non-patungan) or slot 7/7
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('animal_groups');
    }
};
