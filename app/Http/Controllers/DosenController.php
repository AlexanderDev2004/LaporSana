<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DosenController extends Controller
{
    public function index()
    {
        $breadcrumbs = (object) [
            'title' => 'Selamat Datang',
            'list'  => ['Home', 'Welcome']
        ];

        $active_menu = 'dashboard';
        return view('dosen.dashboard', ['breadcrumb' => $breadcrumbs, 'active_menu' => $active_menu]);
    }

    public function laporan()
    {
        $breadcrumbs = (object) [
            'title' => 'Laporan Kerusakan Fasilitas',
            'list'  => ['Home', 'Laporan']
        ];

        $page = (object) [
            'title' => 'Daftar Laporan Kerusakan'
        ];

        $active_menu = 'laporan saya';
        return view('dosen.laporan.laporan', ['breadcrumb' => $breadcrumbs, 'page' => $page, 'active_menu' => $active_menu]);
    }

    public function create()
    {
        $breadcrumbs = (object) [
            'title' => 'Tambah Laporan',
            'list'  => ['Home', 'Tambah Laporan']
        ];

        $page = (object) [
            'title' => 'Daftar Laporan Kerusakan'
        ];

        $active_menu = 'laporan saya';
        return view('dosen.laporan.create', ['breadcrumb' => $breadcrumbs, 'page' => $page, 'active_menu' => $active_menu]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:m_user.user_id',
            'tanggal_lapor' => 'required|datetime',
            'lantai_id' => 'required|exists:m_lantai.lantai_id',
            'ruangan_id' => 'required|exists:m_ruangan.ruangan_id',
            'fasilitas_id' => 'required|exists:m_fasilitas.fasilitas_id',
            'foto_bukti' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'deskripsi' => 'required|string|max:255',
            'status' => 'required|in:0,1,2,3', // 0: Menunggu Verifikasi, 1: Ditolak, 2: Diproses, 3: Selesai
            'jumlah_pelapor' => 'required|integer|min:1',
        ]);

        $laporan = new LaporanModel();
        $laporan->user_id = auth()->user()->user_id; // Ambil dari user yang sedang login
        $laporan->tanggal_lapor = now(); // Ambil tanggal saat ini
        $laporan->lantai_id = $validated['lantai_id'];
        $laporan->ruangan_id = $validated['ruangan_id'];
        $laporan->fasilitas_id = $validated['fasilitas_id'];
        $laporan->deskripsi = $validated['deskripsi'];
        $laporan->jumlah_pelapor = $validated['jumlah_pelapor'];
        $laporan->status = $validated['status'];

        if ($request->hasFile('foto_bukti')) {
            $laporan->foto_bukti = $request->file('foto_bukti')->store('foto_bukti', 'public');
        }

        $laporan->save();

        return redirect()->route('dosen.laporan')->with('success', 'Laporan berhasil dibuat.');
    }
}
