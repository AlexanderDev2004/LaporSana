<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class m_status extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['status_id' => 1, 'status_nama' => 'menunggu verifikasi'],
            ['status_id' => 2, 'status_nama' => 'ditolak'],
            ['status_id' => 3, 'status_nama' => 'diproses'],
            ['status_id' => 4, 'status_nama' => 'selesai'],

        ];
        DB::table('m_status')->insert($statuses);
    }
}
