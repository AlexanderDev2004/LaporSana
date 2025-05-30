<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    public function index()
    {
        $breadcrumbs = (object) [
            'title' => 'Selamat Datang',
            'list'  => ['Home', 'Welcome']
        ];

        $active_menu = 'dashboard';
        return view('mahasiswa.dashboard', ['breadcrumb' => $breadcrumbs, 'active_menu' => $active_menu]);
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

        $active_menu = 'laporan';
        return view('mahasiswa.laporan', ['breadcrumb' => $breadcrumbs, 'page' => $page, 'active_menu' => $active_menu]);
    }

    public function create(Request $request)
    {
        return view('mahasiswa.create');
    }
}
