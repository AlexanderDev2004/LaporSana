<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class m_tugas extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $tugasData = [
            [
                'tugas_id' => 1,
                'user_id' => 13,
                'fasilitas_id' => 1,
                'status_id' => 3,
                'tugas_jenis' => 'perbaikan',
                'tugas_mulai' => '2025-04-12 09:00:00',
                'tugas_selesai' => '2025-04-12 11:00:00',
                'tugas_image' => 'tugas_proyektor_001.jpg',
                'deskripsi' => 'Perbaikan proyektor di Ruang Kelas Teori 1 karena gambar tidak tampil.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tugas_id' => 2,
                'user_id' => 16,
                'fasilitas_id' => 2,
                'status_id' => 4,
                'tugas_jenis' => 'pemeriksaan',
                'tugas_mulai' => '2025-05-06 08:30:00',
                'tugas_selesai' => '2025-05-06 10:00:00',
                'tugas_image' => 'tugas_ac_001.jpg',
                'deskripsi' => 'Pemeriksaan rutin AC di Ruang Kelas Teori 2 untuk memastikan fungsi normal.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tugas_id' => 3,
                'user_id' => 17,
                'fasilitas_id' => 3,
                'status_id' => 4,
                'tugas_jenis' => 'perbaikan',
                'tugas_mulai' => '2025-03-22 10:00:00',
                'tugas_selesai' => '2025-03-22 12:00:00',
                'tugas_image' => 'tugas_whiteboard_001.jpg',
                'deskripsi' => 'Perbaikan whiteboard di Ruang Kelas Teori 3 karena permukaan sulit dibersihkan.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tugas_id' => 4,
                'user_id' => 13,
                'fasilitas_id' => 4,
                'status_id' => 3,
                'tugas_jenis' => 'perbaikan',
                'tugas_mulai' => '2025-05-16 13:00:00',
                'tugas_selesai' => '2025-05-16 15:00:00',
                'tugas_image' => 'tugas_kursi_001.jpg',
                'deskripsi' => 'Perbaikan kursi lipat di Ruang Kelas Teori 4 karena engsel rusak.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tugas_id' => 5,
                'user_id' => 16,
                'fasilitas_id' => 6,
                'status_id' => 3,
                'tugas_jenis' => 'perbaikan',
                'tugas_mulai' => '2025-05-26 09:30:00',
                'tugas_selesai' => '2025-05-26 11:30:00',
                'tugas_image' => 'tugas_speaker_001.jpg',
                'deskripsi' => 'Perbaikan speaker di Ruang Kelas Teori 6 karena suara pecah.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tugas_id' => 6,
                'user_id' => 17,
                'fasilitas_id' => 7,
                'status_id' => 4,
                'tugas_jenis' => 'pemeriksaan',
                'tugas_mulai' => '2025-04-26 14:00:00',
                'tugas_selesai' => '2025-04-26 15:30:00',
                'tugas_image' => 'tugas_mic_001.jpg',
                'deskripsi' => 'Pemeriksaan mic wireless di Ruang Kelas Teori 7 untuk memastikan koneksi stabil.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tugas_id' => 7,
                'user_id' => 13,
                'fasilitas_id' => 8,
                'status_id' => 4,
                'tugas_jenis' => 'perbaikan',
                'tugas_mulai' => '2025-03-12 08:00:00',
                'tugas_selesai' => '2025-03-12 10:30:00',
                'tugas_image' => 'tugas_tv_001.jpg',
                'deskripsi' => 'Perbaikan TV LED di Lab. Proyek 1 karena tidak menyala setelah listrik padam.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('m_tugas')->insert($tugasData);
    }
}

