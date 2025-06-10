<?php

namespace App\Http\Controllers;

use App\Models\LaporanModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        return view('admin.dashboard', compact('breadcrumb', 'active_menu', 'card_data', 'monthly_damage_data'));
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
}
