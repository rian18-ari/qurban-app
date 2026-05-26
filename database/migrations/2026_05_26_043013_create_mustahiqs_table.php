<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mustahiqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('distribution_session_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('nik')->nullable();
            $table->text('address')->nullable();
            $table->string('phone_number')->nullable();
            $table->enum('status', ['Not Received', 'Received'])->default('Not Received');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mustahiqs');
    }
};
