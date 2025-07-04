<?php

namespace App\Http\Controllers;

use App\Models\LaporanModel;
use App\Models\StatusModel;
use App\Models\TugasDetail;
use App\Models\TugasModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
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

        $userId = Auth::user()->user_id;

        // Statistik bulanan tetap
        $dataStatistik = [];
        for ($i = 1; $i <= 12; $i++) {
            $dataStatistik[] = TugasModel::where('user_id', $userId)
                ->whereMonth('tugas_mulai', $i)
                ->whereYear('tugas_mulai', date('Y'))
                ->count();
        }

        // Tugas terbaru yang BELUM selesai
        $tugasTerbaru = TugasModel::with('details.fasilitas', 'status')
            ->where('user_id', $userId)
            ->whereHas('status', function ($q) {
                $q->where('status_nama', '!=', 'selesai');
            })
            ->orderByDesc('tugas_mulai')
            ->limit(2)
            ->get();

        return view('teknisi.dashboard', compact('breadcrumb', 'active_menu', 'tugasTerbaru', 'dataStatistik'));
    }




    public function showLaporan($id)
    {
        $laporan = LaporanModel::with([
            'user',
            'status',
            'details.fasilitas.ruangan.lantai' // details sudah include fasilitas, ruangan, lantai
        ])->findOrFail($id);

        $breadcrumb = (object) [
            'title' => 'Detail Laporan',
            'list'  => ['Home', 'Tugas', 'Detail Laporan']
        ];
        $active_menu = 'tugas';

        return view('teknisi.show_laporan', compact('breadcrumb', 'active_menu', 'laporan'));
    }

    public function riwayat()
    {
        $breadcrumb = (object) [
            'title' => 'Riwayat Tugas',
            'list'  => ['Home', 'Riwayat Tugas']
        ];
        $active_menu = 'riwayat';

        // Kita bisa kirim data status untuk filter
        $status = StatusModel::all();

        return view('teknisi.riwayat', compact('breadcrumb', 'active_menu', 'status'));
    }

    public function riwayatList(Request $request)
    {
        $tugas = TugasModel::with(['status', 'user'])
            ->where('user_id', Auth::user()->user_id)
            ->orderBy('tugas_selesai', 'asc') // filter user
            ->whereHas('status', function ($query) {
                $query->where('status_nama', 'selesai');
            });


        return DataTables::of($tugas->with('riwayat')->get())
            ->addIndexColumn()
            ->addColumn('feedback', function ($tugas) {
                if ($tugas->riwayat) {
                    $rating = str_repeat('⭐', $tugas->riwayat->rating);
                    return "<div>{$rating}<br><small>{$tugas->riwayat->ulasan}</small></div>";
                } else {
                    return '<span class="text-muted">Belum ada</span>';
                }
            })
            ->addColumn('aksi', function ($tugas) {
                $btn = '<button onclick="modalAction(\'' . route('teknisi.show', $tugas->tugas_id) . '\')" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></button>';
                return $btn;
            })
            ->rawColumns(['feedback', 'aksi'])
            ->toJson();
    }


    // HALAMAN TUGAS PERBAIKAN

    public function perbaikan(Request $request)
    {
        $active_menu = 'perbaikan';
        $breadcrumb = (object) [
            'title' => 'Daftar Tugas Perbaikan',
            'list' => ['Home', 'Perbaikan']
        ];

        $tugas = TugasModel::with(['status', 'user', 'laporan'])
            ->where('user_id', Auth::user()->user_id)
            ->whereHas('status', function ($query) {
                $query->where('status_nama', '!=', 'selesai');
            });

        if ($request->filled('status')) {
            $tugas->where('status_id', $request->status);
        }

        $status = StatusModel::all();
        $user = UserModel::all();
        $laporan = LaporanModel::all();

        return view('teknisi.perbaikan', compact('user', 'status', 'tugas', 'active_menu', 'breadcrumb', 'laporan'));
    }


    public function listPerbaikan(Request $request)
    {
        $tugas = TugasModel::with(['status', 'user', 'laporan'])
            ->where('user_id', Auth::user()->user_id)
            ->where('tugas_jenis', 'perbaikan')
            ->whereHas('status', function ($query) {
                $query->where('status_nama', '!=', 'selesai');
            });

        if ($request->has('filter_tugas_jenis') && $request->filter_tugas_jenis != '') {
            $tugas->where('tugas_jenis', $request->filter_tugas_jenis);
        }

        return DataTables::of($tugas->get())
            ->addIndexColumn()
            ->addColumn('laporan', function ($tugas) {
                if ($tugas->laporan) {
                    return '<a href="#" onclick="modalAction(\'' . route('teknisi.show_laporan', $tugas->laporan->laporan_id) . '\')" class="btn btn-link text-info"><i class="fas fa-eye"></i> <span class="ms-1">Laporan</span></a>';
                } else {
                    return '<span class="text-muted">Belum Ada</span>';
                }
            })
            ->addColumn('aksi', function ($tugas) {
                $btn = '<button onclick="modalAction(\'' . route('teknisi.showperbaikan', $tugas->tugas_id) . '\')" class="btn btn-info btn-sm mx-1"><i class="fas fa-eye"></i></button>';
                $btn .= '<button onclick="modalAction(\'' . route('teknisi.editperbaikan', $tugas->tugas_id) . '\')"
            class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></button>';
                return $btn;
            })
            ->rawColumns(['laporan', 'aksi'])
            ->toJson();
    }

    public function showPerbaikan($id)
    {
        $tugas = TugasDetail::with(['tugas', 'fasilitas'])
            ->whereHas('tugas', function ($q) {
                $q->where('user_id', Auth::user()->user_id);
            })
            ->where('detail_id', $id)
            ->findOrFail($id);

        $breadcrumb = (object) [
            'title' => 'Detail Perbaikan',
            'list'  => ['Home', 'Perbaikan', 'Detail']
        ];
        $active_menu = 'perbaikan';

        return view('teknisi.showperbaikan', compact('breadcrumb', 'active_menu', 'tugas'));
    }


    public function editPerbaikan($id)
    {
        $tugas = TugasModel::with(['status', 'user'])
            ->where('user_id', Auth::user()->user_id)
            ->where('tugas_jenis', 'perbaikan')
            ->findOrFail($id);

        $breadcrumb = (object) [
            'title' => 'Edit perbaikan',
            'list'  => ['Home', 'Tugas', 'Edit Perbaikan']
        ];
        $active_menu = 'tugas';

        return view('teknisi.editperbaikan', compact('breadcrumb', 'active_menu', 'tugas'));
    }

    public function updatePerbaikan(Request $request, $id)
    { {
            // Cari status_id berdasarkan status_nama
            $status = StatusModel::where('status_nama', $request->status_nama)->first();

            if (!$status) {
                return response()->json([
                    'status' => false,
                    'message' => 'Status tidak ditemukan.',
                    'msgField' => ['status_nama' => ['Status tidak valid.']]
                ]);
            }

            // Validasi input
            $validator = Validator::make($request->all(), [
                'tugas_selesai' => ['nullable', 'date', function ($attribute, $value, $fail) {
                    if ($value != date('Y-m-d')) {
                        $fail('Tanggal tidak sesuai! (' . date('Y-m-d') . ').');
                    }
                }],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }

            // Proses update
            $tugas = TugasModel::findOrFail($id);


            $formattedTugasSelesai = $request->tugas_selesai . ' ' . date('H:i:s');

            $tugas->update([
                'status_id'     => $status->status_id,
                'tugas_selesai' => $formattedTugasSelesai,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data tugas berhasil diperbarui.'
            ]);
        }
    }

   // HALAMAN TUGAS PEMERIKSAAN

    public function pemeriksaan(Request $request)
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Pemeriksaan',
            'list'  => ['Home', 'Pemeriksaan']
        ];
        $active_menu = 'pemeriksaan';
        $tugas = TugasModel::with(['status', 'user', 'laporan'])
            ->where('user_id', Auth::user()->user_id)
            ->whereHas('status', function ($query) {
                $query->where('status_nama', '!=', 'selesai');
            });

        if ($request->filled('status')) {
            $tugas->where('status_id', $request->status);
        }

        $status = StatusModel::all();
        $user = UserModel::all();
        $laporan = LaporanModel::all();

        return view('teknisi.pemeriksaan', compact('user', 'status', 'tugas', 'active_menu', 'breadcrumb', 'laporan'));
    }

    public function listPemeriksaan(Request $request)
    {
        $tugas = TugasModel::with(['status', 'user', 'laporan', 'details'])
            ->where('user_id', Auth::user()->user_id)
            ->where('tugas_jenis', 'pemeriksaan')
            ->whereHas('status', function ($query) {
                $query->where('status_nama', '!=', 'selesai');
            });

        if ($request->has('filter_tugas_jenis') && $request->filter_tugas_jenis != '') {
            $tugas->where('tugas_jenis', $request->filter_tugas_jenis);
        }

        return DataTables::of($tugas->get())
            ->addIndexColumn()
            ->addColumn('tingkat_kerusakan', function ($tugas) {
                // Ambil dari detail pertama, atau sesuaikan jika multi detail
                return $tugas->details->first()->tingkat_kerusakan ?? '-';
            })
            ->addColumn('biaya_perbaikan', function ($tugas) {
                return $tugas->details->first()->biaya_perbaikan ?? '-';
            })
            ->addColumn('laporan', function ($tugas) {
                if ($tugas->laporan) {
                    return '<a href="#" onclick="modalAction(\'' . route('teknisi.show_laporan', $tugas->laporan->laporan_id) . '\')" class="btn btn-link text-info"><i class="fas fa-eye"></i> <span class="ms-1">Laporan</span></a>';
                } else {
                    return '<span class="text-muted">Belum Ada</span>';
                }
            })
            ->addColumn('aksi', function ($tugas) {
                $btn = '<button onclick="modalAction(\'' . route('teknisi.showpemeriksaan', $tugas->tugas_id) . '\')" class="btn btn-info btn-sm mx-1"><i class="fas fa-eye"></i></button>';
                $btn .= '<button onclick="modalAction(\'' . route('teknisi.editpemeriksaan', $tugas->tugas_id) . '\')" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></button>';
                return $btn;
            })
            ->rawColumns(['laporan', 'aksi'])
            ->toJson();
    }

    public function showPemeriksaan($id)
    {
        $tugas = TugasDetail::with(['tugas', 'fasilitas'])
            ->whereHas('tugas', function ($q) {
                $q->where('user_id', Auth::user()->user_id);
            })
            ->where('detail_id', $id)
            ->findOrFail($id);

        $breadcrumb = (object) [
            'title' => 'Detail Pemeriksaan',
            'list'  => ['Home', 'Pemeriksaan', 'Detail']
        ];
        $active_menu = 'pemeriksaan';

        return view('teknisi.showpemeriksaan', compact('breadcrumb', 'active_menu', 'tugas'));
    }


    public function editPemeriksaan($id)
    {
        $tugas = TugasModel::with(['status', 'user'])
            ->where('user_id', Auth::user()->user_id)
            ->where('tugas_jenis', 'pemeriksaan')
            ->findOrFail($id);

        $breadcrumb = (object) [
            'title' => 'Edit Pemeriksaan',
            'list'  => ['Home', 'Tugas', 'Edit Pemeriksaan']
        ];
        $active_menu = 'tugas';

        return view('teknisi.editpemeriksaan', compact('breadcrumb', 'active_menu', 'tugas'));
    }


    public function updatePemeriksaan(Request $request, $id)
    {
        // Validasi input untuk TugasDetail
        $validator = Validator::make($request->all(), [
            'tingkat_kerusakan' => 'required|integer|min:1|max:5',
            'biaya_perbaikan' => 'nullable|numeric|min:0',
            'tugas_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal.',
                'msgField' => $validator->errors()
            ], 422);
        }

        // Ambil status_id untuk "selesai diperiksa"
        $status = StatusModel::where('status_nama', 'selesai diperiksa')->first();
        if (!$status) {
            return response()->json([
                'status' => false,
                'message' => 'Status "selesai diperiksa" tidak ditemukan.',
            ], 422);
        }

        $tugas = TugasModel::findOrFail($id);

        // Update status saja, jenis tugas tetap "pemeriksaan"
        $tugas->update([
            'status_id' => $status->status_id,
            // 'tugas_jenis' => $tugas->tugas_jenis, // Tidak perlu diubah
        ]);

        // Update status laporan jika ada
        if ($tugas->laporan_id) {
            LaporanModel::where('laporan_id', $tugas->laporan_id)
                ->update(['status_id' => $status->status_id]);
        }

        $tugasDetail = TugasDetail::where('tugas_id', $tugas->tugas_id)->firstOrFail();
        $dataDetail = [
            'tingkat_kerusakan' => $request->tingkat_kerusakan,
            'biaya_perbaikan' => $request->biaya_perbaikan,
        ];

        if ($request->hasFile('tugas_image')) {
            if ($tugasDetail->tugas_image && Storage::exists('public/' . $tugasDetail->tugas_image)) {
                Storage::delete('public/' . $tugasDetail->tugas_image);
            }
            $imagePath = $request->file('tugas_image')->store('tugas_images', 'public');
            $dataDetail['tugas_image'] = $imagePath;
        }

        $tugasDetail->update($dataDetail);

        return response()->json([
            'status' => true,
            'message' => 'Data pemeriksaan berhasil diperbarui dan status menjadi selesai diperiksa.',
        ]);
    }


    // HALAMAN PROFIL
    public function showProfile()
    {
        // Ambil user yang sedang login dan relasi role-nya
        $user = auth()->user()->load('role');

        // Buat breadcrumb
        $breadcrumb = (object) [
            'title' => 'Profil Saya',
            'list'  => ['Home', 'Profil']
        ];

        $active_menu = 'profile';

        // Tampilkan view
        return view('teknisi.profile.show', compact('user', 'breadcrumb', 'active_menu'));
    }

    public function editProfile()
    {
        $user = auth()->user();

        $breadcrumb = (object) [
            'title' => 'Edit Profil Saya',
            'list'  => ['Home', 'Profil', 'Edit']
        ];

        $active_menu = 'profile';

        return view('teknisi.profile.edit', compact('user', 'active_menu', 'breadcrumb'));
    }

    public function updateProfile(Request $request)
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

            return redirect()->route('teknisi.profile.show')->with('success', 'Profil berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal update profil: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal memperbarui profil'])->withInput();
        }
    }
}
