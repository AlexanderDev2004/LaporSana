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
        Schema::create('spk_steps', function (Blueprint $table) {
            $table->id();

            // Relasi ke fasilitas yang dihitung (nullable jika tidak spesifik)
            $table->unsignedBigInteger('fasilitas_id');

            // Metode perhitungan: 'psi' atau 'edas'
            $table->enum('metode', ['psi', 'edas']);

            // Nama langkah, contoh:
            // 'matriks_keputusan', 'normalisasi', 'bobot', 'pda', 'nda', 'ranking'
            $table->string('step');

            // Urutan langkah, agar mudah diurutkan saat menampilkan
            $table->unsignedInteger('urutan')->default(0);

            // Data hasil step (format JSON)
            $table->json('hasil');

            $table->timestamps();

            // Foreign key ke m_fasilitas
            $table->foreign('fasilitas_id')->references('fasilitas_id')->on('m_fasilitas');

            // Hindari duplikasi untuk satu langkah metode yang sama pada fasilitas
            $table->unique(['fasilitas_id', 'metode', 'step']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spk_steps');
    }
};
