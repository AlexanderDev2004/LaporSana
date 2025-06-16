<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dukungan_laporan', function (Blueprint $table) {
            $table->id('dukungan_id');
            $table->unsignedBigInteger('laporan_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            // Menghubungkan ke tabel laporan dan user
            $table->foreign('laporan_id')->references('laporan_id')->on('m_laporan')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('m_user')->onDelete('cascade');

            // Kunci Unik: Mencegah duplikasi data (user_id & laporan_id yang sama)
            $table->unique(['laporan_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dukungan_laporan');
    }
};
