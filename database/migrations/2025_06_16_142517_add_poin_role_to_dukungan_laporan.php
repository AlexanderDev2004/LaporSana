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
        Schema::table('dukungan_laporan', function (Blueprint $table) {
            $table->unsignedInteger('poin_role')->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dukungan_laporan', function (Blueprint $table) {
            $table->dropColumn('poin_role');
        });
    }
};
