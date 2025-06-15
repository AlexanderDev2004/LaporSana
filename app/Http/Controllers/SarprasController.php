<?php

namespace App\Http\Controllers;

use App\Models\FasilitasModel;
use App\Models\LantaiModel;
use App\Models\LaporanModel;
use App\Models\RekomperbaikanModel;
use App\Models\RoleModel;
use App\Models\RuanganModel;
use App\Models\TugasDetailModel;
use App\Models\TugasModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class SarprasController extends Controller
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

        // Ambil daftar fasilitas (id => nama)
        $fasilitasList = FasilitasModel::pluck('fasilitas_nama', 'fasilitas_id')->toArray();

        return view('sarpras.dashboard', [
            'breadcrumb' => $breadcrumb,
            'active_menu' => $active_menu,
            'card_data' => $card_data,
            'monthly_damage_data' => $monthly_damage_data,
            'spkData' => collect($spk_data), // pastikan ini collection/array
            'fasilitasList' => $fasilitasList
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


    public function show()
    {
        // Ambil user yang sedang login dan relasi role-nya
        $user = auth()->user()->load('role');

        // Buat breadcrumb
        $breadcrumb = (object) [
            'title' => 'Profil Saya',
            'list'  => ['Home', 'Profil']
        ];

        // Aktifkan menu sidebar
        $active_menu = 'profile';

        // Tampilkan view
        return view('sarpras.users.show', compact('user', 'breadcrumb', 'active_menu'));
    }

    public function edit()
    {
        $user = auth()->user();

        $breadcrumb = (object) [
            'title' => 'Edit Profil Saya',
            'list'  => ['Home', 'Profil', 'Edit']
        ];

        $active_menu = 'profile';

        return view('sarpras.users.edit', compact('user', 'active_menu', 'breadcrumb'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            // Username tidak diubah sendiri, jadi skip validasi unique username
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

            return redirect()->route('sarpras.profile.show')->with('success', 'Profil berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal update profil: '.$e->getMessage());
            return back()->withErrors(['error' => 'Gagal memperbarui profil'])->withInput();
        }

    }

    public function penugasan()
    {
        $breadcrumb = (object) [
            'title' => 'Penugasan',
            'list'  => ['Home', 'Penugasan']
        ];
        
        $page = (object) [
            'title' => 'Daftar Penugasan Teknisi'
        ];

        $active_menu = 'penugasan';

        return view('sarpras.penugasan.penugasan', compact('breadcrumb', 'page', 'active_menu'));
    }

    public function tugasList(Request $request)
    {
        // Query ini sudah benar, dengan asumsi semua relasi di model benar
        $tugas = TugasModel::with(['details.fasilitas.ruangan.lantai', 'user', 'status'])
            ->where('status_id', '!=', 4) // Ambil semua tugas yang belum selesai
            ->get();

        return DataTables::of($tugas)
            ->addIndexColumn()
            ->editColumn('status.status_nama', function ($tugas) {
                $status = $tugas->status->status_nama ?? 'Tidak Diketahui';
                switch ($tugas->status_id) {
                    case 3: return '<span class="badge badge-info">' . $status . '</span>';
                    case 4: return '<span class="badge badge-success">' . $status . '</span>';
                    case 5: return '<span class="badge badge-primary">' . $status . '</span>';
                    default: return '<span class="badge badge-secondary">' . $status . '</span>';
                }
            })
            ->addColumn('aksi', function ($tugas) {
                $detailUrl = route('sarpras.penugasan.show', ['tugas_id' => $tugas->tugas_id]);
                // $editUrl = route('sarpras.penugasan.edit', ['tugas_id' => $tugas->tugas_id]);
                $deleteUrl = route('sarpras.penugasan.destroy', ['tugas_id' => $tugas->tugas_id]);

                $btn = '<button onclick="modalAction(\''.$detailUrl.'\')" class="btn btn-info btn-sm">Detail</button> ';
                // $btnEdit = '<button onclick="modalAction(\''.$editUrl.'\')" class="btn btn-info btn-sm btn-warning">Edit</button> ';
                $btnDelete = '<button type="button" class="btn btn-danger btn-sm ml-1 btn-hapus" data-url="'.$deleteUrl.'">Hapus</button>';

                return $btn . $btnDelete;
            })
            ->rawColumns(['status.status_nama', 'aksi'])
            ->make(true);
    }

    public function tugasShow($tugas_id)
    {
        $tugas = TugasModel::with(['details.fasilitas.ruangan.lantai', 'status'])
            ->where('tugas_id', $tugas_id)
            ->firstOrFail();

        return view('sarpras.penugasan.show', compact('tugas'));
    }

    public function tugasCreate()
    {
        $teknisi = UserModel::where('roles_id', 6)->get();
        $lantai = LantaiModel::all();

        // Ambil fasilitas yang sudah dilaporkan dan belum pernah ditugaskan
        $fasilitasLaporan = DB::table('m_laporan_detail as d')
            ->join('m_laporan as l', 'l.laporan_id', '=', 'd.laporan_id')
            ->join('m_fasilitas as f', 'f.fasilitas_id', '=', 'd.fasilitas_id')
            ->join('m_ruangan as r', 'r.ruangan_id', '=', 'f.ruangan_id')
            ->join('m_lantai as lt', 'lt.lantai_id', '=', 'r.lantai_id')
            ->leftJoin('m_tugas as t', 't.laporan_id', '=', 'l.laporan_id')
            ->whereNull('t.laporan_id') // Belum pernah ditugaskan
            ->select(
                'l.laporan_id',
                'f.fasilitas_id',
                'f.fasilitas_nama',
                'r.ruangan_id',
                'r.ruangan_nama',
                'lt.lantai_id',
                'lt.lantai_nama'
            )
            ->get();

        return view('sarpras.penugasan.create', compact('teknisi', 'lantai', 'fasilitasLaporan'));
    }

    public function tugasStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:m_user,user_id',
            'tugas_jenis' => 'required|in:Pemeriksaan,Perbaikan',
            'fasilitas_id' => 'required|exists:m_fasilitas,fasilitas_id',
            'laporan_id' => 'required|exists:m_laporan,laporan_id',
            'deskripsi' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        // Cegah duplikasi penugasan laporan
        if (TugasModel::where('laporan_id', $request->laporan_id)->exists()) {
            return response()->json(['status' => false, 'message' => 'Laporan ini sudah ditugaskan.'], 400);
        }

        try {
            $validated = $validator->validated();

            $tugas = new TugasModel();
            $tugas->user_id = $validated['user_id'];
            $tugas->status_id = 3;
            $tugas->tugas_jenis = $validated['tugas_jenis'];
            $tugas->tugas_mulai = now();
            $tugas->tugas_selesai = null;
            $tugas->laporan_id = $validated['laporan_id'];
            $tugas->save();

            $detail = new TugasDetailModel();
            $detail->tugas_id = $tugas->tugas_id;
            $detail->fasilitas_id = $validated['fasilitas_id'];
            $detail->deskripsi = $validated['deskripsi'] ?? '';
            $detail->tingkat_kerusakan = 1;
            $detail->biaya_perbaikan = 0.00;
            $detail->tugas_image = '';
            $detail->save();

            return response()->json(['status' => true, 'message' => 'Tugas berhasil ditambahkan.']);
        } catch (\Exception $e) {
            Log::error('Error saat menyimpan tugas: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Terjadi error di server.'], 500);
        }
    }

    // Helper untuk chained dropdown
    public function getRuangan($lantai_id)
    {
        $ruangan = RuanganModel::where('lantai_id', $lantai_id)->get();
        return response()->json($ruangan);
    }

    // Helper untuk chained dropdown
    public function getFasilitas($ruangan_id)
    {
        $fasilitas = FasilitasModel::where('ruangan_id', $ruangan_id)->get();
        return response()->json($fasilitas);
    }

    // Helper untuk mendapatkan fasilitas berdasarkan jenis tugas
    public function getFasilitasByJenisTugas($jenis_tugas)
    {
        $status_id = null;

        if ($jenis_tugas === 'Pemeriksaan') {
            $status_id = 5;
        } elseif ($jenis_tugas === 'Perbaikan') {
            $status_id = 3;
        }

        if (!$status_id) {
            return response()->json([], 400);
        }

        $query = DB::table('m_laporan_detail as d')
            ->join('m_laporan as l', 'l.laporan_id', '=', 'd.laporan_id')
            ->join('m_fasilitas as f', 'f.fasilitas_id', '=', 'd.fasilitas_id')
            ->join('m_ruangan as r', 'r.ruangan_id', '=', 'f.ruangan_id')
            ->join('m_lantai as lt', 'lt.lantai_id', '=', 'r.lantai_id')
            ->leftJoin('m_tugas as t', 't.laporan_id', '=', 'l.laporan_id')
            ->where('l.status_id', $status_id)
            ->whereNull('t.laporan_id');

        // Tambahkan filter khusus untuk PERBAIKAN → hanya ambil fasilitas yang ada di SPK
        if ($jenis_tugas === 'Perbaikan') {
            $query->whereExists(function ($subquery) {
                $subquery->select(DB::raw(1))
                    ->from('t_rekomperbaikan')
                    ->whereColumn('t_rekomperbaikan.fasilitas_id', 'f.fasilitas_id');
            });
        }

        $fasilitas = $query
            ->select(
                'l.laporan_id',
                'f.fasilitas_id',
                'f.fasilitas_nama',
                'r.ruangan_nama',
                'lt.lantai_nama'
            )
            ->get();

        return response()->json($fasilitas);
    }

    public function tugasEdit($tugas_id)
    {
        $tugas = TugasModel::with('details.fasilitas')->findOrFail($tugas_id);
        $teknisi = UserModel::where('roles_id', 6)->get();

        return view('sarpras.penugasan.edit', compact('tugas', 'teknisi'));
    }

    // public function tugasUpdate(Request $request, $tugas_id)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'user_id' => 'required|exists:m_user,user_id',
    //         'tugas_jenis' => 'required|in:Pemeriksaan,Perbaikan',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['status' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
    //     }

    //     try {
    //         $tugas = TugasModel::findOrFail($tugas_id);
    //         $tugas->user_id = $request->user_id;
    //         $tugas->tugas_jenis = $request->tugas_jenis;
    //         $tugas->save();

    //         return response()->json(['status' => true, 'message' => 'Data penugasan berhasil diperbarui.']);
        
    //     } catch (\Exception $e) {
    //         Log::error('Error saat update tugas: ' . $e->getMessage());
    //         return response()->json(['status' => false, 'message' => 'Terjadi error di server.'], 500);
    //     }
    // }

    public function tugasDestroy($tugas_id)
    {
        DB::beginTransaction();
        try {
            TugasDetailModel::where('tugas_id', $tugas_id)->delete();
            TugasModel::findOrFail($tugas_id)->delete();

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Data penugasan berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menghapus tugas: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Terjadi error di server saat menghapus data.'], 500);
        }
    }

    // view riwayat penugasan
    public function riwayatPenugasan()
    {
        $breadcrumb = (object)[
            'title' => 'Riwayat Penugasan',
            'list'  => ['Home', 'Penugasan', 'Riwayat']
        ];

        $page = (object)[
            'title' => 'Daftar Riwayat Penugasan Teknisi'
        ];

        $active_menu = 'riwayat penugasan';

        return view('sarpras.penugasan.riwayat', compact('breadcrumb', 'page', 'active_menu'));
    }

    public function riwayatPenugasanList(Request $request)
    {
        $tugas = TugasModel::with(['details.fasilitas.ruangan.lantai', 'user', 'status'])
            ->where('status_id', 4) // status selesai
            ->orderByDesc('tugas_mulai')
            ->get();

        return DataTables::of($tugas)
            ->addIndexColumn()
            ->editColumn('status.status_nama', function ($tugas) {
                return '<span class="badge badge-success">' . ($tugas->status->status_nama ?? 'Selesai') . '</span>';
            })
            ->addColumn('aksi', function ($tugas) {
                $detailUrl = route('sarpras.penugasan.show', ['tugas_id' => $tugas->tugas_id]);
                $btn = '<button onclick="modalAction(\''.$detailUrl.'\')" class="btn btn-info btn-sm">Detail</button> ';

                return $btn;
            })
            ->rawColumns(['status.status_nama', 'aksi'])
            ->make(true);
    }

    // masuk bagian laporan kerusakan
    public function laporan()
    {
        $breadcrumb = (object) [
            'title' => 'Laporan',
            'list'  => ['Home', 'Laporan']
        ];

        $page = (object) [
            'title' => 'Daftar Laporan Kerusakan'
        ];

        $active_menu = 'laporan';

        return view('sarpras.laporan.laporan', compact('breadcrumb', 'page', 'active_menu'));
    }

    public function list(Request $request)
    {
        $laporans = LaporanModel::with(['details.fasilitas.ruangan.lantai', 'status', 'user'])
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
                $detailUrl = route('sarpras.laporan.show', ['laporan_id' => $laporan->laporan_id]);
                $btn = '<button onclick="modalAction(\''.$detailUrl.'\')" class="btn btn-info btn-sm">Detail</button> ';

                $btnSelesai = '';
                $btnTolak = '';

                // Tombol aksi hanya muncul jika statusnya "Menunggu Verifikasi" (ID 1)
                if ($laporan->status_id == 3) {
                    $btnSelesai = '<button type="button" class="btn btn-success btn-sm ml-1 btn-update-status" data-id="'.$laporan->laporan_id.'" data-status="4">Selesai</button>';
                    $btnTolak = '<button type="button" class="btn btn-danger btn-sm ml-1 btn-update-status" data-id="'.$laporan->laporan_id.'" data-status="2">Tolak</button>';
                }
                return $btn . $btnSelesai . $btnTolak;
            })
            ->rawColumns(['status.status_nama', 'aksi'])
            ->make(true);
    }

    public function showLaporan($laporan_id)
    {
        $laporan = LaporanModel::with(['details.fasilitas.ruangan.lantai', 'status'])
            ->where('laporan_id', $laporan_id)
            ->firstOrFail();

        return view('sarpras.laporan.show', compact('laporan'));
    }

    public function updateStatusLaporan(Request $request, $laporan_id)
    {
        $validator = Validator::make($request->all(), [
            'status_id' => 'required|in:2,4', // Hanya izinkan status Ditolak (2) atau Selesai (4)
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Status yang dipilih tidak valid.'], 422);
        }

        try {
            $laporan = LaporanModel::findOrFail($laporan_id);
            $laporan->status_id = $request->status_id;
            $laporan->save();
            
            return response()->json(['status' => true, 'message' => 'Status laporan berhasil diperbarui.']);

        } catch (\Exception $e) {
            Log::error('Gagal update status laporan: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Terjadi error di server.'], 500);
        }
    }

    public function riwayatLaporan()
    {
        $breadcrumb = (object) [
            'title' => 'Riwayat Laporan',
            'list'  => ['Home', 'Laporan', 'Riwayat']
        ];
        $page = (object) [
            'title' => 'Daftar Riwayat Laporan Kerusakan'
        ];
        $active_menu = 'riwayat laporan';

        return view('sarpras.riwayat.riwayat', compact('breadcrumb', 'page', 'active_menu'));
    }

    /**
     * Menyediakan data untuk tabel Riwayat Laporan melalui AJAX.
     */
    public function riwayatList(Request $request)
    {
        // Filter hanya untuk status Selesai (4) dan Ditolak (2)
        $laporans = LaporanModel::with(['details.fasilitas.ruangan.lantai', 'user', 'status'])
            ->whereIn('status_id', [2, 4]);

        return DataTables::of($laporans)
            ->addIndexColumn()
            ->editColumn('status.status_nama', function ($laporan) {
                $status = $laporan->status->status_nama ?? 'Tidak Diketahui';
                switch ($laporan->status_id) {
                    case 2: return '<span class="badge badge-danger">' . $status . '</span>';
                    case 4: return '<span class="badge badge-success">' . $status . '</span>';
                    default: return '<span class="badge badge-secondary">' . $status . '</span>';
                }
            })
            ->addColumn('aksi', function ($laporan) {
                $detailUrl = route('sarpras.riwayat.show', ['laporan_id' => $laporan->laporan_id]);
                $btn = '<button onclick="modalAction(\''.$detailUrl.'\')" class="btn btn-info btn-sm">Detail</button>';
                return $btn;
            })
            ->rawColumns(['status.status_nama', 'aksi'])
            ->make(true);
    }

    public function showRiwayatLaporan($laporan_id)
    {
        $laporan = LaporanModel::with(['details.fasilitas.ruangan.lantai', 'status'])
            ->where('laporan_id', $laporan_id)
            ->firstOrFail();

        return view('sarpras.riwayat.show', compact('laporan'));
    }

    private function getSPKData()
    {
        try {
            // Directly query the database instead of making HTTP requests
            return RekomperbaikanModel::with('fasilitas')
                ->orderBy('rank', 'asc')
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            Log::error('Error retrieving SPK data: ' . $e->getMessage());
            return [];
        }
    }
};
