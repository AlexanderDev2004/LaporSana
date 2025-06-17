<?php

namespace App\Http\Controllers;

use App\Models\FasilitasModel;
use App\Models\LantaiModel;
use App\Models\LaporanModel;
use App\Models\RekomperbaikanModel;
use App\Models\RiwayatPerbaikan;
use App\Models\RoleModel;
use App\Models\RuanganModel;
use App\Models\TugasDetail;
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
        $satisfactionData = [
            RiwayatPerbaikan::where('rating', 1)->count(),
            RiwayatPerbaikan::where('rating', 2)->count(),
            RiwayatPerbaikan::where('rating', 3)->count(),
            RiwayatPerbaikan::where('rating', 4)->count(),
            RiwayatPerbaikan::where('rating', 5)->count()
        ];

        // Ambil daftar fasilitas (id => nama)
        $fasilitasList = FasilitasModel::pluck('fasilitas_nama', 'fasilitas_id')->toArray();

        return view('sarpras.dashboard', [
            'breadcrumb' => $breadcrumb,
            'active_menu' => $active_menu,
            'card_data' => $card_data,
            'monthly_damage_data' => $monthly_damage_data,
            'spkData' => collect($spk_data), // pastikan ini collection/array
            'fasilitasList' => $fasilitasList,
            'satisfactionData' => $satisfactionData
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
            Log::error('Gagal update profil: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal memperbarui profil'])->withInput();
        }
    }

    public function pemeriksaan()
    {
        $breadcrumb = (object) [
            'title' => 'Pemeriksaan',
            'list'  => ['Home', 'Penugasan', 'Pemeriksaan']
        ];

        $page = (object) [
            'title' => 'Daftar Pemeriksaan Fasilitas'
        ];

        $active_menu = 'pemeriksaan';

        return view('sarpras.pemeriksaan.index', compact('breadcrumb', 'page', 'active_menu'));
    }

    public function pemeriksaanList(Request $request)
    {
        // Query ini sudah benar, dengan asumsi semua relasi di model benar
        $pemeriksaan = TugasModel::with(['details.fasilitas.ruangan.lantai', 'user', 'status'])
            ->where('tugas_jenis', 'Pemeriksaan') // Hanya ambil tugas jenis Pemeriksaan
            ->where('status_id', 3) // Ambil semua tugas yang diproses (Pemeriksaan)
            ->get();

        return DataTables::of($pemeriksaan)
            ->addIndexColumn()
             ->editColumn('status.status_nama', function ($laporan) {
                $status = $laporan->status->status_nama ?? 'Tidak Diketahui';
                switch ($laporan->status_id) {
                    case 1:
                        return '<span class="badge badge-warning">' . $status . '</span>';
                    case 2:
                        return '<span class="badge badge-danger">' . $status . '</span>';
                    case 3:
                        return '<span class="badge badge-info">' . $status . '</span>';
                    case 4:
                        return '<span class="badge badge-success">' . $status . '</span>';
                    default:
                        return '<span class="badge badge-secondary">' . $status . '</span>';
                }
            })
            ->addColumn('aksi', function ($pemeriksaan) {
                $detailUrl = route('sarpras.pemeriksaan.show', ['tugas_id' => $pemeriksaan->tugas_id]);
                $btn = '<button onclick="modalAction(\'' . $detailUrl . '\')" class="btn btn-info btn-sm">Detail</button>';
                $btn .= ' <button class="btn btn-danger btn-sm ml-1 btn-hapus" data-url="' . route('sarpras.penugasan.destroy', $pemeriksaan->tugas_id) . '">Hapus</button>';

                return $btn;
            })
            ->rawColumns(['status.status_nama', 'aksi'])
            ->make(true);
    }

    public function pemeriksaanShow($tugas_id)
    {
        $pemeriksaan = TugasModel::with(['details.fasilitas.ruangan.lantai', 'status'])
            ->where('tugas_id', $tugas_id)
            ->firstOrFail();

        return view('sarpras.pemeriksaan.show', compact('pemeriksaan'));
    }

    public function pemeriksaanCreate()
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
                'r.ruangan_nama',
                'lt.lantai_nama'
            )
            ->get();

        return view('sarpras.pemeriksaan.create', compact('teknisi', 'lantai', 'fasilitasLaporan'));
    }

    public function pemeriksaanStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'      => 'required|exists:m_user,user_id',
            'fasilitas_id' => 'required|exists:m_fasilitas,fasilitas_id',
            'laporan_id'   => 'required|exists:m_laporan,laporan_id',
            'deskripsi'    => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Cek apakah laporan ini sudah pernah ditugaskan
        if (TugasModel::where('laporan_id', $request->laporan_id)->exists()) {
            return response()->json([
                'status'  => false,
                'message' => 'Laporan ini sudah pernah ditugaskan.'
            ], 400);
        }

        try {
            $validated = $validator->validated();

            // Simpan data tugas pemeriksaan
            $tugas = new TugasModel();
            $tugas->user_id       = $validated['user_id'];
            $tugas->status_id     = 3; // Diproses
            $tugas->tugas_jenis   = 'Pemeriksaan';
            $tugas->tugas_mulai   = now();
            $tugas->tugas_selesai = null;
            $tugas->laporan_id    = $validated['laporan_id'];
            $tugas->save();

            // Simpan detail tugas
            $detail = new TugasDetailModel();
            $detail->tugas_id         = $tugas->tugas_id;
            $detail->fasilitas_id     = $validated['fasilitas_id'];
            $detail->deskripsi        = $validated['deskripsi'] ?? '';
            $detail->tingkat_kerusakan = 1;
            $detail->biaya_perbaikan  = 0.00;
            $detail->tugas_image      = '';
            $detail->save();

            return response()->json([
                'status'  => true,
                'message' => 'Tugas pemeriksaan berhasil ditambahkan.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat menyimpan tugas pemeriksaan: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Terjadi error di server.'
            ], 500);
        }
    }

    public function perbaikan()
    {
        $breadcrumb = (object) [
            'title' => 'Perbaikan',
            'list'  => ['Home', 'Penugasan', 'Perbaikan']
        ];

        $page = (object) [
            'title' => 'Daftar Perbaikan Fasilitas'
        ];

        $active_menu = 'perbaikan';

        return view('sarpras.perbaikan.index', compact('breadcrumb', 'page', 'active_menu'));
    }

    public function perbaikanList(Request $request)
    {
        $perbaikan = TugasModel::with(['details.fasilitas.ruangan.lantai', 'user', 'status'])
            ->where('tugas_jenis', 'Perbaikan') // Hanya ambil tugas jenis Perbaikan
            ->where('status_id', 3) // Ambil semua tugas yang diproses (Perbaikan)
            ->get();

        return DataTables::of($perbaikan)
            ->addIndexColumn()
             ->editColumn('status.status_nama', function ($laporan) {
                $status = $laporan->status->status_nama ?? 'Tidak Diketahui';
                switch ($laporan->status_id) {
                    case 1:
                        return '<span class="badge badge-warning">' . $status . '</span>';
                    case 2:
                        return '<span class="badge badge-danger">' . $status . '</span>';
                    case 3:
                        return '<span class="badge badge-info">' . $status . '</span>';
                    case 4:
                        return '<span class="badge badge-success">' . $status . '</span>';
                    default:
                        return '<span class="badge badge-secondary">' . $status . '</span>';
                }
            })
            ->addColumn('aksi', function ($perbaikan) {
                $detailUrl = route('sarpras.perbaikan.show', ['tugas_id' => $perbaikan->tugas_id]);
                $btn = '<button onclick="modalAction(\'' . $detailUrl . '\')" class="btn btn-info btn-sm">Detail</button>';
                $btn .= ' <button class="btn btn-danger btn-sm ml-1 btn-hapus" data-url="' . route('sarpras.penugasan.destroy', $perbaikan->tugas_id) . '">Hapus</button>';

                return $btn;
            })
            ->rawColumns(['status.status_nama', 'aksi'])
            ->make(true);
    }

    public function perbaikanShow($tugas_id)
    {
        $perbaikan = TugasModel::with(['details.fasilitas.ruangan.lantai', 'status'])
            ->where('tugas_id', $tugas_id)
            ->firstOrFail();

        return view('sarpras.perbaikan.show', compact('perbaikan'));
    }

    public function perbaikanCreate()
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
            ->whereIn('f.fasilitas_id', function ($subquery) {
                $subquery->select('fasilitas_id')
                    ->from('t_rekomperbaikan'); // Hanya ambil fasilitas yang ada di SPK
            })
            ->select(
                'l.laporan_id',
                'f.fasilitas_id',
                'f.fasilitas_nama',
                'r.ruangan_nama',
                'lt.lantai_nama'
            )
            ->get();

        return view('sarpras.perbaikan.create', compact('teknisi', 'lantai', 'fasilitasLaporan'));
    }

    public function perbaikanStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'           => 'required|exists:m_user,user_id',
            'fasilitas_id'      => 'required|exists:m_fasilitas,fasilitas_id',
            'laporan_id'        => 'required|exists:m_laporan,laporan_id',
            'tingkat_kerusakan' => 'required|numeric|min:1',
            'biaya_perbaikan'   => 'required|numeric|min:0',
            'deskripsi'         => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        DB::beginTransaction();

        try {
            $validated = $validator->validated();

            $tugas = TugasModel::create([
                'user_id'      => $validated['user_id'],
                'tugas_jenis'  => 'Perbaikan',
                'laporan_id'   => $validated['laporan_id'],
                'status_id'    => 3,
                'tugas_mulai'  => now(),
                'tugas_selesai'=> null,
            ]);

            TugasDetailModel::create([
                'tugas_id'         => $tugas->tugas_id,
                'fasilitas_id'     => $validated['fasilitas_id'],
                'tingkat_kerusakan'=> $validated['tingkat_kerusakan'],
                'biaya_perbaikan'  => $validated['biaya_perbaikan'],
                'deskripsi'        => $validated['deskripsi'] ?? '',
                'tugas_image'      => '',
            ]);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Data perbaikan berhasil disimpan.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error simpan perbaikan: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan saat menyimpan data perbaikan.'
            ], 500);
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
    // public function getFasilitasByJenisTugas($jenis_tugas)
    // {
    //     $status_id = null;

    //     if ($jenis_tugas === 'Pemeriksaan') {
    //         $status_id = 5;
    //     } elseif ($jenis_tugas === 'Perbaikan') {
    //         $status_id = 6;
    //     }

    //     if (!$status_id) {
    //         return response()->json([], 400);
    //     }

    //     $query = DB::table('m_laporan_detail as d')
    //         ->join('m_laporan as l', 'l.laporan_id', '=', 'd.laporan_id')
    //         ->join('m_fasilitas as f', 'f.fasilitas_id', '=', 'd.fasilitas_id')
    //         ->join('m_ruangan as r', 'r.ruangan_id', '=', 'f.ruangan_id')
    //         ->join('m_lantai as lt', 'lt.lantai_id', '=', 'r.lantai_id')
    //         ->leftJoin('m_tugas as t', 't.laporan_id', '=', 'l.laporan_id')
    //         ->where('l.status_id', $status_id)
    //         ->whereNull('t.laporan_id');

    //     // Tambahkan filter khusus untuk PERBAIKAN → hanya ambil fasilitas yang ada di SPK
    //     if ($jenis_tugas === 'Perbaikan') {
    //         // Hanya ambil fasilitas yang ada di tabel t_rekomperbaikan (hasil rekomendasi)
    //         $query->whereIn('f.fasilitas_id', function ($subquery) {
    //         $subquery->select('fasilitas_id')
    //             ->from('t_rekomperbaikan');
    //         });
    //     }


    //     $fasilitas = $query
    //         ->select(
    //             'l.laporan_id',
    //             'f.fasilitas_id',
    //             'f.fasilitas_nama',
    //             'r.ruangan_nama',
    //             'lt.lantai_nama'
    //         )
    //         ->get();

    //     return response()->json($fasilitas);
    // }

    public function getDataPemeriksaan($fasilitas_id)
    {
        $pemeriksaan = DB::table('m_tugas_detail as td')
            ->join('m_tugas as t', 't.tugas_id', '=', 'td.tugas_id')
            ->where('td.fasilitas_id', $fasilitas_id)
            ->where('t.tugas_jenis', 'Pemeriksaan')
            ->where('t.status_id', 4) // pastikan sudah selesai
            ->orderByDesc('t.tugas_mulai')
            ->select('td.tingkat_kerusakan', 'td.biaya_perbaikan')
            ->first();

        return response()->json($pemeriksaan);
    }

    public function tugasDestroy($tugas_id)
    {
        DB::beginTransaction();
        try {
            // Hapus detail terlebih dahulu
            TugasDetailModel::where('tugas_id', $tugas_id)->delete();

            // Hapus tugas utamanya
            TugasModel::findOrFail($tugas_id)->delete();

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Tugas berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus tugas: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan saat menghapus tugas.'], 500);
        }
    }

    public function riwayatPemeriksaan()
    {
        $breadcrumb = (object) [
            'title' => 'Riwayat Pemeriksaan',
            'list'  => ['Home', 'Penugasan', 'Riwayat Pemeriksaan']
        ];

        $page = (object) [
            'title' => 'Daftar Riwayat Pemeriksaan Fasilitas'
        ];

        $active_menu = 'riwayat pemeriksaan';

        return view('sarpras.pemeriksaan.riwayat', compact('breadcrumb', 'page', 'active_menu'));
    }

    public function riwayatPemeriksaanList(Request $request)
    {
        $tugas = TugasModel::with(['details.fasilitas.ruangan.lantai', 'user', 'status'])
            ->where('tugas_jenis', 'Pemeriksaan')
            ->where('status_id', 4) // status selesai
            ->orderByDesc('tugas_mulai')
            ->get();

        return DataTables::of($tugas)
            ->addIndexColumn()
             ->editColumn('status.status_nama', function ($laporan) {
                $status = $laporan->status->status_nama ?? 'Tidak Diketahui';
                switch ($laporan->status_id) {
                    case 1:
                        return '<span class="badge badge-warning">' . $status . '</span>';
                    case 2:
                        return '<span class="badge badge-danger">' . $status . '</span>';
                    case 3:
                        return '<span class="badge badge-info">' . $status . '</span>';
                    case 4:
                        return '<span class="badge badge-success">' . $status . '</span>';
                    default:
                        return '<span class="badge badge-secondary">' . $status . '</span>';
                }
            })
            ->addColumn('aksi', function ($tugas) {
                $detailUrl = route('sarpras.pemeriksaan.show', ['tugas_id' => $tugas->tugas_id]);
                $btn = '<button onclick="modalAction(\'' . $detailUrl . '\')" class="btn btn-info btn-sm">Detail</button> ';

                return $btn;
            })
            ->rawColumns(['status.status_nama', 'aksi'])
            ->make(true);
    }

    public function riwayatPerbaikan()
    {
        $breadcrumb = (object) [
            'title' => 'Riwayat Perbaikan',
            'list'  => ['Home', 'Penugasan', 'Riwayat Perbaikan']
        ];

        $page = (object) [
            'title' => 'Daftar Riwayat Perbaikan Fasilitas'
        ];

        $active_menu = 'riwayat perbaikan';

        return view('sarpras.perbaikan.riwayat', compact('breadcrumb', 'page', 'active_menu'));
    }

    public function riwayatPerbaikanList(Request $request)
    {
        $tugas = TugasModel::with(['details.fasilitas.ruangan.lantai', 'user', 'status'])
            ->where('tugas_jenis', 'Perbaikan')
            ->where('status_id', 4) // status selesai
            ->orderByDesc('tugas_mulai')
            ->get();

        return DataTables::of($tugas)
            ->addIndexColumn()
             ->editColumn('status.status_nama', function ($laporan) {
                $status = $laporan->status->status_nama ?? 'Tidak Diketahui';
                switch ($laporan->status_id) {
                    case 1:
                        return '<span class="badge badge-warning">' . $status . '</span>';
                    case 2:
                        return '<span class="badge badge-danger">' . $status . '</span>';
                    case 3:
                        return '<span class="badge badge-info">' . $status . '</span>';
                    case 4:
                        return '<span class="badge badge-success">' . $status . '</span>';
                    default:
                        return '<span class="badge badge-secondary">' . $status . '</span>';
                }
            })
            ->addColumn('aksi', function ($tugas) {
                $detailUrl = route('sarpras.perbaikan.show', ['tugas_id' => $tugas->tugas_id]);
                $btn = '<button onclick="modalAction(\'' . $detailUrl . '\')" class="btn btn-info btn-sm">Detail</button> ';

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
            ->whereIn('status_id', [3, 4, 5]) // Ambil laporan yang sedang diproses, selesai, atau disetujui
            ->get();

        return DataTables::of($laporans)
            ->addIndexColumn()
            ->editColumn('status.status_nama', function ($laporan) {
                $status = $laporan->status->status_nama ?? 'Tidak Diketahui';
                switch ($laporan->status_id) {
                    case 1:
                        return '<span class="badge badge-warning">' . $status . '</span>';
                    case 2:
                        return '<span class="badge badge-danger">' . $status . '</span>';
                    case 3:
                        return '<span class="badge badge-info">' . $status . '</span>';
                    case 4:
                        return '<span class="badge badge-success">' . $status . '</span>';
                    default:
                        return '<span class="badge badge-secondary">' . $status . '</span>';
                }
            })
            ->addColumn('aksi', function ($laporan) {
                $detailUrl = route('sarpras.laporan.show', ['laporan_id' => $laporan->laporan_id]);
                $btn = '<button onclick="modalAction(\'' . $detailUrl . '\')" class="btn btn-info btn-sm">Detail</button> ';

                $btnSelesai = '';
                $btnTolak = '';

                // Tombol aksi hanya muncul jika statusnya "Menunggu Verifikasi" (ID 1)
                if ($laporan->status_id != 2 && $laporan->status_id != 1) {
                    $btnSelesai = '<button type="button" class="btn btn-success btn-sm ml-1 btn-update-status" data-id="' . $laporan->laporan_id . '" data-status="4">Selesai</button>';
                    $btnTolak = '<button type="button" class="btn btn-danger btn-sm ml-1 btn-update-status" data-id="' . $laporan->laporan_id . '" data-status="2">Tolak</button>';
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
                    case 2:
                        return '<span class="badge badge-danger">' . $status . '</span>';
                    case 4:
                        return '<span class="badge badge-success">' . $status . '</span>';
                    default:
                        return '<span class="badge badge-secondary">' . $status . '</span>';
                }
            })
            ->addColumn('aksi', function ($laporan) {
                $detailUrl = route('sarpras.riwayat.show', ['laporan_id' => $laporan->laporan_id]);
                $btn = '<button onclick="modalAction(\'' . $detailUrl . '\')" class="btn btn-info btn-sm">Detail</button>';
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
            // Eager load fasilitas, ruangan, and lantai relationships
            return RekomperbaikanModel::with(['fasilitas.ruangan.lantai'])
                ->orderBy('rank', 'asc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            Log::error('Error retrieving SPK data: ' . $e->getMessage());
            return [];
        }
    }
};
