<?php

namespace App\Http\Controllers;

use App\Models\StatusModel;
use App\Models\TugasModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
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

        return view('teknisi.dashboard', compact('breadcrumb', 'active_menu'));
    }

    public function index(Request $request)
    {
        $active_menu = 'index';
        $breadcrumb = (object) [
            'title' => 'Daftar Tugas',
            'list' => ['Home', 'Tugas']
        ];

        $query = TugasModel::with(['status', 'user']);

        if ($request->filled('status')) {
            $query->where('status_id', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%$search%")
                    ->orWhere('name', 'like', "%$search%");
            });
        }

        $tugas = $query->paginate(10);
        $status = StatusModel::all();
        $user = UserModel::all();



        return view('teknisi.index', compact('user', 'status', 'tugas', 'active_menu', 'breadcrumb'));
    }

    public function list(Request $request)
    {
        $query = TugasModel::with(['status', 'user']); // Gunakan with() untuk eager loading

        // Filter status
        if ($request->has('filter_status') && $request->filter_status != '') {
            $query->where('status_id', $request->filter_status);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($tugas) {
                // Gabungkan semua tombol aksi
                return '
                <button onclick="modalAction(\'' . route('teknisi.show', $tugas->tugas_id) . '\')" 
                    class="btn btn-info btn-sm">Detail</button>
                <button onclick="modalAction(\'' . route('teknisi.edit', $tugas->tugas_id) . '\')" 
                    class="btn btn-warning btn-sm">Edit</button>
                <button onclick="modalAction(\'' . route('teknisi.destroy', $tugas->tugas_id) . '\')" 
                    class="btn btn-danger btn-sm">Hapus</button>
            ';
            })
            ->rawColumns(['aksi']) // Kolom aksi mengandung HTML
            ->toJson(); // Pastikan mengembalikan JSON
    }

     public function show(string $id)
        {

                $tugas = TugasModel::with('user')->find($id);

                $breadcrumb = (object) [
                        'title' => 'Detail Tugas',
                        'list'  => ['Home', 'Tugas', 'Detail']
                ];

                $page = (object) [
                        'title' => 'Detail tugas'
                ];

                $activeMenu = 'detail'; // set menu yang sedang aktif

                return view('teknisi.detail', ['breadcrumb' => $breadcrumb, 'page' => $page, 'tugas' => $tugas, 'activeMenu' => $activeMenu]);
        }
}
