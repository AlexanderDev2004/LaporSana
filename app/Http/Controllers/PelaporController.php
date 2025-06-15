<?php

namespace App\Http\Controllers;

use App\Models\DukungLaporanModel;
use App\Models\LaporanModel;
use App\Models\LaporanDetail;
use App\Models\LantaiModel;
use App\Models\RuanganModel;
use App\Models\FasilitasModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            'title' => 'Laporan Saya',
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
        $laporans = LaporanModel::with(['details.fasilitas.ruangan.lantai'])
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

        return view('pelapor.create', compact('lantai'));
    }

    // Mengambil daftar lantai
    public function getRuangan($lantai_id)
    {
        $ruangan = RuanganModel::where('lantai_id', $lantai_id)->get();
        return response()->json($ruangan);
    }

    // Mengambil daftar fasilitas berdasarkan ruangan_id
    public function getFasilitas($ruangan_id)
    {
        $fasilitas = FasilitasModel::where('ruangan_id', $ruangan_id)->get();
        return response()->json($fasilitas);
    }


    public function store(Request $request)
    {
        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'lantai_id' => 'required|exists:m_lantai,lantai_id',
            'ruangan_id' => 'required|exists:m_ruangan,ruangan_id',
            'fasilitas_id' => 'required|exists:m_fasilitas,fasilitas_id',
            'foto_bukti' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'deskripsi' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Validasi Gagal', 'msgField' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        $userId = Auth::id();

        // 2. Cari laporan AKTIF yang sudah ada untuk fasilitas yang sama
        // Laporan aktif adalah yang statusnya belum 'Selesai' atau 'Ditolak'
        $existingLaporan = LaporanModel::with('dukungan')
            ->whereHas('details', function ($query) use ($validated) {
                $query->where('fasilitas_id', $validated['fasilitas_id']);
            })
            ->whereIn('status_id', [3, 5]) // 3=Diproses, 5=Disetujui
            ->first();

        // 3. Jika ADA laporan yang sudah ada
        if ($existingLaporan) {
            // Cek apakah user saat ini sudah terlibat di laporan tersebut
            $isPelaporUtama = $existingLaporan->user_id == $userId;
            $sudahMendukung = $existingLaporan->dukungan->contains('user_id', $userId);

            if ($isPelaporUtama || $sudahMendukung) {
                // Jika sudah terlibat, kembalikan pesan informasi
                return response()->json([
                    'status' => false,
                    'message' => 'Anda sudah melaporkan kerusakan pada fasilitas ini sebelumnya.'
                ], 409); // 409 Conflict adalah status yang cocok
            }

            // Jika belum terlibat, tambahkan sebagai pendukung
            try {
                DB::transaction(function () use ($existingLaporan, $userId) {
                    // Catat dukungan di tabel pivot
                    DukungLaporanModel::create([
                        'laporan_id' => $existingLaporan->laporan_id,
                        'user_id' => $userId,
                    ]);

                    // Tambah jumlah pelapor
                    $existingLaporan->increment('jumlah_pelapor');
                });

                return response()->json([
                    'status' => true,
                    'message' => 'Laporan serupa sudah ada. Anda berhasil ditambahkan sebagai pendukung laporan.'
                ]);
            } catch (\Exception $e) {
                Log::error('Gagal menambahkan dukungan: ' . $e->getMessage());
                return response()->json(['status' => false, 'message' => 'Terjadi kesalahan saat menambahkan dukungan.'], 500);
            }
        }

        // 4. Jika TIDAK ADA laporan, buat laporan baru
        try {
            DB::transaction(function () use ($request, $validated, $userId) {
                // Buat data laporan utama
                $laporan = LaporanModel::create([
                    'user_id' => $userId,
                    'status_id' => 1, // Status awal: Menunggu Konfirmasi
                    'tanggal_lapor' => now(),
                    'jumlah_pelapor' => 1,
                ]);

                // Simpan foto jika ada
                $fotoPath = null;
                if ($request->hasFile('foto_bukti')) {
                    $fotoPath = $request->file('foto_bukti')->store('foto_bukti', 'public');
                }

                // Buat data detail laporan
                $laporan->details()->create([
                    'fasilitas_id' => $validated['fasilitas_id'],
                    'deskripsi' => $validated['deskripsi'],
                    'foto_bukti' => $fotoPath,
                ]);
            });

            return response()->json(['status' => true, 'message' => 'Laporan baru berhasil dibuat.']);

        } catch (\Exception $e) {
            Log::error('Gagal membuat laporan baru: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan saat membuat laporan baru.'], 500);
        }
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
            'title' => 'Laporan Bersama',
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
        $laporans = LaporanModel::with(['details.fasilitas.ruangan.lantai', 'status', 'user', 'dukungan'])
            ->whereIn('status_id', [3, 5]); // Status yang relevan untuk didukung

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
                $dukungUrl = route('pelapor.dukungLaporan', ['laporan_id' => $laporan->laporan_id]);
                $btn = '<button onclick="modalAction(\''.$detailUrl.'\')" class="btn btn-info btn-sm">Detail</button> ';
                $btnDukung = '';

                $userId = Auth::id();
                // Cek apakah user adalah pelapor asli ATAU sudah pernah mendukung
                $sudahTerlibat = $laporan->user_id == $userId || $laporan->dukungan->contains('user_id', $userId);

                if (!$sudahTerlibat) {
                    // Hanya tampilkan tombol jika user belum terlibat
                    $btnDukung = '<button class="btn btn-primary btn-sm ml-1 btn-dukung" data-url="'.$dukungUrl.'">Ikut Melapor</button>';
                }

                return $btn . $btnDukung;
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

    public function dukungLaporan($laporan_id)
    {
        try {
            $userId = Auth::id();
            $laporan = LaporanModel::findOrFail($laporan_id);

            // Cek 1: Apakah user adalah pelapor asli?
            if ($laporan->user_id == $userId) {
                return response()->json(['status' => false, 'message' => 'Anda adalah pelapor utama laporan ini.'], 400);
            }

            // Cek 2: Apakah user sudah pernah mendukung sebelumnya?
            $sudahDukung = DukungLaporanModel::where('laporan_id', $laporan_id)
                                        ->where('user_id', $userId)
                                        ->exists();

            if ($sudahDukung) {
                return response()->json(['status' => false, 'message' => 'Anda sudah ikut melapor untuk laporan ini.'], 400);
            }

            // Jika semua pengecekan lolos, simpan dukungan
            DukungLaporanModel::create([
                'laporan_id' => $laporan_id,
                'user_id' => $userId
            ]);

            // Tambah jumlah pelapor
            $laporan->increment('jumlah_pelapor');

            return response()->json(['status' => true, 'message' => 'Terima kasih telah ikut melaporkan kerusakan ini!']);

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }
}
