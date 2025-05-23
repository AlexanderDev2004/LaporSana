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
        $table->string('username', 20)->after('user_id');
    });
}

public function down()
{
    Schema::table('m_user', function (Blueprint $table) {
        $table->dropColumn('username');
    });
}
};
