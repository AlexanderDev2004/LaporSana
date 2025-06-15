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
        Schema::create('t_rekomperbaikan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fasilitas_id');
            $table->integer('rank');
            $table->timestamps();

            $table->foreign('fasilitas_id')->references('fasilitas_id')->on('m_fasilitas');
            $table->unique(['fasilitas_id', 'rank']);
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_RekomPerbaikan');
    }
};
