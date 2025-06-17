<?php

namespace App\Http\Controllers;

use App\Models\LaporanModel;
use App\Models\RiwayatPerbaikan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Dashboard',
            'list'  => ['Home', 'Dashboard']
        ];

        $active_menu = 'dashboard';
        $card_data = $this->getCardData();
        $monthly_damage_data = $this->getMonthlyDamageData();
        $spk_data = $this->getSPKData(); // Tambahkan ini

        $satisfactionData = [
            RiwayatPerbaikan::where('rating', 1)->count(),
            RiwayatPerbaikan::where('rating', 2)->count(),
            RiwayatPerbaikan::where('rating', 3)->count(),
            RiwayatPerbaikan::where('rating', 4)->count(),
            RiwayatPerbaikan::where('rating', 5)->count()
        ];

        // Ambil daftar fasilitas (id => nama)
        $fasilitasList = \App\Models\FasilitasModel::pluck('fasilitas_nama', 'fasilitas_id')->toArray();

        return view('admin.dashboard', [
            'breadcrumb' => $breadcrumb,
            'active_menu' => $active_menu,
            'card_data' => $card_data,
            'monthly_damage_data' => $monthly_damage_data,
            'spkData' => collect($spk_data), // pastikan ini collection/array
            'fasilitasList' => $fasilitasList,
            'satisfactionData' => $satisfactionData,
        ]);
    }

    private function getCardData()
    {
        $data = [
            'total_laporan' => LaporanModel::count(),
            'menunggu_verifikasi' => LaporanModel::where('status_id', 1)->count(),
            'ditolak' => LaporanModel::where('status_id', 2)->count(),
            'diproses' => LaporanModel::where('status_id', 3)->count(),
            'selesai' => LaporanModel::where('status_id', 4)->count(),
        ];
        return $data;
    }

    private function getMonthlyDamageData()
    {
        $currentYear = date('Y');
        $monthlyData = [];

        // Inisialisasi array untuk 12 bulan (0-11 untuk index JavaScript)
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[$i] = 0;
        }

        // Query untuk menghitung jumlah fasilitas yang dilaporkan per bulan tahun ini
        // Menggunakan join antara m_laporan dan m_laporan_detail
        $reports = DB::table('m_laporan')
            ->join('m_laporan_detail', 'm_laporan.laporan_id', '=', 'm_laporan_detail.laporan_id')
            ->select(DB::raw('MONTH(tanggal_lapor) as month'), DB::raw('COUNT(m_laporan_detail.fasilitas_id) as total'))
            ->whereYear('tanggal_lapor', $currentYear)
            ->groupBy('month')
            ->get();

        // Mengisi data ke array hasil
        foreach ($reports as $report) {
            $monthlyData[$report->month] = $report->total;
        }

        // Mengembalikan array values saja (tanpa key)
        return array_values($monthlyData);
    }

    private function getSPKData()
    {
        try {
            // Eager load fasilitas, ruangan, and lantai relationships
            return \App\Models\RekomperbaikanModel::with(['fasilitas.ruangan.lantai'])
                ->orderBy('rank', 'asc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            Log::error('Error retrieving SPK data: ' . $e->getMessage());
            return [];
        }
    }
}
