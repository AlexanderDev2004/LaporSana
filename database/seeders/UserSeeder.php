<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['user_id' => 1, 'roles_id' => 1, 'username' => 'AdminJTI', 'nama' => 'AdminJti', 'password' => Hash::make('AdminJti123'), 'NIM' => null, 'NIP' => 111111111111111111, 'avatar' => null],
            ['user_id' => 2, 'roles_id' => 1, 'username' => 'SuperAdminTes', 'nama' => 'SuperAdmin', 'password' => Hash::make('AdminSuperjti12345'), 'NIM' => null, 'NIP' => 222222222222222222, 'avatar' => null],
            ['user_id' => 3, 'roles_id' => 2, 'username' => 'TestingMHS', 'nama' => 'testing', 'password' => Hash::make('12345678'), 'NIM' => 1111111112, 'NIP' => null, 'avatar' => null],
            ['user_id' => 4, 'roles_id' => 2, 'username' => 'Alexander', 'nama' => 'Alex', 'password' => Hash::make('Alexander1234'), 'NIM' => 2341720040, 'NIP' => null, 'avatar' => null],
            ['user_id' => 5, 'roles_id' => 2, 'username' => 'juan', 'nama' => 'juan', 'password' => Hash::make('NathanJuan1234'), 'NIM' =>  2341720217, 'NIP' => null, 'avatar' => null],
            ['user_id' => 6, 'roles_id' => 2, 'username' => 'IrsaCahaya', 'nama' => 'Irsa', 'password' => Hash::make('IrsaCahaya1234'), 'NIM' =>  2341720193, 'NIP' => null, 'avatar' => null],
            ['user_id' => 7, 'roles_id' => 2, 'username' => 'Fatikah', 'nama' => 'Fatikah', 'password' => Hash::make('Fatikah1234'), 'NIM' =>  2341720003, 'NIP' => null, 'avatar' => null],
            ['user_id' => 8, 'roles_id' => 2, 'username' => 'Danendra', 'nama' => 'Danendra', 'password' => Hash::make('Danendra1234'), 'NIM' =>  244107023011, 'NIP' => null, 'avatar' => null],
            ['user_id' => 9, 'roles_id' => 3, 'username' => 'TestingDSN', 'nama' => 'testingDSN', 'password' => Hash::make('DSN12345678'), 'NIM' => null, 'NIP' => 333333333333333333, 'avatar' => null],
            ['user_id' => 10, 'roles_id' => 3, 'username' => 'MochZawaruddin', 'nama' => 'Moch. Zawaruddin Abdullah', 'password' => Hash::make('MochZawaruddin12345678'), 'NIM' => null, 'NIP' => 198902102019031019, 'avatar' => null],
            ['user_id' => 11, 'roles_id' => 4, 'username' => 'TestingTNDK', 'nama' => 'TestingTNDK', 'password' => Hash::make('Tendik12345678'), 'NIM' => null, 'NIP' => 444444444444444444, 'avatar' => null],
            ['user_id' => 12, 'roles_id' => 5, 'username' => 'TestingSPA', 'nama' => 'TestingSPA', 'password' => Hash::make('SPA12345678'), 'NIM' => null, 'NIP' => 555555555555555555, 'avatar' => null],
            ['user_id' => 13, 'roles_id' => 6, 'username' => 'TestingTSI', 'nama' => 'TestingTSI', 'password' => Hash::make('TSI12345678'), 'NIM' => null, 'NIP' => 666666666666666666, 'avatar' => null],


        ];
        DB::table('m_user')->insert($data);
    }
}
