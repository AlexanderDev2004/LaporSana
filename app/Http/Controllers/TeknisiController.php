<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeknisiController extends Controller
{
    public function index()
    {
        $breadcrumbs = (object) [
            'title' => 'Selamat Datang',
            'list'  => ['Home', 'Welcome']
        ];

        $active_menu = 'dashboard';
        return view('teknisi.dashboard', ['breadcrumb' => $breadcrumbs, 'active_menu' => $active_menu]);
    }

    public function tugas()
    {
        $breadcrumbs = (object) [
            'title' => 'Tugas Perbaikan',
            'list'  => ['Home', 'Welcome']
        ];
        $active_menu = 'tugas';
        return view('teknisi.tugas', ['breadcrumb' => $breadcrumbs, 'active_menu' => $active_menu]);
    }

    public function riwayat()
    {
        $breadcrumbs = (object) [
            'title' => 'Tugas Perbaikan',
            'list'  => ['Home', 'Welcome']
        ];
        $active_menu = 'riwayat';
        return view('teknisi.riwayat', ['breadcrumb' => $breadcrumbs, 'active_menu' => $active_menu]);
    }

public function show($id)
{
    $laporans = [
        1 => [
            'id' => 1,
            'fasilitas_nama' => 'Proyektor',
            'fasilitas_lokasi' => 'LIG1 Lantai 7',
            'tanggal_penugasan' => '2025-04-05',
            'tanggal_selesai' => '2025-04-06',
            'deskripsi' => 'Proyektor tidak menyala saat digunakan.',
            'feedback_rating' => 3.5,
            'feedback_komentar' => 'Sudah bagus, tapi agak lama responnya.'
        ],
        2 => [
            'id' => 2,
            'fasilitas_nama' => 'Kipas Angin Kelas',
            'fasilitas_lokasi' => 'LIG1 Lantai 7',
            'tanggal_penugasan' => '2025-04-01',
            'tanggal_selesai' => '2025-04-02',
            'deskripsi' => 'Kipas angin tidak berputar.',
            'feedback_rating' => 5,
            'feedback_komentar' => 'Pelayanan cepat dan hasil memuaskan.'
        ],
    ];

    if (!isset($laporans[$id])) {
        abort(404);
    }

    $laporan = (object) $laporans[$id]; // Pastikan ini object
    $active_menu = 'laporan';

    $breadcrumb = (object) [
        'title' => 'Detail Laporan',
        'list' => [
            (object) ['label' => 'Dashboard', 'url' => route('teknisi.dashboard')],
            (object) ['label' => 'Detail Laporan', 'url' => '']
        ]
    ];

    return view('teknisi.detail', compact('laporan', 'active_menu', 'breadcrumb'));
}


}
