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
        Schema::create('riwayat_perbaikan', function (Blueprint $table) {
            $table->bigIncrements('riwayat_id');
            $table->unsignedBigInteger('tugas_id');
            $table->integer('rating')->length(3)->nullable();
            $table->text('ulasan')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('tugas_id')
                  ->references('tugas_id')
                  ->on('m_tugas')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_perbaikan');
    }
};