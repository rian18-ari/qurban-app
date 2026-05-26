<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribution_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g., "Sesi 1"
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('quota');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribution_sessions');
    }
};
