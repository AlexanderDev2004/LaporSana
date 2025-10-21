<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserModel;
use App\Models\RoleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // GET /api/users
    public function index()
    {
        $users = UserModel::with('role')->get();
        return response()->json([
            'status' => true,
            'data' => $users
        ]);
    }

    // GET /api/users/{id}
    public function show($id)
    {
        $user = UserModel::with('role')->find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $user
        ]);
    }

    // POST /api/users
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
                'errors' => $validator->errors()
            ], 422);
        }

        $role = RoleModel::find($request->roles_id);
        $roleName = $role ? strtolower($role->roles_nama) : '';

        if (str_contains($roleName, 'mahasiswa') && empty($request->NIM)) {
            return response()->json([
                'status' => false,
                'message' => 'NIM wajib diisi untuk user dengan role Mahasiswa'
            ], 422);
        } elseif (!str_contains($roleName, 'mahasiswa') && empty($request->NIP)) {
            return response()->json([
                'status' => false,
                'message' => 'NIP wajib diisi untuk user selain Mahasiswa'
            ], 422);
        }

        $data = [
            'username' => $request->username,
            'name' => $request->name,
            'roles_id' => $request->roles_id,
            'password' => Hash::make($request->password),
            'NIM' => $request->NIM,
            'NIP' => $request->NIP,
        ];

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('avatars', $filename, 'public');
            $data['avatar'] = $path;
        }

        $user = UserModel::create($data);

        return response()->json([
            'status' => true,
            'message' => 'User berhasil dibuat',
            'data' => $user
        ]);
    }

    // PUT /api/users/{id}
    public function update(Request $request, $id)
    {
        $user = UserModel::find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:m_user,username,' . $user->user_id . ',user_id|min:3|max:50',
            'name' => 'required|min:3|max:100',
            'roles_id' => 'required|exists:m_roles,roles_id',
            'password' => 'nullable|min:6',
            'NIM' => 'nullable|string|max:20',
            'NIP' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
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
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('avatars', $filename, 'public');
            $data['avatar'] = $path;
        }

        $user->update($data);

        return response()->json([
            'status' => true,
            'message' => 'User berhasil diperbarui',
            'data' => $user
        ]);
    }

    // DELETE /api/users/{id}
    public function destroy($id)
    {
        $user = UserModel::find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'User berhasil dihapus'
        ]);
    }
}
