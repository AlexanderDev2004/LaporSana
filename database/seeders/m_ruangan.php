<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class m_ruangan extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ruangan = [
            // lantai 5
            ['ruangan_id' => 1,  'lantai_id' => 1, 'ruangan_kode' => 'R.05.01', 'ruangan_nama' => 'R. Kelas Teori 1'],
            ['ruangan_id' => 2,  'lantai_id' => 1, 'ruangan_kode' => 'R.05.02', 'ruangan_nama' => 'R. Kelas Teori 2'],
            ['ruangan_id' => 3,  'lantai_id' => 1, 'ruangan_kode' => 'R.05.03', 'ruangan_nama' => 'R. Kelas Teori 3'],
            ['ruangan_id' => 4,  'lantai_id' => 1, 'ruangan_kode' => 'R.05.04', 'ruangan_nama' => 'R. Kelas Teori 4'],
            ['ruangan_id' => 5,  'lantai_id' => 1, 'ruangan_kode' => 'R.05.05', 'ruangan_nama' => 'R. Kelas Teori 5'],
            ['ruangan_id' => 6,  'lantai_id' => 1, 'ruangan_kode' => 'R.05.06', 'ruangan_nama' => 'R. Kelas Teori 6'],
            ['ruangan_id' => 7,  'lantai_id' => 1, 'ruangan_kode' => 'R.05.07', 'ruangan_nama' => 'R. Kelas Teori 7'],
            ['ruangan_id' => 8,  'lantai_id' => 1, 'ruangan_kode' => 'R.05.08', 'ruangan_nama' => 'Lab. Proyek 1'],
            ['ruangan_id' => 9,  'lantai_id' => 1, 'ruangan_kode' => 'R.05.09', 'ruangan_nama' => 'Toilet Wanita'],
            ['ruangan_id' => 10, 'lantai_id' => 1, 'ruangan_kode' => 'R.05.10', 'ruangan_nama' => 'R. Teknisi'],
            ['ruangan_id' => 11, 'lantai_id' => 1, 'ruangan_kode' => 'R.05.11', 'ruangan_nama' => 'Toilet Pria'],

            // lantai 6
            ['ruangan_id' => 12, 'lantai_id' => 2, 'ruangan_kode' => 'R.06.01', 'ruangan_nama' => 'R. Dosen 1'],
            ['ruangan_id' => 13, 'lantai_id' => 2, 'ruangan_kode' => 'R.06.02', 'ruangan_nama' => 'R. Dosen 2'],
            ['ruangan_id' => 14, 'lantai_id' => 2, 'ruangan_kode' => 'R.06.03', 'ruangan_nama' => 'R. Dosen 3'],
            ['ruangan_id' => 15, 'lantai_id' => 2, 'ruangan_kode' => 'R.06.04', 'ruangan_nama' => 'R. Dosen 4'],
            ['ruangan_id' => 16, 'lantai_id' => 2, 'ruangan_kode' => 'R.06.05', 'ruangan_nama' => 'R. Jurusan TI'],
            ['ruangan_id' => 17, 'lantai_id' => 2, 'ruangan_kode' => 'R.06.06', 'ruangan_nama' => 'R. Dosen 5'],
            ['ruangan_id' => 18, 'lantai_id' => 2, 'ruangan_kode' => 'R.06.07', 'ruangan_nama' => 'R. Program Studi'],
            ['ruangan_id' => 19, 'lantai_id' => 2, 'ruangan_kode' => 'R.06.08', 'ruangan_nama' => 'R. Dosen 2'],
            ['ruangan_id' => 20, 'lantai_id' => 2, 'ruangan_kode' => 'R.06.09', 'ruangan_nama' => 'Toilet Wanita'],
            ['ruangan_id' => 21, 'lantai_id' => 2, 'ruangan_kode' => 'R.06.10', 'ruangan_nama' => 'Meeting Room 2'],
            ['ruangan_id' => 22, 'lantai_id' => 2, 'ruangan_kode' => 'R.06.11', 'ruangan_nama' => 'Toilet Pria'],
            ['ruangan_id' => 23, 'lantai_id' => 2, 'ruangan_kode' => 'R.06.12', 'ruangan_nama' => 'Musholla'],
            ['ruangan_id' => 24, 'lantai_id' => 2, 'ruangan_kode' => 'R.06.13', 'ruangan_nama' => 'Library'],
            ['ruangan_id' => 25, 'lantai_id' => 2, 'ruangan_kode' => 'R.06.14', 'ruangan_nama' => 'R. Arsip'],
            ['ruangan_id' => 26, 'lantai_id' => 2, 'ruangan_kode' => 'R.06.15', 'ruangan_nama' => 'Lab. Sistem Informasi 1'],
            ['ruangan_id' => 27, 'lantai_id' => 2, 'ruangan_kode' => 'R.06.16', 'ruangan_nama' => 'Lab. Sistem Informasi 2'],
            ['ruangan_id' => 28, 'lantai_id' => 2, 'ruangan_kode' => 'R.06.17', 'ruangan_nama' => 'Lab. Sistem Informasi 3'],
            ['ruangan_id' => 29, 'lantai_id' => 2, 'ruangan_kode' => 'R.06.18', 'ruangan_nama' => 'Lab. Proyek 2'],
            ['ruangan_id' => 30, 'lantai_id' => 2, 'ruangan_kode' => 'R.06.19', 'ruangan_nama' => 'Lab. Proyek 3'],
            ['ruangan_id' => 31, 'lantai_id' => 2, 'ruangan_kode' => 'R.06.20', 'ruangan_nama' => 'R. Ecosytem'],

            // lantai 7
            ['ruangan_id' => 32, 'lantai_id' => 3, 'ruangan_kode' => 'R.07.01', 'ruangan_nama' => 'Lab. Pemrograman 1'],
            ['ruangan_id' => 33, 'lantai_id' => 3, 'ruangan_kode' => 'R.07.02', 'ruangan_nama' => 'Lab. Pemrograman 2'],
            ['ruangan_id' => 34, 'lantai_id' => 3, 'ruangan_kode' => 'R.07.03', 'ruangan_nama' => 'Lab. Pemrograman 3'],
            ['ruangan_id' => 35, 'lantai_id' => 3, 'ruangan_kode' => 'R.07.04', 'ruangan_nama' => 'Lab. Pemrograman 4'],
            ['ruangan_id' => 36, 'lantai_id' => 3, 'ruangan_kode' => 'R.07.05', 'ruangan_nama' => 'Lab. Pemrograman 5'],
            ['ruangan_id' => 37, 'lantai_id' => 3, 'ruangan_kode' => 'R.07.06', 'ruangan_nama' => 'Lab. Pemrograman 6'],
            ['ruangan_id' => 38, 'lantai_id' => 3, 'ruangan_kode' => 'R.07.07', 'ruangan_nama' => 'Lab. Komputasi Jaringan 1'],
            ['ruangan_id' => 39, 'lantai_id' => 3, 'ruangan_kode' => 'R.07.08', 'ruangan_nama' => 'Lab. Pemrograman 3'],
            ['ruangan_id' => 40, 'lantai_id' => 3, 'ruangan_kode' => 'R.07.09', 'ruangan_nama' => 'Toilet Wanita'],
            ['ruangan_id' => 41, 'lantai_id' => 3, 'ruangan_kode' => 'R.07.10', 'ruangan_nama' => 'R. Teknisi'],
            ['ruangan_id' => 42, 'lantai_id' => 3, 'ruangan_kode' => 'R.07.11', 'ruangan_nama' => 'Toilet Pria'],
            ['ruangan_id' => 43, 'lantai_id' => 3, 'ruangan_kode' => 'R.07.12', 'ruangan_nama' => 'Kantin'],
            ['ruangan_id' => 44, 'lantai_id' => 3, 'ruangan_kode' => 'R.07.13', 'ruangan_nama' => 'Lab. Komputasi Jaringan 2'],
            ['ruangan_id' => 45, 'lantai_id' => 3, 'ruangan_kode' => 'R.07.14', 'ruangan_nama' => 'Lab. Pemrograman 8'],
            ['ruangan_id' => 46, 'lantai_id' => 3, 'ruangan_kode' => 'R.07.15', 'ruangan_nama' => 'Lab. Komputasi Jaringan 3'],
            ['ruangan_id' => 47, 'lantai_id' => 3, 'ruangan_kode' => 'R.07.16', 'ruangan_nama' => 'Lab. Visual Komputer 1'],
            ['ruangan_id' => 48, 'lantai_id' => 3, 'ruangan_kode' => 'R.07.17', 'ruangan_nama' => 'R. Kelas Teori 8'],
            ['ruangan_id' => 49, 'lantai_id' => 3, 'ruangan_kode' => 'R.07.18', 'ruangan_nama' => 'Lab. Visual Komputer 2'],
            ['ruangan_id' => 50, 'lantai_id' => 3, 'ruangan_kode' => 'R.07.19', 'ruangan_nama' => 'Lab. Proyek 4'],
            ['ruangan_id' => 51, 'lantai_id' => 3, 'ruangan_kode' => 'R.07.20', 'ruangan_nama' => 'Lab. Kecerdasan Buatan 1'],

            // lantai 8
            ['ruangan_id' => 52, 'lantai_id' => 4, 'ruangan_kode' => 'R.08.01', 'ruangan_nama' => 'R. Kelas Teori'],
            ['ruangan_id' => 53, 'lantai_id' => 4, 'ruangan_kode' => 'R.08.02', 'ruangan_nama' => 'Toilet Pria'],
            ['ruangan_id' => 54, 'lantai_id' => 4, 'ruangan_kode' => 'R.08.03', 'ruangan_nama' => 'Toilet Wanita'],
            ['ruangan_id' => 55, 'lantai_id' => 4, 'ruangan_kode' => 'R.08.04', 'ruangan_nama' => 'R. Teknisi'],
            ['ruangan_id' => 56, 'lantai_id' => 4, 'ruangan_kode' => 'R.08.05', 'ruangan_nama' => 'R. Kelas Teori 9'],
            ['ruangan_id' => 57, 'lantai_id' => 4, 'ruangan_kode' => 'R.08.06', 'ruangan_nama' => 'R. Kelas Teori 10'],
        ];
        DB::table('m_ruangan')->insert($ruangan);
    }
}
