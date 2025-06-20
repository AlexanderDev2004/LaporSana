<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTugasSelesaiNullableOnMTugas extends Migration
{
    public function up()
    {
        Schema::table('m_tugas', function (Blueprint $table) {
            $table->dateTime('tugas_selesai')->nullable()->default(null)->change();
        });
    }

    public function down()
    {
        Schema::table('m_tugas', function (Blueprint $table) {
            $table->dateTime('tugas_selesai')->nullable(false)->change();
        });
    }
}
