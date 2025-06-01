<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class m_kriteria extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kriteriaData = [
            [
                'kriteria_id' => 1,
                'kriteria_kode' => 'K001',
                'kriteria_nama' => 'Tingkat Urgensi',
                'kriteria_bobot' => 0.2,
            ],
            [
                'kriteria_id' => 2,
                'kriteria_kode' => 'K002',
                'kriteria_nama' => 'Tingkat Kerusakan',
                'kriteria_bobot' => 0.25,
            ],
            [
                'kriteria_id' => 3,
                'kriteria_kode' => 'K003',
                'kriteria_nama' => 'Lama Perbaikan',
                'kriteria_bobot' => 0.15,
            ],
            [
                'kriteria_id' => 4,
                'kriteria_kode' => 'K004',
                'kriteria_nama' => 'Jumlah Pelapor',
                'kriteria_bobot' => 0.15,
            ],
            [
                'kriteria_id' => 5,
                'kriteria_kode' => 'K005',
                'kriteria_nama' => 'Biaya Perbaikan',
                'kriteria_bobot' => 0.2,
            ],
            [
                'kriteria_id' => 6,
                'kriteria_kode' => 'K006',
                'kriteria_nama' => 'Poin by Derajat Role User',
                'kriteria_bobot' => 0.25,
            ],
        ];

        DB::table('m_kriteria')->insert($kriteriaData);
    }
}

