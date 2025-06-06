<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambahkan kolom poin_roles ke tabel m_roles
        Schema::table('m_roles', function (Blueprint $table) {
            $table->integer('poin_roles', false, true)->length(3)->nullable()->after('roles_nama');
        });

        // 2. Update nilai poin_roles sesuai ketentuan
        DB::statement("UPDATE m_roles SET poin_roles = CASE 
                          WHEN roles_kode = 'DSN' OR roles_kode = 'TNDK' THEN 10
                          WHEN roles_kode = 'MHS' THEN 5
                          ELSE NULL 
                        END");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_roles', function (Blueprint $table) {
            $table->dropColumn('poin_roles');
        });
    }
};