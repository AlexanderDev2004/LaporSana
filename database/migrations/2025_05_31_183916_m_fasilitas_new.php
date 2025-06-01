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
        Schema::create('m_fasilitas', function (Blueprint $table) {
            $table->id('fasilitas_id');
            $table->unsignedBigInteger('ruangan_id');
            $table->string('fasilitas_kode', 10)->unique();
            $table->string('fasilitas_nama', 100);
            $table->timestamps();


            $table->foreign('ruangan_id')->references('ruangan_id')->on('m_ruangan');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_fasilitas');
    }
};
