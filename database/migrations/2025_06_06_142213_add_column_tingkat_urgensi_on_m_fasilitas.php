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
        Schema::table('m_fasilitas', function (Blueprint $table) {
            $table->integer('tingkat_urgensi', false, true)->length(3)->default(1)->after('fasilitas_nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_fasilitas', function (Blueprint $table) {
            $table->dropColumn('tingkat_urgensi');
        });
    }
};