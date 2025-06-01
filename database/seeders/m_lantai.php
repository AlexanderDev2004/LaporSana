<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class m_lantai extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['lantai_id' => 1 , 'lantai_kode' => 'LT5' , 'lantai_nama' => 'lantai 5'],
            ['lantai_id' => 2 , 'lantai_kode' => 'LT6' , 'lantai_nama' => 'lantai 6'],
            ['lantai_id' => 3 , 'lantai_kode' => 'LT7' , 'lantai_nama' => 'lantai 7'],
            ['lantai_id' => 4 , 'lantai_kode' => 'LT8' , 'lantai_nama' => 'lantai 8'],
        ];
        DB::table('m_lantai')->insert($data);
    }

}
