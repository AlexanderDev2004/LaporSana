<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class RekomendasiPerbaikan extends Controller
{
    public function hitungSPK()
    {
        $data = [
            [
                'Alternatif' => 'A1',
                'Urgensi' => 4,
                'Kerusakan' => 3,
                'Jumlah Pelapor' => 5,
                'Biaya Perbaikan' => 10,
                'Poin Derajat' => 15,
            ],
            [
                'Alternatif' => 'A2',
                'Urgensi' => 2,
                'Kerusakan' => 4,
                'Jumlah Pelapor' => 3,
                'Biaya Perbaikan' => 3.0,
                'Poin Derajat' => 3,
            ],
            [
                'Alternatif' => 'A3',
                'Urgensi' => 3,
                'Kerusakan' => 2,
                'Jumlah Pelapor' => 4,
                'Biaya Perbaikan' => 1.5,
                'Poin Derajat' => 5
                ,
                'Alternatif' => 'A20',
                'Urgensi' => 3,
                'Kerusakan' => 4,
                'Jumlah Pelapor' => 5,
                'Biaya Perbaikan' => 4.5,
                'Poin Derajat' => 15,
            ],
        ];

        $response = Http::post('http://127.0.0.1:5001/spk/calculate', [
            'data' => $data
        ]);

        $result = $response->json();

        return response()->json($result);
    }
}
