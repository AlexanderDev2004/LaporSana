<?php

namespace App\Http\Controllers;

use App\Models\LaporanModel;
use App\Models\RoleModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
        $fasilitasList = \App\Models\FasilitasModel::pluck('fasilitas_nama', 'fasilitas_id')->toArray();

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

 private function getSPKData()
    {
        try {
            // Directly query the database instead of making HTTP requests
            return \App\Models\RekomperbaikanModel::with('fasilitas')
                ->orderBy('rank', 'asc')
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            Log::error('Error retrieving SPK data: ' . $e->getMessage());
            return [];
        }
        return view('sarpras.dashboard', compact('breadcrumb', 'active_menu'));
    }
};
