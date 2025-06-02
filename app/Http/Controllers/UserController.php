<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

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

        return view('admin.users.index', compact( 'users', 'roles'));
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
}
