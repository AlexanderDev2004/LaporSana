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
        Schema::create('m_ruangan', function (Blueprint $table) {
            $table->id('ruangan_id');
            $table->unsignedBigInteger('lantai_id');
            $table->string('ruangan_kode', 10)->unique();
            $table->string('ruangan_nama', 100);
            $table->timestamps();

            $table->foreign('lantai_id')->references('lantai_id')->on('m_lantai');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_ruangan');
    }
};
