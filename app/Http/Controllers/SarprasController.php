<?php

namespace App\Http\Controllers;

use App\Models\LaporanModel;
use App\Models\RoleModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class SarprasController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Selamat Datang',
            'list'  => ['Home', 'Welcome']
        ];

        $active_menu = 'dashboard';
        return view('sarpras.dashboard', compact('breadcrumb', 'active_menu'));
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

public function verifikasilaporan()
    {
        $breadcrumb = (object) [
            'title' => 'Verifikasi Laporan Kerusakan',
            'list'  => ['Home', 'Verifikasi Laporan']
        ];

        $page = (object) [
            'title' => 'Daftar Laporan Kerusakan'
        ];

        $active_menu = 'verifikasi laporan';

        return view('sarpras.verifikasi', compact('breadcrumb', 'page', 'active_menu'));
    }
    
    public function listLaporan(Request $request)
{
    $laporans = LaporanModel::with(['details.fasilitas.ruangan.lantai', 'status'])
        ->orderBy('status_id', 'asc')
        ->where('status_id', 1) // hanya mengambil status yang sedang dalam proses
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
        $detailUrl = route('sarpras.show', ['laporan_id' => $laporan->laporan_id]);
        $btn = '<button onclick="modalAction(\''.$detailUrl.'\')" class="btn btn-info btn-sm">Detail</button>';

        if ($laporan->status_id == 1) {
            $approveUrl = route('sarpras.approve', ['laporan_id' => $laporan->laporan_id]);
            $rejectUrl = route('sarpras.reject', ['laporan_id' => $laporan->laporan_id]);

            $btn .= '
                <form action="'.$approveUrl.'" method="POST" class="d-inline form-approve" style="margin-left:4px;">
                    '.csrf_field().'
                    <button type="button" class="btn btn-success btn-sm btn-approve">Setujui</button>
                </form>
                <form action="'.$rejectUrl.'" method="POST" class="d-inline form-reject" style="margin-left:4px;">
                    '.csrf_field().'
                    <button type="button" class="btn btn-danger btn-sm btn-reject">Tolak</button>
                </form>';
        } else {
            $btn .= '<span class="text-muted ml-2">Sudah diverifikasi</span>';
        }

        return $btn;
    })
        ->rawColumns(['status.status_nama', 'aksi'])
        ->make(true);
}


    public function showLaporan($laporan_id)
    {
        $laporan = LaporanModel::with(['details.fasilitas.ruangan.lantai', 'status'])
            ->where('laporan_id', $laporan_id)
            ->firstOrFail();

        return view('sarpras.show', compact('laporan'));
    }

    public function approve($laporan_id)
{
    $laporan = LaporanModel::findOrFail($laporan_id);
    $laporan->status_id = 3; // Misalnya 4 = Disetujui
    $laporan->save();

    return back()->with('success', 'Laporan telah disetujui.');
}

public function reject($laporan_id)
{
    $laporan = LaporanModel::findOrFail($laporan_id);
    $laporan->status_id = 2; // Misalnya 2 = Ditolak
    $laporan->save();

    return back()->with('error', 'Laporan telah ditolak.');
}

public function riwayatlaporan()
    {
        $breadcrumb = (object) [
            'title' => 'Riwayat Laporan Kerusakan',
            'list'  => ['Home', 'Riwayat Laporan']
        ];

        $page = (object) [
            'title' => 'Daftar Riwayat Laporan Kerusakan'
        ];

        $active_menu = 'riwayat laporan';

        return view('sarpras.riwayat', compact('breadcrumb', 'page', 'active_menu'));
    }

    public function listRiwayat(Request $request)
{
    $laporans = LaporanModel::with(['details.fasilitas.ruangan.lantai', 'status'])
        ->where('status_id', '!=', 1) // hanya status selain 1
        ->orderBy('status_id', 'asc')
        ->get();

    return DataTables::of($laporans)
        ->addIndexColumn()
        ->editColumn('status.status_nama', function ($laporan) {
            $status = $laporan->status->status_nama ?? 'Tidak Diketahui';
            switch ($laporan->status_id) {
                case 2: return '<span class="badge badge-danger">' . $status . '</span>';
                case 3: return '<span class="badge badge-info">' . $status . '</span>';
                case 4: return '<span class="badge badge-success">' . $status . '</span>';
                default: return '<span class="badge badge-secondary">' . $status . '</span>';
            }
        })
        ->addColumn('aksi', function ($laporan) {
            $detailUrl = route('sarpras.show', ['laporan_id' => $laporan->laporan_id]);
            $btn = '<button onclick="modalAction(\''.$detailUrl.'\')" class="btn btn-info btn-sm">Detail</button>';

            // Tambahan teks "Sudah dikonfirmasi" di samping tombol Detail
            $btn .= '<span class="text-success ml-2">Sudah diverivikasi</span>';

            return $btn;
        })
        ->rawColumns(['status.status_nama', 'aksi'])
        ->make(true);
}
};