<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
    {
        Schema::table('m_user', function (Blueprint $table) {
            $table->bigInteger('NIM')->nullable()->change();
            $table->bigInteger('NIP')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('m_user', function (Blueprint $table) {
            $table->integer('NIM')->nullable()->change();
            $table->integer('NIP')->nullable()->change();
        });
    }
};
