<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('animal_groups', function (Blueprint $table) {
            $table->string('status')->default('Antre')->after('is_full'); // Antre, Disembelih, Dikuliti, Selesai Cacah
        });
    }

    public function down(): void
    {
        Schema::table('animal_groups', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
