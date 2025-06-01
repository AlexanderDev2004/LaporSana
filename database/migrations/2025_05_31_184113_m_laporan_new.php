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
        Schema::create('m_laporan', function (Blueprint $table) {
            $table->id('laporan_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('fasilitas_id');
            $table->unsignedBigInteger('status_id');
            $table->dateTime('tanggal_lapor');
            $table->string('foto_bukti', 255)->nullable();
            $table->text('deskripsi');
            $table->integer('jumlah_pelapor');
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('m_user');
            $table->foreign('fasilitas_id')->references('fasilitas_id')->on('m_fasilitas');
            $table->foreign('status_id')->references('status_id')->on('m_status');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_laporan');

    }
};
