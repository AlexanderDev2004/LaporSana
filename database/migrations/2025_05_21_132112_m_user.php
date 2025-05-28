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
        Schema::create('m_user', function (Blueprint $table) {
            $table->id('user_id');
            $table->foreignId('roles_id')->constrained('m_roles', 'roles_id')->onDelete('cascade');
            $table->string('username', 255);
            $table->string('name', 255);
            $table->string('password', 255);
            $table->integer('NIM')->nullable();
            $table->integer('NIP')->nullable();
            $table->string('avatar', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_user');
    }
};
