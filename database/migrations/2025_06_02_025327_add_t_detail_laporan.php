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
        // 1. Ambil data dari tabel m_laporan yang lama (jika tabel sudah berisi data)
        if (Schema::hasTable('m_laporan')) {
            // Simpan data ke variabel temporary
            $oldData = DB::table('m_laporan')->get()->toArray();
            
            // 2. Drop tabel m_laporan yang lama
            Schema::dropIfExists('m_laporan');
        } else {
            $oldData = [];
        }
        
        // 3. Buat tabel m_laporan baru
        Schema::create('m_laporan', function (Blueprint $table) {
            $table->id('laporan_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('status_id');
            $table->dateTime('tanggal_lapor');
            $table->integer('jumlah_pelapor');
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('m_user');
            $table->foreign('status_id')->references('status_id')->on('m_status');
        });
        
        // 4. Buat tabel m_laporan_detail
        Schema::create('m_laporan_detail', function (Blueprint $table) {
            $table->id('detail_id');
            $table->unsignedBigInteger('laporan_id');
            $table->unsignedBigInteger('fasilitas_id');
            $table->string('foto_bukti', 255)->nullable();
            $table->text('deskripsi');
            $table->timestamps();

            $table->foreign('laporan_id')->references('laporan_id')->on('m_laporan')->onDelete('cascade');
            $table->foreign('fasilitas_id')->references('fasilitas_id')->on('m_fasilitas');
        });
        
        // 5. Migrasi data lama ke struktur tabel baru (jika ada)
        if (!empty($oldData)) {
            foreach ($oldData as $data) {
                // Insert ke tabel m_laporan
                $laporan_id = DB::table('m_laporan')->insertGetId([
                    'user_id' => $data->user_id,
                    'status_id' => $data->status_id,
                    'tanggal_lapor' => $data->tanggal_lapor,
                    'jumlah_pelapor' => $data->jumlah_pelapor,
                    'created_at' => $data->created_at,
                    'updated_at' => $data->updated_at
                ]);
                
                // Insert ke tabel m_laporan_detail
                DB::table('m_laporan_detail')->insert([
                    'laporan_id' => $laporan_id,
                    'fasilitas_id' => $data->fasilitas_id,
                    'foto_bukti' => $data->foto_bukti,
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
        if (Schema::hasTable('m_laporan') && Schema::hasTable('m_laporan_detail')) {
            $newData = DB::table('m_laporan')
                ->join('m_laporan_detail', 'm_laporan.laporan_id', '=', 'm_laporan_detail.laporan_id')
                ->select(
                    'm_laporan.laporan_id', 
                    'm_laporan.user_id', 
                    'm_laporan_detail.fasilitas_id', 
                    'm_laporan.status_id', 
                    'm_laporan.tanggal_lapor',
                    'm_laporan_detail.foto_bukti',
                    'm_laporan_detail.deskripsi',
                    'm_laporan.jumlah_pelapor',
                    'm_laporan.created_at',
                    'm_laporan.updated_at'
                )
                ->get()
                ->toArray();
                
            // 2. Drop tabel baru
            Schema::dropIfExists('m_laporan_detail');
            Schema::dropIfExists('m_laporan');
            
            // 3. Buat tabel original
            Schema::create('m_laporan', function (Blueprint $table) {
                $table->id('laporan_id');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('fasilitas_id');
                $table->unsignedBigInteger('status_id');
                $table->dateTime('tanggal_lapor');
                $table->string('foto_bukti', 255)->nullable();
                $table->text('deskripsi');
                $table->integer('jumlah_pelapor');
                $table->timestamps();

                $table->foreign('user_id')->references('user_id')->on('m_user');
                $table->foreign('fasilitas_id')->references('fasilitas_id')->on('m_fasilitas');
                $table->foreign('status_id')->references('status_id')->on('m_status');
            });
            
            // 4. Kembalikan data ke struktur lama
            foreach ($newData as $data) {
                DB::table('m_laporan')->insert([
                    'laporan_id' => $data->laporan_id,
                    'user_id' => $data->user_id,
                    'fasilitas_id' => $data->fasilitas_id,
                    'status_id' => $data->status_id,
                    'tanggal_lapor' => $data->tanggal_lapor,
                    'foto_bukti' => $data->foto_bukti,
                    'deskripsi' => $data->deskripsi,
                    'jumlah_pelapor' => $data->jumlah_pelapor,
                    'created_at' => $data->created_at,
                    'updated_at' => $data->updated_at
                ]);
            }
        }
    }
};