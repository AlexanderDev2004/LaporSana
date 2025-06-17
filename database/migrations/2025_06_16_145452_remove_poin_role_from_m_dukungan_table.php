<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dukungan_laporan', function (Blueprint $table) {
            $table->dropColumn('poin_role');
        });
    }

    public function down(): void
    {
        Schema::table('dukungan_laporan', function (Blueprint $table) {
            $table->integer('poin_role')->default(0)->after('user_id');
        });
    }
};
