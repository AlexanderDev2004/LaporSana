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
        Schema::table('m_tugas_detail', function (Blueprint $table) {
            $table->integer('tingkat_kerusakan')->length(3)->default(1)->after('fasilitas_id');
            $table->float('biaya_perbaikan', 15, 2)->default(0)->after('tingkat_kerusakan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_tugas_detail', function (Blueprint $table) {
            $table->dropColumn(['tingkat_kerusakan', 'biaya_perbaikan']);
        });
    }
};