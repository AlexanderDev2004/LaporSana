<?php

namespace App\Http\Controllers;

use App\Models\LaporanModel;
use App\Models\LaporanDetail;
use App\Models\LantaiModel;
use App\Models\RuanganModel;
use App\Models\FasilitasModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PelaporController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Selamat Datang',
            'list'  => ['Home', 'Welcome']
        ];

        $active_menu = 'dashboard';
        return view('pelapor.dashboard', compact('breadcrumb', 'active_menu'));
    }

    public function laporan()
    {
        $breadcrumb = (object) [
            'title' => 'Laporan Kerusakan Fasilitas',
            'list'  => ['Home', 'Laporan Saya']
        ];

        $page = (object) [
            'title' => 'Daftar Laporan Kerusakan'
        ];

        $active_menu = 'laporan saya';

        return view('pelapor.laporan', compact('breadcrumb', 'page', 'active_menu'));
    }

    public function list(Request $request)
    {
        $laporans = LaporanModel::with(['details.fasilitas.ruangan.lantai', 'status'])
            ->where('user_id', auth()->user()->user_id);

        return DataTables::of($laporans)
            ->addIndexColumn()
            ->editColumn('status.status_nama', function ($laporan) {
                $status = $laporan->status->status_nama ?? 'Tidak Diketahui';
                switch ($laporan->status_id) {
                    case 1: return '<span class="badge badge-warning">' . $status . '</span>';
                    case 2: return '<span class="badge badge-danger">' . $status . '</span>';
                    case 3: return '<span class="badge badge-info">' . $status . '</span>';
                    case 4: return '<span class="badge badge-success">' . $status . '</span>';
                    default: return '<span class="badge badge-secondary">' . $status . '</span>';
                }
            })
            ->addColumn('aksi', function ($laporan) {
                $detailUrl = route('pelapor.show', ['laporan_id' => $laporan->laporan_id]);
                $btn = '<button onclick="modalAction(\''.$detailUrl.'\')" class="btn btn-info btn-sm">Detail</button> ';
                return $btn;
            })
            ->rawColumns(['status.status_nama', 'aksi'])
            ->make(true);
    }

    public function create()
    {
        $lantai = LantaiModel::all();
        $ruangan = RuanganModel::all();
        $fasilitas = FasilitasModel::all();

        return view('pelapor.create', compact('lantai', 'ruangan', 'fasilitas'));
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lantai_id' => 'required|exists:m_lantai,lantai_id',
            'ruangan_id' => 'required|exists:m_ruangan,ruangan_id',
            'fasilitas_id' => 'required|exists:m_fasilitas,fasilitas_id',
            'foto_bukti' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240', // Batas sudah 10MB
            'deskripsi' => 'required|string|min:10|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Validasi Gagal', 'msgField' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $existingLaporan = LaporanModel::whereHas('details', function ($query) use ($validated) {
            $query->where('fasilitas_id', $validated['fasilitas_id'])
                ->whereHas('fasilitas', function ($subQuery) use ($validated) {
                    $subQuery->where('ruangan_id', $validated['ruangan_id'])
                        ->whereHas('ruangan', function ($subSubQuery) use ($validated) {
                            $subSubQuery->where('lantai_id', $validated['lantai_id']);
                        });
                });
        })->whereIn('status_id', [3])
            ->first();

        if ($existingLaporan) {
            $existingLaporan->jumlah_pelapor += 1;
            $existingLaporan->save();

            return response()->json(['status' => true, 'message' => 'Laporan serupa sudah ada. Anda telah ditambahkan sebagai pelapor.']);
        }

        $laporan = new LaporanModel();
        $laporan->user_id = auth()->user()->user_id;
        $laporan->status_id = 1;
        $laporan->tanggal_lapor = now();
        $laporan->jumlah_pelapor = 1;
        $laporan->save();

        $detail = new LaporanDetail();
        $detail->laporan_id = $laporan->laporan_id;
        $detail->fasilitas_id = $validated['fasilitas_id'];
        $detail->deskripsi = $validated['deskripsi'];
        if ($request->hasFile('foto_bukti')) {
            $detail->foto_bukti = $request->file('foto_bukti')->store('foto_bukti', 'public');
        }
        $detail->save();
        return response()->json(['status' => true, 'message' => 'Laporan baru berhasil dibuat.']);
    }

    public function show($laporan_id)
    {
        $laporan = LaporanModel::with(['details.fasilitas.ruangan.lantai', 'status'])
            ->where('laporan_id', $laporan_id)
            ->where('user_id', auth()->user()->user_id)
            ->firstOrFail();

        return view('pelapor.show', compact('laporan'));
    }

    public function laporanBersama()
    {
        $breadcrumb = (object) [
            'title' => 'Laporan Kerusakan Fasilitas',
            'list'  => ['Home', 'Laporan Bersama']
        ];

        $page = (object) [
            'title' => 'Daftar Laporan Kerusakan'
        ];

        $active_menu = 'laporan bersama';

        return view('pelapor.laporan_bersama', compact('breadcrumb', 'page', 'active_menu'));
    }

    public function listBersama(Request $request)
    {
        $laporans = LaporanModel::with(['details.fasilitas.ruangan.lantai', 'status'])
        ->where('status_id', 3) // hanya mengambil status yang sedang dalam proses
        ->get();

        return DataTables::of($laporans)
            ->addIndexColumn()
            ->editColumn('status.status_nama', function ($laporan) {
                $status = $laporan->status->status_nama ?? 'Tidak Diketahui';
                switch ($laporan->status_id) {
                    case 1: return '<span class="badge badge-warning">' . $status . '</span>';
                    case 2: return '<span class="badge badge-danger">' . $status . '</span>';
                    case 3: return '<span class="badge badge-info">' . $status . '</span>';
                    case 4: return '<span class="badge badge-success">' . $status . '</span>';
                    default: return '<span class="badge badge-secondary">' . $status . '</span>';
                }
            })
            ->addColumn('aksi', function ($laporan) {
                $detailUrl = route('pelapor.show.bersama', ['laporan_id' => $laporan->laporan_id]);
                $btn = '<button onclick="modalAction(\''.$detailUrl.'\')" class="btn btn-info btn-sm">Detail</button> ';
                return $btn;
            })
            ->rawColumns(['status.status_nama', 'aksi'])
            ->make(true);
    }

    public function showBersama($laporan_id)
    {
        $laporan = LaporanModel::with(['details.fasilitas.ruangan.lantai', 'status'])
            ->where('laporan_id', $laporan_id)
            ->firstOrFail();

        return view('pelapor.show_bersama', compact('laporan'));
    }
}
