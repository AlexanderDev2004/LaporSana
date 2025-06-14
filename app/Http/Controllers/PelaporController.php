<?php

namespace App\Http\Controllers;

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
use Illuminate\Support\Facades\Hash;
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
        $validator = Validator::make($request->all(), [
            'lantai_id' => 'required|exists:m_lantai,lantai_id',
            'ruangan_id' => 'required|exists:m_ruangan,ruangan_id',
            'fasilitas_id' => 'required|exists:m_fasilitas,fasilitas_id',
            'foto_bukti' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240', // Batas sudah 10MB
            'deskripsi' => 'required|string',
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
                $btnDukung = '<button class="btn btn-primary btn-sm ml-1 btn-dukung" data-id="'.$laporan->laporan_id.'">Ikut Melapor</button>';
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
            $laporan = LaporanModel::findOrFail($laporan_id);
            $laporan->jumlah_pelapor += 1;
            $laporan->save();

            return response()->json(['status' => true, 'message' => 'Terima kasih telah ikut melaporkan kerusakan ini!']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Gagal memberikan dukungan: ' . $e->getMessage()], 500);
        }
    }

    public function feedback()
    {
        $breadcrumb = (object) [
            'title' => 'Feedback',
            'list'  => ['Home', 'Feedback']
        ];

        $page = (object) [
            'title' => 'Daftar Feedback Laporan'
        ];

        $active_menu = 'feedback';

        return view('pelapor.feedback', compact('breadcrumb', 'page', 'active_menu'));
    }

    public function feedbackList(Request $request)
{
    $tugas = TugasModel::with(['details.fasilitas', 'user', 'status', 'riwayat'])
        ->where('tugas_jenis', 'perbaikan')
        ->where('status_id', 4)
        ->get();

    return DataTables::of($tugas)
        ->addIndexColumn()
        ->editColumn('status.status_nama', function ($tugas) {
            $status = $tugas->status->status_nama ?? 'Tidak Diketahui';
            switch ($tugas->status_id) {
                case 4: return '<span class="badge badge-success">' . $status . '</span>';
                default: return '<span class="badge badge-primary">' . $status . '</span>';
            }
        })
        ->addColumn('rating', function ($tugas) {
            $rating = $tugas->riwayat->rating ?? null;
            if (!$rating) return 'Belum ada ulasan';

            $stars = '';
            for ($i = 1; $i <= 5; $i++) {
                $stars .= $i <= $rating
                    ? '<i class="fas fa-star text-warning"></i>'
                    : '<i class="far fa-star text-warning"></i>';
            }
            return $stars . "<span class='ml-1'>($rating)</span>";
        })
        ->addColumn('aksi', function ($tugas) {
            $feedbackUrl = route('pelapor.feedback.create', ['tugas_id' => $tugas->tugas_id]);
            $detailUrl = route('pelapor.feedback.show', ['tugas_id' => $tugas->tugas_id]);

            $btnFeedback = '<button onclick="modalAction(\''.$feedbackUrl.'\')" class="btn btn-success btn-sm">Beri Ulasan</button> ';
            $btn = '<button onclick="modalAction(\''.$detailUrl.'\')" class="btn btn-info btn-sm">Detail</button> ';

            if (empty($tugas->riwayat->rating)) {
                $btnFeedback = '<button onclick="modalAction(\''.$feedbackUrl.'\')" class="btn btn-success btn-sm">Beri Ulasan</button> ';
            } else {
                $btnFeedback = '<button class="btn btn-secondary btn-sm" disabled>Sudah Diulas</button> ';
            }

            return $btnFeedback . $btn;
        })
        ->rawColumns(['status.status_nama', 'rating', 'aksi'])
        ->make(true);
}



    public function feedbackShow($tugas_id)
    {
        $tugas = TugasModel::with(['details.fasilitas', 'status'])
            ->where('tugas_id', $tugas_id)
            ->firstOrFail();

        return view('pelapor.feedback_show', compact('tugas'));
    }

    public function feedbackData($tugas_id)
{
    $riwayat = RiwayatPerbaikan::where('tugas_id', $tugas_id)->first();

    if (!$riwayat) {
        return response()->json(['success' => false, 'message' => 'Tidak ada feedback.']);
    }

    return response()->json([
        'success' => true,
        'data' => [
            'rating' => $riwayat->rating,
            'ulasan' => $riwayat->ulasan,
            'created_at' => $riwayat->created_at->format('d-m-Y H:i')
        ]
    ]);
}

    public function feedbackCreate($tugas_id)
{
    $tugas = TugasModel::with(['details.fasilitas'])->findOrFail($tugas_id);
    return view('pelapor.feedback_form', compact('tugas'));
}

public function feedbackStore(Request $request)
{
    $validated = $request->validate([
        'tugas_id' => 'required|exists:m_tugas,tugas_id',
        'rating' => 'required|integer|min:1|max:5',
        'ulasan' => 'nullable|string|max:255',
    ]);

    try {
        RiwayatPerbaikan::updateOrCreate(
            ['tugas_id' => $validated['tugas_id']],
            [
                'rating' => $validated['rating'],
                'ulasan' => $validated['ulasan'],
            ]
        );

        return redirect()
            ->route('pelapor.feedback')
            ->with('success', 'Feedback berhasil disimpan.');
    } catch (\Exception $e) {
        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Gagal menyimpan feedback. ' . $e->getMessage());
    }
}

}
