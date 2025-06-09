<?php

namespace App\Http\Controllers;

use App\Models\RoleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;

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

    public function import()
        {
                return view('admin.roles.import');
        }

     public function import_ajax(Request $request)
        {
                if ($request->ajax() || $request->wantsJson()) {
                        $rules = [
                                // validasi file harus xls atau xlsx, max 1MB
                                'file_roles' => ['required', 'mimes:xlsx', 'max:1024']
                        ];

                        $validator = Validator::make($request->all(), $rules);

                        if ($validator->fails()) {
                                return response()->json([
                                        'status' => false,
                                        'message' => 'Validasi Gagal',
                                        'msgField' => $validator->errors()
                                ]);
                        }

                        $file = $request->file('file_roles'); // ambil file dari request

                        $reader = IOFactory::createReader('Xlsx'); // load reader file excel
                        $reader->setReadDataOnly(true); // hanya membaca data
                        $spreadsheet = $reader->load($file->getRealPath()); // load file excel
                        $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif

                        $data = $sheet->toArray(null, false, true, true); // ambil data excel

                        $insert = [];

                        if (count($data) > 1) { // jika data lebih dari 1 baris
                                foreach ($data as $baris => $value) {
                                        if ($baris > 1) { // baris ke 1 adalah header, maka lewati
                                                $insert[] = [
                                                        'roles_kode'        => $value['A'],
                                                        'roles_nama'        => $value['B'],
                                                        'created_at'        => now(),
                                                ];
                                        }
                                }

                                if (count($insert) > 0) {
                                        // insert data ke database, jika data sudah ada, maka diabaikan
                                        RoleModel::insertOrIgnore($insert);
                                }

                                return response()->json([
                                        'status'  => true,
                                        'message' => 'Data berhasil diimport'
                                ]);
                        } else {
                                return response()->json([
                                        'status'  => false,
                                        'message' => 'Tidak ada data yang diimport'
                                ]);
                        }
                }

                return redirect('/');
        }

        public function export_excel()
        {
                //ambil data role yang akan di export
                $roles = RoleModel::select( 'roles_kode', 'roles_nama')
                        ->orderBy('roles_nama')
                        ->get();

                // load library excel
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif

                $sheet->setCellValue('A1', 'No');
                $sheet->setCellValue('B1', 'Role Kode');
                $sheet->setCellValue('C1', 'Role Nama');

                $sheet->getStyle('A1:C1')->getFont()->setBold(true); // bold header

                $no = 1;        // nomor data dimulai dari 1
                $baris = 2;     //baris data dimulai dari baris ke 2
                foreach ($roles as $key => $value) {
                        $sheet->setCellValue('A' . $baris, $no);
                        $sheet->setCellValue('B' . $baris, $value->roles_kode);
                        $sheet->setCellValue('C' . $baris, $value->roles_nama);
                        $baris++;
                        $no++;
                }

                foreach (range('A', 'C') as $columnID) {
                        $sheet->getColumnDimension($columnID)->setAutoSize(true); //set auto size untuk kolom
                }

                $sheet->setTitle('Data Roles'); // set title sheet
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $filename = 'Data Roles ' . date('Y-m-d H:i:s') . '.xlsx';
                header('Content-Type: application/vnd. openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Cache-Control: max-age=0');
                header('Cache-Control: max-age=1');
                header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                header('Cache-Control: cache, must-revalidate');
                header('Pragma: public');
                $writer->save('php://output');
                exit;
        } // end function export_excel

        public function export_pdf()
        {
                  $roles = RoleModel::select( 'roles_kode', 'roles_nama')
                        ->orderBy('roles_kode')
                        ->get();

                //use Barryvdh\DomPDF\Facade\Pdf;
                $pdf = Pdf::loadView('admin.roles.export_pdf', ['roles' => $roles]);
                $pdf->setPaper('a4', 'potrait'); //Set ukuran kertas dan orientasi
                $pdf->setOption('isRemoteEnabled', true); // set true jika ada gambar dari url
                $pdf->render();

                return $pdf->stream('Data Roles ' . date('Y-m-d H:i:s') . '.pdf');
        }
}
