<?php

namespace App\Http\Controllers;

use App\Models\DukungLaporanModel;
use App\Models\LaporanModel;
use App\Models\LaporanDetail;
use App\Models\LantaiModel;
use App\Models\RuanganModel;
use App\Models\FasilitasModel;
use App\Models\RiwayatPerbaikan;
use App\Models\TugasDetailModel;
use App\Models\TugasModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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

    public function showProfile()
{
    $user = auth()->user()->load('role');

    $breadcrumb = (object) [
        'title' => 'Profil Saya',
        'list'  => ['Home', 'Profil']
    ];

    $active_menu = 'profile';

    return view('pelapor.users.show', compact('user', 'breadcrumb', 'active_menu'));
}

public function edit()
{
    $user = auth()->user();

    $breadcrumb = (object) [
        'title' => 'Edit Profil Saya',
        'list'  => ['Home', 'Profil', 'Edit']
    ];

    $active_menu = 'profile';

    return view('pelapor.users.edit', compact('user', 'active_menu', 'breadcrumb'));
}

public function update(Request $request)
{
    $user = auth()->user();

    $validated = $request->validate([
        'name' => 'required|string|max:100',
        'NIM' => 'nullable|string|max:20',
        'NIP' => 'nullable|string|max:20',
        'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'password' => 'nullable|string|min:6',
    ]);

    try {
        $data = [
            'name' => $validated['name'],
            'NIM' => $validated['NIM'] ?? null,
            'NIP' => $validated['NIP'] ?? null,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::exists('public/' . $user->avatar)) {
                Storage::delete('public/' . $user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()->route('pelapor.profile.show')->with('success', 'Profil berhasil diperbarui.');
    } catch (\Exception $e) {
        Log::error('Gagal update profil: '.$e->getMessage());
        return back()->withErrors(['error' => 'Gagal memperbarui profil'])->withInput();
    }
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
        $laporans = LaporanModel::with(['details.fasilitas.ruangan.lantai', 'status', 'user'])
            ->where('status_id', 5)
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
                $dukungUrl = route('pelapor.dukungLaporan', ['laporan_id' => $laporan->laporan_id]);

                $btn = '<button onclick="modalAction(\''.$detailUrl.'\')" class="btn btn-info btn-sm">Detail</button> ';
                $btnDukung = '<button class="btn btn-primary btn-sm ml-1 btn-dukung" data-url="'.$dukungUrl.'">Ikut Melapor</button>';
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
            $userId = auth()->user()->user_id;

            $sudahDukung = LaporanDetail::where('laporan_id', $laporan_id)
                ->where('user_id', $userId)
                ->exists();

            if ($sudahDukung) {
                return response()->json([
                    'status' => false,
                    'message' => 'Kamu sudah ikut melapor untuk laporan ini.'
                ]);
            }

            $detail = new LaporanDetail();
            $detail->laporan_id = $laporan_id;
            $detail->deskripsi = '-';
            $detail->save();

            $laporan = LaporanModel::findOrFail($laporan_id);
            $laporan->jumlah_pelapor += 1;
            $laporan->save();

            return response()->json([
                'status' => true,
                'message' => 'Terima kasih telah ikut melaporkan kerusakan ini!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal memberikan dukungan: ' . $e->getMessage()
            ], 500);
        }
    }
}
