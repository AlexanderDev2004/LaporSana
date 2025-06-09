<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class UserController extends Controller
{
    public function index(Request $request)
    {


        $active_menu = 'dashboard';
        $query = UserModel::with('role');

        if ($request->filled('role')) {
            $query->where('roles_id', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%$search%")
                    ->orWhere('name', 'like', "%$search%");
            });
        }

        $users = $query->paginate(10);
        $roles = RoleModel::all();

        return view('admin.users.index', compact('users', 'roles'));
    }



    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah User',
            'list'  => ['Home', 'User', 'Tambah']
        ];

        $active_menu = 'users';
        $roles = RoleModel::all();

        return view('admin.users.create', compact('roles', 'active_menu', 'breadcrumb'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|unique:m_user,username',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'roles_id' => 'required|exists:m_roles,roles_id', // Ubah ke m_roles
            'avatar' => 'nullable|image|max:2048',
            'NIM' => 'nullable|string|max:20',
            'NIP' => 'nullable|string|max:20'
        ]);

        $user = new UserModel();
        $user->username = $validated['username'];
        $user->name = $validated['name'];
        $user->password = bcrypt($validated['password']);
        $user->roles_id = $validated['roles_id'];
        $user->NIM = $validated['NIM'];
        $user->NIP = $validated['NIP'];

        if ($request->hasFile('avatar')) {
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }


    public function edit(UserModel $user)
    {
        $breadcrumb = (object) [
            'title' => 'Edit User',
            'list'  => ['Home', 'User', 'Edit']
        ];

        $active_menu = 'users';
        $roles = RoleModel::all();

        return view('admin.users.edit', compact('user', 'roles', 'active_menu', 'breadcrumb'));
    }


    public function update(Request $request, UserModel $user)
    {
        $validated = $request->validate([
            'username' => 'required|unique:m_user,username,' . $user->user_id . ',user_id',
            'name' => 'required',
            'roles_id' => 'required|exists:m_roles,roles_id',
            'NIM' => 'nullable|string|max:20',
            'NIP' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'password' => 'nullable|string|min:6',
        ]);

        try {
            // Debug data sebelum update
            Log::info('Data sebelum update:', $user->toArray());
            Log::info('Data validasi:', $validated);

            $data = [
                'username' => $validated['username'],
                'name' => $validated['name'],
                'roles_id' => $validated['roles_id'],
                'NIM' => $validated['NIM'],
                'NIP' => $validated['NIP'],
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($validated['password']);
            }

            if ($request->hasFile('avatar')) {
                // Hapus avatar lama jika ada
                if ($user->avatar) {
                    Storage::delete('public/' . $user->avatar);
                }
                $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            $user->update($data);

            // Debug data setelah update
            Log::info('Data setelah update:', $user->fresh()->toArray());

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal memperbarui user: ' . $e->getMessage())
                ->withInput();
        }
    }


    public function destroy($id)
    {
        $user = UserModel::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
    public function list(Request $request)
    {
        $breadcrumb = (object) [
            'title' => 'Manajemen User',
            'list'  => ['Home', 'User']
        ];

        $active_menu = 'users';

        // Ambil semua role untuk dropdown filter
        $roles = RoleModel::all();

        // Buat query user dengan relasi role
        $query = UserModel::with('role');

        // Terapkan filter role jika ada
        if ($request->filled('role') && $request->role !== '') {
            $query->where('roles_id', $request->role);
        }

        // Ambil data dengan paginasi (10 per halaman)
        $users = $query->paginate(10);

        return view('admin.users.index', compact('breadcrumb', 'active_menu', 'users', 'roles'));
    }


    public function show(UserModel $user)
    {
        $breadcrumb = (object) [
            'title' => 'Detail User',
            'list'  => ['Home', 'User', 'Detail']
        ];

        $active_menu = 'users';

        return view('admin.users.show', compact('user', 'active_menu', 'breadcrumb'));
    }

    public function import()
    {
        return view('admin.users.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                // validasi file harus xls atau xlsx, max 1MB
                'file_users' => ['required', 'mimes:xlsx', 'max:1024']
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $file = $request->file('file_users'); // ambil file dari request

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
                            'roles_id'      => $value['A'],
                            'username'      => $value['B'],
                            'name'          => $value['C'],
                            'password'      => $value['D'],
                            'NIM'           => $value['E'],
                            'NIP'           => $value['F'],
                            'avatar'        => $value['G'] ?? null, // jika ada avatar, jika tidak ada maka null
                            'created_at'     => now(),
                        ];
                    }
                }

                if (count($insert) > 0) {
                    // insert data ke database, jika data sudah ada, maka diabaikan
                    UserModel::insertOrIgnore($insert);
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
        //ambil data user yang akan di export
        $user = UserModel::select('roles_id', 'username', 'name', 'NIM', 'NIP', 'avatar')
            ->orderBy('roles_id')
            ->with('roles')
            ->get();

        // load library excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Avatar');
        $sheet->setCellValue('C1', 'Nama Role');
        $sheet->setCellValue('D1', 'Username');
        $sheet->setCellValue('E1', 'Nama');
        $sheet->setCellValue('F1', 'NIM');
        $sheet->setCellValue('G1', 'NIP');

        $sheet->getStyle('A1:G1')->getFont()->setBold(true); // bold header

        $no = 1;        // nomor data dimulai dari 1
        $baris = 2;     //baris data dimulai dari baris ke 2
        foreach ($user as $key => $value) {
            $sheet->setCellValue('A' . $baris, $no);
             if ($value->avatar) {
                $sheet->setCellValue('B' . $baris, asset('storage/' . $value->avatar));
            } else {
                $sheet->setCellValue('B' . $baris, 'Tidak ada avatar');
            }
            $sheet->setCellValue('C' . $baris, $value->roles->roles_nama);
            $sheet->setCellValue('D' . $baris, $value->username);
            $sheet->setCellValue('E' . $baris, $value->name);
            $sheet->setCellValue('F' . $baris, $value->NIM);
            $sheet->setCellValue('G' . $baris, $value->NIP);
           
            $baris++;
            $no++;
        }

        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true); //set auto size untuk kolom
        }

        $sheet->setTitle('Data User'); // set title sheet
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data User ' . date('Y-m-d H:i:s') . '.xlsx';
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
         $user = UserModel::select('roles_id', 'username', 'name', 'NIM', 'NIP', 'avatar')
            ->orderBy('roles_id')
            ->with('roles')
            ->get();

        //use Barryvdh\DomPDF\Facade\Pdf;
        $pdf = Pdf::loadView('admin.users.export_pdf', ['user' => $user]);
        $pdf->setPaper('a4', 'potrait'); //Set ukuran kertas dan orientasi
        $pdf->setOption('isRemoteEnabled', true); // set true jika ada gambar dari url
        $pdf->render();

        return $pdf->stream('Data User ' . date('Y-m-d H:i:s') . '.pdf');
    }
}
