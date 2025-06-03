<?php

namespace App\Http\Controllers;

use App\Models\RoleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Manajemen Role',
            'list'  => ['Home', 'role']
        ];

        $active_menu = 'roles';
        $role = RoleModel::all();

        return view('admin.roles.index', compact('breadcrumb', 'active_menu', 'role'));
    }

     public function list(Request $request)
    {
        $role = RoleModel::select('roles_id', 'roles_kode', 'roles_nama');

        if ($request->rolea_id) {
            $role->where('roles_id', $request->rolea_id);
        }

        return DataTables::of($role)
            ->addIndexColumn()
            ->addColumn('aksi', function ($role) {
                // $btn = '<a href="' . url('/role/' . $role->role_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                // $btn .= '<a href="' . url('/role/' . $role->role_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                // $btn .= '<form class="d-inline-block" method="POST" action="' . url('/role/' . $role->role_id) . '">'
                //     . csrf_field() . method_field('DELETE')
                //     . '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah anda yakin menghapus data ini?\');">Hapus</button></form>';

                $btn = '<button onclick="modalAction(\''.route('admin.roles.show', $role->roles_id).'\')" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></button>';
                $btn .= '<button onclick="modalAction(\''.route('admin.roles.edit', $role->roles_id).'\')" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></button>';
                $btn .= '<button onclick="modalAction(\''.route('admin.roles.confirm', $role->roles_id).'\')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah role',
            'list'  => ['Home', 'role', 'Tambah']
        ];

        $active_menu = 'roles';
        return view('admin.roles.create', compact('breadcrumb', 'active_menu'));
    }
  
        public function store(Request $request)
        {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'roles_kode' => 'required|string|max:5',
                'roles_nama' => 'required|string|min:3|max:50'
            ]);

            // If validation fails, return with errors
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            // Create new role
            try {
                $role = new RoleModel();
                $role->roles_kode = $request->roles_kode;
                $role->roles_nama = $request->roles_nama;
                $role->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Data role berhasil disimpan'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal menyimpan data: ' . $e->getMessage()
                ]);
            }
        }
    public function edit(string $id) {
            $role = RoleModel::find($id);

            return view('admin.roles.edit', ['role' => $role]); 
        }

      public function update(Request $request, $id) {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'roles_kode' => 'required|string|max:5',
                'roles_nama' => 'required|string|min:3|max:50'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }

            $check = RoleModel::find($id);
            if ($check) {
                $check->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diupdate'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }
        return redirect('/');
    }

    public function confirm(string $id) {
        $role = RoleModel::find($id);

        return view('admin.roles.confirm', ['role' => $role]);
    }
    public function delete(Request $request, $id)
    {
        $role = RoleModel::find($id);
        if (!$role) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }
        try {
            $role->delete();
            return response()->json([
                'status' => true,
                'message' => 'Data role berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ]);
        }
    }

    public function show(RoleModel $role)
    {
        $breadcrumb = (object) [
            'title' => 'Detail role',
            'list'  => ['Home', 'role', 'Detail']
        ];

        $active_menu = 'roles';

        return view('admin.roles.show', compact('breadcrumb', 'active_menu', 'role'));
    }
}
