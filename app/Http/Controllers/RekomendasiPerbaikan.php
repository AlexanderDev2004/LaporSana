<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekomendasiPerbaikan extends Controller
{
    // public function hitungSPK()
    // {
    //     $data = [
    //         [
    //             'Alternatif' => 'A1',
    //             'Urgensi' => 4,
    //             'Kerusakan' => 3,
    //             'Jumlah Pelapor' => 5,
    //             'Biaya Perbaikan' => 10,
    //             'Poin Derajat' => 15,
    //         ],
    //         [
    //             'Alternatif' => 'A2',
    //             'Urgensi' => 2,
    //             'Kerusakan' => 4,
    //             'Jumlah Pelapor' => 3,
    //             'Biaya Perbaikan' => 3.0,
    //             'Poin Derajat' => 3,
    //         ],
    //         [
    //             'Alternatif' => 'A3',
    //             'Urgensi' => 3,
    //             'Kerusakan' => 2,
    //             'Jumlah Pelapor' => 4,
    //             'Biaya Perbaikan' => 1.5,
    //             'Poin Derajat' => 5,
    //         ],
    //         [
    //             'Alternatif' => 'A20',
    //             'Urgensi' => 3,
    //             'Kerusakan' => 4,
    //             'Jumlah Pelapor' => 5,
    //             'Biaya Perbaikan' => 4.5,
    //             'Poin Derajat' => 15,
    //         ],
    //         [
    //             'Alternatif' => 'A4',
    //             'Urgensi' => 5,
    //             'Kerusakan' => 3,
    //             'Jumlah Pelapor' => 2,
    //             'Biaya Perbaikan' => 6.0,
    //             'Poin Derajat' => 10,
    //         ],
    //         [
    //             'Alternatif' => 'A5',
    //             'Urgensi' => 4,
    //             'Kerusakan' => 5,
    //             'Jumlah Pelapor' => 1,
    //             'Biaya Perbaikan' => 2.0,
    //             'Poin Derajat' => 8,
    //         ],
    //     ];

    //     $response = Http::post('http://127.0.0.1:5001/spk/calculate', [
    //         'data' => $data
    //     ]);

    //     $result = $response->json();

    //     return response()->json($result);
    // }

    public function hitungSPK()
    {
        $laporanData = DB::table('m_laporan')
            ->join('users', 'm_laporan.user_id', '=', 'users.id')
            ->join('m_roles', 'users.role_id', '=', 'm_roles.id')
            ->join('m_tugas_detail', 'm_laporan.tugas_detail_id', '=', 'm_tugas_detail.id')
            ->join('m_fasilitas', 'm_tugas_detail.fasilitas_id', '=', 'm_fasilitas.id')
            ->select(
                'm_fasilitas.id as Alternatif',
                'm_fasilitas.tingkat_urgensi as Urgensi',
                'm_tugas_detail.tingkat_kerusakan as Kerusakan',
                DB::raw('COUNT(m_laporan.id) as Jumlah_Pelapor'),
                'm_tugas_detail.biaya_perbaikan as Biaya_Perbaikan',
                'm_roles.poin_roles as Poin_Derajat'
            )
            ->groupBy(
                'm_fasilitas.id',
                'm_fasilitas.tingkat_urgensi',
                'm_tugas_detail.tingkat_kerusakan',
                'm_tugas_detail.biaya_perbaikan',
                'm_roles.poin_roles'
            )
            ->get();

        // Transform hasil query ke array sesuai format Python
        $data = $laporanData->map(function ($item, $index) {
            return [
                'Alternatif' => 'A' . ($index + 1),
                'Urgensi' => (float) $item->Urgensi,
                'Kerusakan' => (float) $item->Kerusakan,
                'Jumlah Pelapor' => (float) $item->Jumlah_Pelapor,
                'Biaya Perbaikan' => (float) $item->Biaya_Perbaikan,
                'Poin Derajat' => (float) $item->Poin_Derajat,
            ];
        })->toArray();

        // Kirim data ke Python API
        $response = Http::post('http://127.0.0.1:5001/spk/calculate', [
            'data' => $data
        ]);

        return response()->json($response->json());
    }
}
