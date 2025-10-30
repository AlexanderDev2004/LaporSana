<?php

namespace App\Http\Controllers;

use App\Models\RoleModel;
use App\Traits\ExcelExportTrait;
use App\Traits\ExcelImportTrait;
use App\Traits\JsonResponseTrait;
use App\Traits\PdfExportTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    use ExcelExportTrait, ExcelImportTrait, JsonResponseTrait, PdfExportTrait;

    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Manajemen Role',
            'list' => ['Home', 'role'],
        ];

        $active_menu = 'roles';
        $role = RoleModel::all();

        return view('admin.roles.index', compact('breadcrumb', 'active_menu', 'role'));
    }

    public function list(Request $request)
    {
        $role = RoleModel::select('roles_id', 'roles_kode', 'roles_nama', 'poin_roles');

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
            'list' => ['Home', 'role', 'Tambah'],
        ];

        $active_menu = 'roles';

        return view('admin.roles.create', compact('breadcrumb', 'active_menu'));
    }

    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'roles_kode' => 'required|string|max:5',
            'roles_nama' => 'required|string|min:3|max:50',
            'poin_roles' => 'nullable|integer|min:0',
        ]);

        // If validation fails, return with errors
        if ($validator->fails()) {
            return $this->jsonValidationError($validator);
        }

        // Create new role
        try {
            $role = new RoleModel;
            $role->roles_kode = $request->roles_kode;
            $role->roles_nama = $request->roles_nama;
            $role->poin_roles = $request->poin_roles;
            $role->save();

            return $this->jsonSuccess('Data role berhasil disimpan');
        } catch (\Exception $e) {
            return $this->jsonError('Gagal menyimpan data: '.$e->getMessage());
        }
    }

    public function edit(string $id)
    {
        $role = RoleModel::find($id);

        return view('admin.roles.edit', ['role' => $role]);
    }

    public function update(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'roles_kode' => 'required|string|max:5',
                'roles_nama' => 'required|string|min:3|max:50',
                'poin_roles' => 'nullable|integer|min:0',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return $this->jsonError('Validasi gagal.', $validator->errors()->toArray());
            }

            $check = RoleModel::find($id);
            if ($check) {
                $check->update($request->all());

                return $this->jsonSuccess('Data berhasil diupdate');
            } else {
                return $this->jsonError('Data tidak ditemukan');
            }
        }

        return redirect('/');
    }

    public function confirm(string $id)
    {
        $role = RoleModel::find($id);

        return view('admin.roles.confirm', ['role' => $role]);
    }

    public function delete(Request $request, $id)
    {
        $role = RoleModel::find($id);
        if (! $role) {
            return $this->jsonError('Data tidak ditemukan');
        }
        try {
            $role->delete();

            return $this->jsonSuccess('Data role berhasil dihapus');
        } catch (\Exception $e) {
            return $this->jsonError('Gagal menghapus data: '.$e->getMessage());
        }
    }

    public function show(RoleModel $role)
    {
        $breadcrumb = (object) [
            'title' => 'Detail role',
            'list' => ['Home', 'role', 'Detail'],
        ];

        $active_menu = 'roles';

        return view('admin.roles.show', compact('breadcrumb', 'active_menu', 'role'));
    }

    public function import()
    {
        return view('admin.roles.import');
    }

    public function import_ajax(Request $request)
    {
        return $this->importExcel(
            $request,
            'file_roles',
            function ($value) {
                return [
                    'roles_kode' => $value['A'],
                    'roles_nama' => $value['B'],
                ];
            },
            RoleModel::class
        );
    }

    public function export_excel()
    {
        $roles = RoleModel::select('roles_kode', 'roles_nama')
            ->orderBy('roles_nama')
            ->get();

        $headers = [
            'A' => 'No',
            'B' => 'Role Kode',
            'C' => 'Role Nama',
        ];

        $data = [];
        $no = 1;
        foreach ($roles as $role) {
            $data[] = [
                'A' => $no,
                'B' => $role->roles_kode,
                'C' => $role->roles_nama,
            ];
            $no++;
        }

        $spreadsheet = $this->createSpreadsheet($headers, $data, 'Data Roles');
        $filename = 'Data Roles '.date('Y-m-d H:i:s').'.xlsx';
        $this->exportSpreadsheet($spreadsheet, $filename);
    } // end function export_excel

    public function export_pdf()
    {
        $roles = RoleModel::select('roles_kode', 'roles_nama')
            ->orderBy('roles_kode')
            ->get();

        return $this->generatePdf(
            'admin.roles.export_pdf',
            ['roles' => $roles],
            'Data Roles '.date('Y-m-d H:i:s').'.pdf',
            'portrait'
        );
    }
}
