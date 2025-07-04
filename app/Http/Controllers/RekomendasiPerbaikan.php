<?php

namespace App\Http\Controllers;

use App\Models\FasilitasModel;
use App\Models\RekomperbaikanModel;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        try {
            // 1. Ambil 5 laporan dengan jumlah pelapor terbanyak
            $topLaporanIDs = DB::table('m_laporan')
                ->select('laporan_id')
                ->where('status_id', 6)
                ->orderByDesc('jumlah_pelapor')
                ->limit(10)
                ->pluck('laporan_id')
                ->toArray();

            // 2. Ambil fasilitas terkait dari laporan_detail
            $fasilitasData = DB::table('m_laporan_detail')
                ->join('m_fasilitas', 'm_laporan_detail.fasilitas_id', '=', 'm_fasilitas.fasilitas_id')
                ->whereIn('m_laporan_detail.laporan_id', $topLaporanIDs)
                ->select(
                    'm_fasilitas.fasilitas_id',
                    'm_fasilitas.fasilitas_nama as Alternatif',
                    'm_fasilitas.tingkat_urgensi as Urgensi',
                    DB::raw('(SELECT tingkat_kerusakan FROM m_tugas_detail WHERE fasilitas_id = m_fasilitas.fasilitas_id LIMIT 1) as Kerusakan'),
                    DB::raw('(SELECT biaya_perbaikan FROM m_tugas_detail WHERE fasilitas_id = m_fasilitas.fasilitas_id LIMIT 1) as Biaya_Perbaikan'),
                    DB::raw('(
                    SELECT COALESCE(SUM(r.poin_roles), 0)
                    FROM dukungan_laporan dl
                    JOIN m_user u ON dl.user_id = u.user_id
                    JOIN m_roles r ON u.roles_id = r.roles_id
                    WHERE dl.laporan_id = m_laporan_detail.laporan_id
                ) as Poin_Derajat'),
                    DB::raw('(SELECT jumlah_pelapor FROM m_laporan WHERE laporan_id = m_laporan_detail.laporan_id LIMIT 1) as Jumlah_Pelapor')
                )
                ->groupBy('m_fasilitas.fasilitas_id', 'm_fasilitas.fasilitas_nama', 'm_fasilitas.tingkat_urgensi', 'm_laporan_detail.laporan_id')
                ->limit(10) // Batasi 5 fasilitas saja
                ->get();

            // 3. Format data untuk API Flask
            $data = $fasilitasData->map(function ($item) {
                return [
                    'fasilitas_id' => $item->fasilitas_id,
                    'Alternatif' => $item->fasilitas_id,
                    'Urgensi' => (float) $item->Urgensi,
                    'Kerusakan' => (float) $item->Kerusakan,
                    'Jumlah Pelapor' => (float) $item->{'Jumlah_Pelapor'},
                    'Biaya Perbaikan' => (float) $item->{'Biaya_Perbaikan'},
                    'Poin Derajat' => (float) $item->{'Poin_Derajat'},
                ];
            })->toArray();

            $response = Http::timeout(10)->post('http://127.0.0.1:5001/spk/calculate', [
                'data' => $data
            ]);

            if ($response->successful()) {
                // Ambil response sebagai object, bukan array
                $responseObject = json_decode($response->body());
                RekomperbaikanModel::truncate();
                // Dump hasilnya untuk debugging
                foreach ($responseObject->ranking as $i => $item) {
                    RekomperbaikanModel::create([
                        'fasilitas_id' => $item->Alternatif,
                        'rank' => $item->Ranking,
                        'score_ranking' => $item->AppraisalScore
                    ]);
                }

                // dd($responseObject->ranking);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal menghubungi server Flask',
                    'error' => $response->body()
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghitung SPK',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'data' => $response->json()
        ]);
    }

    public function tampilkanSPK()
    {
        $spkData = RekomperbaikanModel::orderBy('rank')->get();
        $fasilitasList = FasilitasModel::pluck('fasilitas_nama', 'fasilitas_id')->toArray();

        // Tampilkan ke dua view: Admin dan Satpras
        // return view('Admin.dashboard', compact('spkData', 'fasilitasList'));
        return view('Admin.dashboard', compact('spkData', 'fasilitasList'))
            ->with('satprasView', view('sarpras.dashboard', compact('spkData', 'fasilitasList'))->render());
    }

    // Fungsi untuk tombol "Perbarui Data"
    public function perbaruiData(Request $request)
    {
        try {
            $response = $this->hitungSPK();

            if ($response instanceof \Illuminate\Http\JsonResponse) {
                $responseData = $response->getData(true);
                if (isset($responseData['status']) && $responseData['status'] === 'error') {
                    return redirect()->back()->with([
                        'error' => $responseData['message'] ?? 'Terjadi kesalahan saat memperbarui data!',
                    ]);
                }
            }

            $fasilitasList = FasilitasModel::pluck('fasilitas_nama', 'fasilitas_id')->toArray();
            $spkData = RekomperbaikanModel::orderBy('rank')->get();

            // Ambil role user yang sedang login
            $userRole = Auth::user()->role_id; // Pastikan field-nya benar

            // Redirect sesuai role
            if ($userRole == 1) {
                return redirect()->route('admin.dashboard')->with([
                    'spkData' => $spkData,
                    'fasilitasList' => $fasilitasList,
                    'success' => 'Data berhasil diperbarui!'
                ]);
            } elseif ($userRole == 5) {
                return redirect()->route('sarpras.dashboard')->with([
                    'spkData' => $spkData,
                    'fasilitasList' => $fasilitasList,
                    'success' => 'Data berhasil diperbarui!'
                ]);
            } else {
                return redirect()->back()->with([
                    'error' => 'Role tidak dikenali.'
                ]);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function showStepByStep()
    {
        try {
            // Ambil 10 laporan dengan jumlah pelapor terbanyak (sama seperti hitungSPK)
            $topLaporanIDs = DB::table('m_laporan')
                ->select('laporan_id')
                ->where('status_id', 6)
                ->orderByDesc('jumlah_pelapor')
                ->limit(10)
                ->pluck('laporan_id')
                ->toArray();

            $fasilitasData = DB::table('m_laporan_detail')
                ->join('m_fasilitas', 'm_laporan_detail.fasilitas_id', '=', 'm_fasilitas.fasilitas_id')
                ->whereIn('m_laporan_detail.laporan_id', $topLaporanIDs)
                ->select(
                    'm_fasilitas.fasilitas_id',
                    'm_fasilitas.fasilitas_nama as Alternatif',
                    'm_fasilitas.tingkat_urgensi as Urgensi',
                    DB::raw('(SELECT tingkat_kerusakan FROM m_tugas_detail WHERE fasilitas_id = m_fasilitas.fasilitas_id LIMIT 1) as Kerusakan'),
                    DB::raw('(SELECT biaya_perbaikan FROM m_tugas_detail WHERE fasilitas_id = m_fasilitas.fasilitas_id LIMIT 1) as Biaya_Perbaikan'),
                    DB::raw('(
                    SELECT COALESCE(SUM(r.poin_roles), 0)
                    FROM dukungan_laporan dl
                    JOIN m_user u ON dl.user_id = u.user_id
                    JOIN m_roles r ON u.roles_id = r.roles_id
                    WHERE dl.laporan_id = m_laporan_detail.laporan_id
                ) as Poin_Derajat'),
                    DB::raw('(SELECT jumlah_pelapor FROM m_laporan WHERE laporan_id = m_laporan_detail.laporan_id LIMIT 1) as Jumlah_Pelapor')
                )
                ->groupBy('m_fasilitas.fasilitas_id', 'm_fasilitas.fasilitas_nama', 'm_fasilitas.tingkat_urgensi', 'm_laporan_detail.laporan_id')
                ->limit(10)
                ->get();

            $data = $fasilitasData->map(function ($item) {
                return [
                    'fasilitas_id' => $item->fasilitas_id,
                    'Alternatif' => $item->fasilitas_id,
                    'Urgensi' => (float) $item->Urgensi,
                    'Kerusakan' => (float) $item->Kerusakan,
                    'Jumlah Pelapor' => (float) $item->{'Jumlah_Pelapor'},
                    'Biaya Perbaikan' => (float) $item->{'Biaya_Perbaikan'},
                    'Poin Derajat' => (float) $item->{'Poin_Derajat'},
                ];
            })->toArray();

            // Panggil API Flask untuk dapatkan step-by-step
            $response = Http::timeout(10)->post('http://127.0.0.1:5001/spk/calculate', [
                'data' => $data
            ]);

            if (!$response->successful()) {
                return back()->with('error', 'Gagal mengambil data dari server SPK');
            }

            $result = $response->json();

            // Ambil data fasilitas untuk mapping nama
            $fasilitasList = \App\Models\FasilitasModel::pluck('fasilitas_nama', 'fasilitas_id')->toArray();

            $breadcrumb = (object) [
                'title' => 'Step by Step SPK',
                'list'  => ['Home', 'SPK']
            ];
            // Kirim ke view
            return view('Admin.spk.step_by_step', [
                'spkData' => $result['ranking'] ?? [],
                'psiSteps' => $result['psi_steps'] ?? [],
                'edasSteps' => $result['edas_steps'] ?? [],
                'fasilitasList' => $fasilitasList,
                'active_menu' => 'spk_step_by_step',
                'breadcrumb' => $breadcrumb
            ]);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi error: ' . $e->getMessage());
        }
    }
}
