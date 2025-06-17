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
                ->where('status_id', 3)
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
                    DB::raw('(SELECT poin_roles FROM m_roles WHERE roles_id = (SELECT roles_id FROM m_user WHERE user_id = (SELECT user_id FROM m_laporan WHERE laporan_id = m_laporan_detail.laporan_id LIMIT 1) LIMIT 1) LIMIT 1) as Poin_Derajat'),
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

    public function tampilkanStepSPK(Request $request)
{
    try {

        return view('admin.spk.index');
        
        $valid = $request->validate([
            'fasilitas_id' => 'required|integer'
        ]);

        // Get facility data from database (example query - adjust as needed)
        $laporanData = DB::table('m_laporan')
            ->join('m_laporan_detail', 'm_laporan.id', '=', 'm_laporan_detail.laporan_id')
            ->join('m_tugas_detail', 'm_laporan_detail.id', '=', 'm_tugas_detail.laporan_detail_id')
            ->where('m_laporan_detail.fasilitas_id', $valid['fasilitas_id'])
            ->select(
                'm_laporan_detail.fasilitas_id',
                'm_laporan_detail.tingkat_urgensi as Urgensi',
                'm_tugas_detail.tingkat_kerusakan as Kerusakan',
                DB::raw('COUNT(m_laporan.id) as Jumlah_Pelapor'),
                'm_tugas_detail.biaya_perbaikan as Biaya_Perbaikan',
                DB::raw('(SELECT poin FROM m_roles WHERE id = m_laporan.user_id) as Poin_Derajat')
            )
            ->groupBy('m_laporan_detail.fasilitas_id', 'm_laporan_detail.tingkat_urgensi',
                     'm_tugas_detail.tingkat_kerusakan', 'm_tugas_detail.biaya_perbaikan', 'm_laporan.user_id')
            ->get();

        if ($laporanData->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Tidak ada data laporan untuk fasilitas ini.'
            ], 404);
        }

        // Prepare data for SPK calculation
        $data = $laporanData->map(function ($item) {
            return [
                'fasilitas_id' => $item->fasilitas_id,
                'Alternatif' => $item->fasilitas_id, // Using facility ID as alternative ID
                'Urgensi' => (int)$item->Urgensi,
                'Kerusakan' => (int)$item->Kerusakan,
                'Jumlah Pelapor' => (int)$item->Jumlah_Pelapor,
                'Biaya Perbaikan' => (float)$item->Biaya_Perbaikan,
                'Poin Derajat' => (int)$item->Poin_Derajat
            ];
        })->toArray();

        // ================ PSI METHOD ================
        // 1. Normalization
        $normalizedData = [];
        $criteriaTypes = [
            'Urgensi' => 'benefit',
            'Kerusakan' => 'benefit',
            'Jumlah Pelapor' => 'benefit',
            'Biaya Perbaikan' => 'cost',
            'Poin Derajat' => 'benefit'
        ];

        // Get max and min for each criterion
        $maxValues = [];
        $minValues = [];
        foreach ($criteriaTypes as $criterion => $type) {
            $values = array_column($data, $criterion);
            $maxValues[$criterion] = max($values);
            $minValues[$criterion] = min($values);
        }

        // Normalize
        foreach ($data as $item) {
            $normalizedItem = ['Alternatif' => $item['Alternatif']];
            foreach ($criteriaTypes as $criterion => $type) {
                if ($type == 'benefit') {
                    $normalizedItem[$criterion] = $item[$criterion] / $maxValues[$criterion];
                } else {
                    $normalizedItem[$criterion] = $minValues[$criterion] / $item[$criterion];
                }
            }
            $normalizedData[] = $normalizedItem;
        }

        // 2. Calculate mean (Ēₖ)
        $means = [];
        foreach ($criteriaTypes as $criterion => $type) {
            $values = array_column($normalizedData, $criterion);
            $means[$criterion] = array_sum($values) / count($values);
        }

        // 3. Preference Variation (PVₖ)
        $pv = [];
        foreach ($criteriaTypes as $criterion => $type) {
            $sum = 0;
            foreach ($normalizedData as $item) {
                $sum += pow($item[$criterion] - $means[$criterion], 2);
            }
            $pv[$criterion] = $sum;
        }

        // 4. Deviation (Φₖ)
        $phi = [];
        foreach ($pv as $criterion => $value) {
            $phi[$criterion] = abs(1 - $value);
        }

        // 5. Weight (ψₖ)
        $totalPhi = array_sum($phi);
        $weights = [];
        foreach ($phi as $criterion => $value) {
            $weights[$criterion] = $value / $totalPhi;
        }

        // ================ EDAS METHOD ================
        // 1. Average solution (AVG)
        $avgSolution = [];
        foreach ($criteriaTypes as $criterion => $type) {
            $values = array_column($data, $criterion);
            $avgSolution[$criterion] = array_sum($values) / count($values);
        }

        // 2. Calculate PDA and NDA
        $pda = [];
        $nda = [];
        foreach ($data as $item) {
            $altId = $item['Alternatif'];
            $pda[$altId] = [];
            $nda[$altId] = [];

            foreach ($criteriaTypes as $criterion => $type) {
                $value = $item[$criterion];
                $avg = $avgSolution[$criterion];

                if ($type == 'benefit') {
                    $pda[$altId][$criterion] = max(0, ($value - $avg) / $avg);
                    $nda[$altId][$criterion] = max(0, ($avg - $value) / $avg);
                } else {
                    $pda[$altId][$criterion] = max(0, ($avg - $value) / $avg);
                    $nda[$altId][$criterion] = max(0, ($value - $avg) / $avg);
                }
            }
        }

        // 3. Weighted SP and SN
        $weightedSP = [];
        $weightedSN = [];
        foreach ($data as $item) {
            $altId = $item['Alternatif'];
            $weightedSP[$altId] = 0;
            $weightedSN[$altId] = 0;

            foreach ($criteriaTypes as $criterion => $type) {
                $weightedSP[$altId] += $pda[$altId][$criterion] * $weights[$criterion];
                $weightedSN[$altId] += $nda[$altId][$criterion] * $weights[$criterion];
            }
        }

        // 4. Normalized SP and SN
        $maxSP = max($weightedSP);
        $maxSN = max($weightedSN);

        $nsp = [];
        $nsn = [];
        foreach ($weightedSP as $altId => $value) {
            $nsp[$altId] = $value / $maxSP;
            $nsn[$altId] = 1 - ($weightedSN[$altId] / $maxSN);
        }

        // 5. Appraisal Score
        $appraisalScores = [];
        foreach ($nsp as $altId => $value) {
            $appraisalScores[$altId] = 0.5 * $value + 0.5 * $nsn[$altId];
        }

        // 6. Ranking
        arsort($appraisalScores);
        $ranking = [];
        $rank = 1;
        foreach ($appraisalScores as $altId => $score) {
            $ranking[] = [
                'Alternatif' => $altId,
                'AppraisalScore' => $score,
                'Ranking' => $rank++
            ];
        }

        return response()->json([
            'status' => true,
            'data' => [
                'psi_steps' => [
                    'normalized_data' => $normalizedData,
                    'means' => $means,
                    'preference_variation' => $pv,
                    'deviation' => $phi,
                    'weights' => $weights
                ],
                'edas_steps' => [
                    'average_solution' => $avgSolution,
                    'pda' => $pda,
                    'nda' => $nda,
                    'weighted_sp' => $weightedSP,
                    'weighted_sn' => $weightedSN,
                    'nsp' => $nsp,
                    'nsn' => $nsn,
                    'appraisal_scores' => $appraisalScores
                ],
                'ranking' => $ranking
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}
}
