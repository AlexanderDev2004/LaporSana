<?php

namespace App\Http\Controllers;

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

        // tugas terbaru yang aktif kecuali status_id != dibatalkan dan selesai)
        $tugasTerbaru = TugasModel::where('user_id', Auth::user()->user_id)
            ->whereHas('status', function ($q) {
                $q->whereNotIn('status_nama', ['dibatalkan', 'selesai']);
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

        // array 12 bulan dengan default 0 jumlah
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
            ->where('user_id', Auth::user()->user_id)
            ->whereHas('status', function ($query) {
                $query->where('status_nama', '!=', 'selesai');
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
            ->where('user_id', Auth::user()->user_id) // filter user
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
                $btn = '<button onclick="modalAction(\'' . route('teknisi.show', $tugas->tugas_id) . '\')" class="btn btn-info btn-sm mx-1"><i class="fas fa-eye"></i></button>';
                $btn .= '<button onclick="modalAction(\'' . route('teknisi.edit', $tugas->tugas_id) . '\')" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->toJson();
    }

    public function show($id)
    {
        $tugas = TugasDetail::with(['tugas', 'fasilitas'])
            ->whereHas('tugas', function ($q) {
                $q->where('user_id', Auth::user()->user_id);
            })
            ->findOrFail($id);

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
            ->where('user_id', Auth::user()->user_id) // filter user
            ->whereHas('status', function ($query) {
                $query->where('status_nama', 'selesai');
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


    public function edit($id)
    {
        $tugas = TugasModel::with(['status', 'user'])
            ->where('user_id', Auth::user()->user_id)
            ->findOrFail($id);

        $breadcrumb = (object) [
            'title' => 'Edit Tugas',
            'list'  => ['Home', 'Tugas', 'Edit']
        ];
        $active_menu = 'tugas';

        // Pilih view sesuai jenis tugas
        if ($tugas->tugas_jenis === 'pemeriksaan') {
            return view('teknisi.editpemeriksaan', compact('breadcrumb', 'active_menu', 'tugas'));
        } else {
            // Default ke edit.blade.php untuk perbaikan atau jenis lain
            return view('teknisi.edit', compact('breadcrumb', 'active_menu', 'tugas'));
        }
    }

    public function update(Request $request, $id)
    {
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
            'tugas_selesai' => 'nullable|date|after_or_equal:tugas_mulai',
            // validasi lain sesuai kebutuhan
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

        // Jika jenis tugas sebelumnya adalah pemeriksaan, ubah ke perbaikan
        $jenis_tugas_baru = $tugas->tugas_jenis === 'pemeriksaan' ? 'perbaikan' : $tugas->tugas_jenis;

        $tugas->update([
            'status_id' => $status->status_id,
            'tugas_jenis' => $jenis_tugas_baru,
            'tugas_selesai' => $request->tugas_selesai,
            // tambahkan field lain jika perlu
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Data tugas berhasil diperbarui.'
        ]);
    }


    // HALAMAN
  public function showProfile()
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
        Log::error('Gagal update profil: '.$e->getMessage());
        return back()->withErrors(['error' => 'Gagal memperbarui profil'])->withInput();
    }
}

}