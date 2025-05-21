<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $data = [
            ['roles_id' => 1, 'roles_kode' => 'ADM' , 'roles_nama' => 'Admin'],
            ['roles_id' => 2, 'roles_kode' => 'MHS' , 'roles_nama' => 'Mahasiswa'],
            ['roles_id' => 3, 'roles_kode' => 'DSN ' , 'roles_nama' => 'Dosen'],
            ['roles_id' => 4, 'roles_kode' => 'TNDK  ' , 'roles_nama' => 'Tendik'],
            ['roles_id' => 5, 'roles_kode' => 'SPA   ' , 'roles_nama' => 'Sarana Prasarana'],
            ['roles_id' => 6, 'roles_kode' => 'TSI   ' , 'roles_nama' => 'Teknisi'],
        ];
        DB::table('m_roles')->insert($data);
    }

}
