<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class m_fasilitas extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'fasilitas_id' => 1,
                'ruangan_id' => 1,
                'fasilitas_kode' => 'F001',
                'fasilitas_nama' => 'Proyektor',
            ],
            [
                'fasilitas_id' => 2,
                'ruangan_id' => 2,
                'fasilitas_kode' => 'F002',
                'fasilitas_nama' => 'AC',
            ],
            [
                'fasilitas_id' => 3,
                'ruangan_id' => 3,
                'fasilitas_kode' => 'F003',
                'fasilitas_nama' => 'Whiteboard',
            ],
            [
                'fasilitas_id' => 4,
                'ruangan_id' => 4,
                'fasilitas_kode' => 'F004',
                'fasilitas_nama' => 'Kursi Lipat',
            ],
            [
                'fasilitas_id' => 5,
                'ruangan_id' => 5,
                'fasilitas_kode' => 'F005',
                'fasilitas_nama' => 'Meja Kayu',
            ],
            [
                'fasilitas_id' => 6,
                'ruangan_id' => 6,
                'fasilitas_kode' => 'F006',
                'fasilitas_nama' => 'Speaker',
            ],
            [
                'fasilitas_id' => 7,
                'ruangan_id' => 7,
                'fasilitas_kode' => 'F007',
                'fasilitas_nama' => 'Mic Wireless',
            ],
            [
                'fasilitas_id' => 8,
                'ruangan_id' => 8,
                'fasilitas_kode' => 'F008',
                'fasilitas_nama' => 'TV LED',
            ],
            [
                'fasilitas_id' => 9,
                'ruangan_id' => 9,
                'fasilitas_kode' => 'F009',
                'fasilitas_nama' => 'Papan Tulis Kaca',
            ],
            [
                'fasilitas_id' => 10,
                'ruangan_id' => 10,
                'fasilitas_kode' => 'F010',
                'fasilitas_nama' => 'Lampu LED',
            ],
        ];

        DB::table('m_fasilitas')->insert($data);
    }
}

