<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Manajemen User',
            'list'  => ['Home', 'User']
        ];

        $active_menu = 'users';
        $roles = RoleModel::all();

        return view('admin.users.index', compact('breadcrumb', 'active_menu', 'roles'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:m_user,username|min:3|max:50',
            'name' => 'required|min:3|max:100',
            'roles_id' => 'required|exists:m_roles,roles_id',
            'password' => 'required|min:6',
            'NIM' => 'nullable|string|max:20',
            'NIP' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'msgField' => $validator->errors()
            ]);
        }

        try {
            // Get role information to check if it's a "Mahasiswa" role
            $role = RoleModel::find($request->roles_id);
            $roleName = $role ? strtolower($role->roles_nama) : '';

            // Additional validation based on role
            if (str_contains($roleName, 'mahasiswa') && empty($request->NIM)) {
                return response()->json([
                    'status' => false,
                    'message' => 'NIM wajib diisi untuk user dengan role Mahasiswa',
                    'msgField' => ['NIM' => ['NIM wajib diisi untuk Mahasiswa']]
                ]);
            } elseif (!str_contains($roleName, 'mahasiswa') && !empty($roleName) && empty($request->NIP)) {
                return response()->json([
                    'status' => false,
                    'message' => 'NIP wajib diisi untuk user selain Mahasiswa',
                    'msgField' => ['NIP' => ['NIP wajib diisi untuk role selain Mahasiswa']]
                ]);
            }

            $data = [
                'username' => $request->username,
                'name' => $request->name,
                'roles_id' => $request->roles_id,
                'password' => Hash::make($request->password),
                'NIM' => $request->NIM,
                'NIP' => $request->NIP,
            ];

            // Handle file upload jika ada
            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('avatars', $filename, 'public');
                $data['avatar'] = $path;
            }

            UserModel::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Data user berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat menyimpan user: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage()
            ]);
        }
    }

    public function create()
    {
        $roles = RoleModel::all();
        return view('Admin.users.create', compact('roles'));
    }


    public function edit(UserModel $user, Request $request)
    {
        $roles = RoleModel::all();

        // If it's an AJAX request (modal), return just the modal content
        if ($request->ajax()) {
            return view('admin.users.edit', compact('user', 'roles'));
        }

        // Otherwise, return the full page for direct navigation
        $breadcrumb = (object) [
            'title' => 'Edit User',
            'list'  => ['Home', 'User', 'Edit']
        ];
        $active_menu = 'users';

        return view('admin.users.edit', compact('user', 'roles', 'active_menu', 'breadcrumb'));
    }

    public function update(Request $request, UserModel $user)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:m_user,username,' . $user->user_id . ',user_id|min:3|max:50',
            'name' => 'required|min:3|max:100',
            'roles_id' => 'required|exists:m_roles,roles_id',
            'NIM' => 'nullable|string|max:20',
            'NIP' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Get role information to check if it's a "Mahasiswa" role
            $role = RoleModel::find($request->roles_id);
            $roleName = $role ? strtolower($role->roles_nama) : '';

            // Additional validation based on role
            if (str_contains($roleName, 'mahasiswa') && empty($request->NIM)) {
                $nimError = 'NIM wajib diisi untuk user dengan role Mahasiswa';

                if ($request->ajax()) {
                    return response()->json([
                        'status' => false,
                        'message' => $nimError,
                        'msgField' => ['NIM' => [$nimError]]
                    ]);
                }

                return redirect()->back()
                    ->withErrors(['NIM' => $nimError])
                    ->withInput();
            } elseif (!str_contains($roleName, 'mahasiswa') && !empty($roleName) && empty($request->NIP)) {
                $nipError = 'NIP wajib diisi untuk role selain Mahasiswa';

                if ($request->ajax()) {
                    return response()->json([
                        'status' => false,
                        'message' => $nipError,
                        'msgField' => ['NIP' => [$nipError]]
                    ]);
                }

                return redirect()->back()
                    ->withErrors(['NIP' => $nipError])
                    ->withInput();
            }

            $data = [
                'username' => $request->username,
                'name' => $request->name,
                'roles_id' => $request->roles_id,
                'NIM' => $request->NIM,
                'NIP' => $request->NIP,
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            if ($request->hasFile('avatar')) {
                // Hapus avatar lama jika ada
                if ($user->avatar) {
                    Storage::delete('public/' . $user->avatar);
                }
                $file = $request->file('avatar');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('avatars', $filename, 'public');
                $data['avatar'] = $path;
            }

            $user->update($data);

            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => 'User berhasil diperbarui'
                ]);
            }

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal memperbarui user: ' . $e->getMessage()
                ]);
            }

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
        $users = UserModel::select('user_id', 'username', 'name', 'roles_id', 'NIM', 'NIP', 'avatar')
            ->with('role');

        // Filter berdasarkan role jika ada
        if ($request->roles_id) {
            $users->where('roles_id', $request->roles_id);
        }

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('role_nama', function ($user) {
                return $user->role ? $user->role->roles_nama : '-';
            })
            ->addColumn('avatar_img', function ($user) {
                if ($user->avatar) {
                    return '<img src="' . asset('storage/' . $user->avatar) . '" class="img-circle" width="50" height="50">';
                }
                return '<img src="' . asset('LaporSana/dist/img/user2-160x160.jpg') . '" class="img-circle" width="50" height="50">';
            })
            ->addColumn('aksi', function ($user) {
                $btn = '<button onclick="modalAction(\'' . route('admin.users.show', $user->user_id) . '\')" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></button> ';
                $btn .= '<button onclick="modalAction(\'' . route('admin.users.edit', $user->user_id) . '\')" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></button> ';
                $btn .= '<button onclick="modalAction(\'' . route('admin.users.confirm', $user->user_id) . '\')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>';
                return $btn;
            })
            ->rawColumns(['avatar_img', 'aksi'])
            ->make(true);
    }

    public function confirm($id)
    {
        $user = UserModel::with('role')->find($id);
        return view('Admin.users.confirm', compact('user'));
    }


    public function delete($id)
    {
        try {
            $user = UserModel::findOrFail($id);

            // Delete avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->delete();

            return response()->json([
                'status' => true,
                'message' => 'User berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus user: ' . $e->getMessage(),
                'msgField' => []
            ]);
        }
    }
    public function show(Request $request, $id)
    {
        $user = UserModel::with('role')->find($id);

        // If it's an AJAX request (modal), return just the modal content
        if ($request->ajax()) {
            return view('admin.users.show', compact('user'));
        }

        // Otherwise, return the full page for direct navigation
        $breadcrumb = (object) [
            'title' => 'Detail User',
            'list'  => ['Home', 'User', 'Detail']
        ];
        $active_menu = 'users';

        return view('admin.users.show', compact('user', 'active_menu', 'breadcrumb'));
    }
}
