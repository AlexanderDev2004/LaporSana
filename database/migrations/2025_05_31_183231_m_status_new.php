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
        Schema::create('m_status', function (Blueprint $table) {
            $table->id('status_id');
            $table->enum('status_nama', ['menunggu verifikasi', 'ditolak', 'diproses', 'selesai'])->default('menunggu verifikasi');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
   public function down(): void
    {
        Schema::dropIfExists('m_status');
    }

};
