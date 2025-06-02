<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ambil data dari tabel m_tugas yang lama (jika tabel sudah berisi data)
        if (Schema::hasTable('m_tugas')) {
            // Simpan data ke variabel temporary
            $oldData = DB::table('m_tugas')->get()->toArray();
            
            // 2. Drop tabel m_tugas yang lama
            Schema::dropIfExists('m_tugas');
        } else {
            $oldData = [];
        }
        
        // 3. Buat tabel m_tugas baru
        Schema::create('m_tugas', function (Blueprint $table) {
            $table->id('tugas_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('status_id');
            $table->enum('tugas_jenis', ['pemeriksaan', 'perbaikan']);
            $table->dateTime('tugas_mulai');
            $table->dateTime('tugas_selesai');
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('m_user');
            $table->foreign('status_id')->references('status_id')->on('m_status');
        });
        
        // 4. Buat tabel m_tugas_detail
        Schema::create('m_tugas_detail', function (Blueprint $table) {
            $table->id('detail_id');
            $table->unsignedBigInteger('tugas_id');
            $table->unsignedBigInteger('fasilitas_id');
            $table->string('tugas_image', 255);
            $table->text('deskripsi');
            $table->timestamps();

            $table->foreign('tugas_id')->references('tugas_id')->on('m_tugas')->onDelete('cascade');
            $table->foreign('fasilitas_id')->references('fasilitas_id')->on('m_fasilitas');
        });
        
        // 5. Migrasi data lama ke struktur tabel baru (jika ada)
        if (!empty($oldData)) {
            foreach ($oldData as $data) {
                // Insert ke tabel m_tugas
                $tugas_id = DB::table('m_tugas')->insertGetId([
                    'user_id' => $data->user_id,
                    'status_id' => $data->status_id,
                    'tugas_jenis' => $data->tugas_jenis,
                    'tugas_mulai' => $data->tugas_mulai,
                    'tugas_selesai' => $data->tugas_selesai,
                    'created_at' => $data->created_at,
                    'updated_at' => $data->updated_at
                ]);
                
                // Insert ke tabel m_tugas_detail
                DB::table('m_tugas_detail')->insert([
                    'tugas_id' => $tugas_id,
                    'fasilitas_id' => $data->fasilitas_id,
                    'tugas_image' => $data->tugas_image,
                    'deskripsi' => $data->deskripsi,
                    'created_at' => $data->created_at,
                    'updated_at' => $data->updated_at
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Ambil data dari tabel baru jika ingin dikembalikan
        if (Schema::hasTable('m_tugas') && Schema::hasTable('m_tugas_detail')) {
            $newData = DB::table('m_tugas')
                ->join('m_tugas_detail', 'm_tugas.tugas_id', '=', 'm_tugas_detail.tugas_id')
                ->select(
                    'm_tugas.tugas_id', 
                    'm_tugas.user_id', 
                    'm_tugas_detail.fasilitas_id', 
                    'm_tugas.status_id', 
                    'm_tugas.tugas_jenis',
                    'm_tugas.tugas_mulai',
                    'm_tugas.tugas_selesai',
                    'm_tugas_detail.tugas_image',
                    'm_tugas_detail.deskripsi',
                    'm_tugas.created_at',
                    'm_tugas.updated_at'
                )
                ->get()
                ->toArray();
                
            // 2. Drop tabel baru
            Schema::dropIfExists('m_tugas_detail');
            Schema::dropIfExists('m_tugas');
            
            // 3. Buat tabel original
            Schema::create('m_tugas', function (Blueprint $table) {
                $table->id('tugas_id');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('fasilitas_id');
                $table->unsignedBigInteger('status_id');
                $table->enum('tugas_jenis', ['pemeriksaan', 'perbaikan']);
                $table->dateTime('tugas_mulai');
                $table->dateTime('tugas_selesai');
                $table->string('tugas_image', 255);
                $table->text('deskripsi');
                $table->timestamps();

                $table->foreign('user_id')->references('user_id')->on('m_user');
                $table->foreign('fasilitas_id')->references('fasilitas_id')->on('m_fasilitas');
                $table->foreign('status_id')->references('status_id')->on('m_status');
            });
            
            // 4. Kembalikan data ke struktur lama
            foreach ($newData as $data) {
                DB::table('m_tugas')->insert([
                    'tugas_id' => $data->tugas_id,
                    'user_id' => $data->user_id,
                    'fasilitas_id' => $data->fasilitas_id,
                    'status_id' => $data->status_id,
                    'tugas_jenis' => $data->tugas_jenis,
                    'tugas_mulai' => $data->tugas_mulai,
                    'tugas_selesai' => $data->tugas_selesai,
                    'tugas_image' => $data->tugas_image,
                    'deskripsi' => $data->deskripsi,
                    'created_at' => $data->created_at,
                    'updated_at' => $data->updated_at
                ]);
            }
        }
    }
};