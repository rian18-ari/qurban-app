<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mudhohis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('animal_group_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('phone_number');
            $table->text('address')->nullable();
            $table->enum('type', ['Cow', 'Goat']); // redundant but useful for quick query
            $table->boolean('is_full_animal')->default(false); // If they buy 1 whole cow
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mudhohis');
    }
};
