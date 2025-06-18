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
        Schema::rename('spk_steps', 't_spk_steps');
    }

    public function down()
    {
        Schema::rename('t_spk_steps', 'spk_steps');
    }
};
