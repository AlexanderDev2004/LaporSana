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

        $tugas = TugasModel::with(['status', 'user']);

        if ($request->filled('status')) {
            $tugas->where('status_id', $request->status);
        }

        $status = StatusModel::all();
        $user = UserModel::all();



        return view('teknisi.index', compact('user', 'status', 'tugas', 'active_menu', 'breadcrumb'));
    }

    public function list(Request $request)
    {
        $tugas = TugasModel::with(['status', 'user']); 

        // Filter status
        if ($request->has('filter_status') && $request->filter_status != '') {
            $tugas->where('status_id', $request->filter_status);
        }

        return DataTables::of($tugas->get())
            ->addIndexColumn()
            ->addColumn('aksi', function ($tugas) {
                // Gabungkan semua tombol aksi
             
                $btn = '<button onclick="modalAction(\'' . route('teknisi.show', $tugas->tugas_id) . '\')" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></button>';
                $btn .= '<button onclick="modalAction(\'' . route('teknisi.edit', $tugas->tugas_id) . '\')" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></button>';
                $btn .= '<button onclick="modalAction(\'' . route('teknisi.destroy', $tugas->tugas_id) . '\')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>';
                return $btn;
            })
            ->rawColumns(['aksi']) // Kolom aksi mengandung HTML
            ->toJson(); // Pastikan mengembalikan JSON
    }

    public function show(TugasModel $tugas)
    {
        $breadcrumb = (object) [
            'title' => 'Detail Tugas',
            'list'  => ['Home', 'Tugas', 'Detail']
        ];
        $active_menu = 'tugas';
        return view('teknisi.show', compact('breadcrumb', 'active_menu', 'tugas'));
    }
}
