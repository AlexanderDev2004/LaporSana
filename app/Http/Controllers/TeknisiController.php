<?php

namespace App\Http\Controllers;

use App\Models\StatusModel;
use App\Models\TugasDetail;
use App\Models\TugasModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TeknisiController extends Controller
{

    public function dashboard(Request $request)
    {
        $breadcrumb = (object) [
            'title' => 'Dashboard',
            'list'  => ['Home', 'Dashboard']
        ];

        $active_menu = 'dashboard';

        // tugas terbaru yang aktif kecuali status_id != dibatalkan)
        $tugasTerbaru = TugasModel::whereHas('status', function ($q) {
            $q->where('status_nama', '!=', ['dibatalkan', 'selesai']);
        })
            ->orderBy('tugas_mulai', 'desc')
            ->limit(2)
            ->get();

        // jumlah tugas per bulan dalam tahun berjalan
        $tahunIni = date('Y');
        $statistik = TugasModel::selectRaw('MONTH(tugas_mulai) as bulan, COUNT(*) as jumlah')
            ->whereYear('tugas_mulai', $tahunIni)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        //rray 12 bulan dengan default 0 jumlah
        $dataStatistik = array_fill(1, 12, 0);
        foreach ($statistik as $item) {
            $dataStatistik[(int)$item->bulan] = $item->jumlah;
        }

        return view('teknisi.dashboard', compact('breadcrumb', 'active_menu', 'tugasTerbaru', 'dataStatistik'));
    }


    public function index(Request $request)
    {
        $active_menu = 'index';
        $breadcrumb = (object) [
            'title' => 'Daftar Tugas',
            'list' => ['Home', 'Tugas']
        ];

        $tugas = TugasModel::with(['status', 'user'])
            ->whereHas('status', function ($query) {
                $query->where('status_nama', '!=', 'selesai'); // kecuali yang selesai
            });

        if ($request->filled('status')) {
            $tugas->where('status_id', $request->status);
        }

        $status = StatusModel::all();
        $user = UserModel::all();

        return view('teknisi.index', compact('user', 'status', 'tugas', 'active_menu', 'breadcrumb'));
    }


    public function list(Request $request)
    {
        $tugas = TugasModel::with(['status', 'user'])
            ->whereHas('status', function ($query) {
                $query->where('status_nama', '!=', 'selesai');
            });


        // Filter status
        if ($request->has('filter_status') && $request->filter_status != '') {
            $tugas->where('status_id', $request->filter_status);
        }

        return DataTables::of($tugas->get())
            ->addIndexColumn()
            ->addColumn('aksi', function ($tugas) {
                // Gabungkan semua tombol aksi

                $btn = '<button onclick="modalAction(\'' . route('teknisi.show', $tugas->tugas_id) . '\')" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></button>';
                $btn .= '<button onclick="modalAction(\'' . route('teknisi.edit', $tugas->tugas_id) . '\')" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></button>';
                $btn .= '<button onclick="modalAction(\'' . route('teknisi.destroy', $tugas->tugas_id) . '\')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>';
                return $btn;
            })
            ->rawColumns(['aksi']) // Kolom aksi mengandung HTML
            ->toJson(); // Pastikan mengembalikan JSON
    }

    public function show($id)
    {
        $tugas = TugasDetail::with(['tugas', 'fasilitas'])->find($id);
        $breadcrumb = (object) [
            'title' => 'Detail Tugas',
            'list'  => ['Home', 'Tugas', 'Detail']
        ];
        $active_menu = 'tugas';
        return view('teknisi.show', compact('breadcrumb', 'active_menu', 'tugas'));
    }

    public function riwayat()
    {
        $breadcrumb = (object) [
            'title' => 'Riwayat Tugas',
            'list'  => ['Home', 'Riwayat Tugas']
        ];
        $active_menu = 'riwayat';

        // Kita bisa kirim data status untuk filter juga, misal semua status selesai
        $status = StatusModel::all();

        return view('teknisi.riwayat', compact('breadcrumb', 'active_menu', 'status'));
    }

    public function riwayatList(Request $request)
    {
        $tugas = TugasModel::with(['status', 'user'])
            ->whereHas('status', function ($query) {
                $query->where('status_nama', 'selesai'); // hanya yang status selesai
            });

        return DataTables::of($tugas->get())
            ->addIndexColumn()
            ->addColumn('aksi', function ($tugas) {
                $btn = '<button onclick="modalAction(\'' . route('teknisi.show', $tugas->tugas_id) . '\')" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></button>';
                return $btn; // di riwayat biasanya hanya lihat detail
            })
            ->rawColumns(['aksi'])
            ->toJson();
    }
}
