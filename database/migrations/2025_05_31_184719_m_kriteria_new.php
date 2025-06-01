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
    Schema::create('m_kriteria', function (Blueprint $table) {
        $table->id('kriteria_id');
        $table->string('kriteria_kode', 10);
        $table->string('kriteria_nama', 100);
        $table->float('kriteria_bobot', 300,0);
    });
}


    /**
     * Reverse the migrations.
     */
   public function down(): void
{
    Schema::dropIfExists('m_kriteria');
}
};
