<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class m_laporan extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $laporanData = [
            [
                'laporan_id' => 1,
                'user_id' => 1,
                'fasilitas_id' => 1,
                'status_id' => 1,
                'tanggal_lapor' => '2025-04-10 08:30:00',
                'foto_bukti' => 'proyektor_rusak_001.jpg',
                'deskripsi' => 'Proyektor di Ruang Kelas Teori 1 tidak menampilkan gambar dengan jelas.',
                'jumlah_pelapor' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'laporan_id' => 2,
                'user_id' => 2,
                'fasilitas_id' => 2,
                'status_id' => 2,
                'tanggal_lapor' => '2025-05-05 10:15:00',
                'foto_bukti' => null,
                'deskripsi' => 'AC di Ruang Kelas Teori 2 tidak dingin meskipun sudah dinyalakan lama.',
                'jumlah_pelapor' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'laporan_id' => 3,
                'user_id' => 3,
                'fasilitas_id' => 3,
                'status_id' => 3,
                'tanggal_lapor' => '2025-03-20 09:00:00',
                'foto_bukti' => 'whiteboard_rusak_001.jpg',
                'deskripsi' => 'Whiteboard di Ruang Kelas Teori 3 sulit dibersihkan dari bekas spidol.',
                'jumlah_pelapor' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'laporan_id' => 4,
                'user_id' => 4,
                'fasilitas_id' => 4,
                'status_id' => 1,
                'tanggal_lapor' => '2025-05-15 14:20:00',
                'foto_bukti' => 'kursi_rusak_001.jpg',
                'deskripsi' => 'Beberapa kursi lipat di Ruang Kelas Teori 4 patah di bagian engsel.',
                'jumlah_pelapor' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'laporan_id' => 5,
                'user_id' => 5,
                'fasilitas_id' => 6,
                'status_id' => 2,
                'tanggal_lapor' => '2025-05-25 11:45:00',
                'foto_bukti' => null,
                'deskripsi' => 'Speaker di Ruang Kelas Teori 6 menghasilkan suara pecah saat digunakan.',
                'jumlah_pelapor' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'laporan_id' => 6,
                'user_id' => 1,
                'fasilitas_id' => 7,
                'status_id' => 1,
                'tanggal_lapor' => '2025-04-25 13:30:00',
                'foto_bukti' => 'mic_rusak_001.jpg',
                'deskripsi' => 'Mic Wireless di Ruang Kelas Teori 7 sering terputus saat digunakan.',
                'jumlah_pelapor' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'laporan_id' => 7,
                'user_id' => 2,
                'fasilitas_id' => 8,
                'status_id' => 3,
                'tanggal_lapor' => '2025-03-10 08:00:00',
                'foto_bukti' => 'tv_rusak_001.jpg',
                'deskripsi' => 'TV LED di Lab. Proyek 1 tidak menyala setelah listrik padam.',
                'jumlah_pelapor' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('m_laporan')->insert($laporanData);
    }
}

