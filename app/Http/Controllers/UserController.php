<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Selamat Datang',
            'list'  => ['Home', 'Welcome']
        ];

        $active_menu = 'dashboard';
        return view('admin.dashboard', compact('breadcrumb', 'active_menu'));
    }

    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah User',
            'list'  => ['Home', 'User', 'Tambah']
        ];

        $active_menu = 'users';
        $roles = RoleModel::all();

        return view('admin.create', compact('roles', 'active_menu', 'breadcrumb'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|unique:m_user,username',
            'nama' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'roles_id' => 'required|exists:m_roles,roles_id', // Ubah ke m_roles
            'avatar' => 'nullable|image|max:2048',
            'NIM' => 'nullable|string|max:20',
            'NIP' => 'nullable|string|max:20'
        ]);

        $user = new UserModel();
        $user->username = $validated['username'];
        $user->nama = $validated['nama'];
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
        $roles = RoleModel::all();
        return view('admin.users', compact('user', 'roles'));
    }


    public function update(Request $request, $id)
    {
        $user = UserModel::findOrFail($id);

        $request->validate([
            'username' => 'required|unique:m_user,username,' . $id . ',user_id',
            'nama' => 'required',
            'role_id' => 'required|exists:m_roles,roles_id', // Ubah ke m_roles
            'NIM' => 'nullable|string|max:20',
            'NIP' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = [
            'username' => $request->username,
            'nama' => $request->nama,
            'role_id' => $request->role_id,
            'NIM' => $request->NIM,
            'NIP' => $request->NIP,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = UserModel::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
    public function list()
    {
        $breadcrumb = (object) [
            'title' => 'Manajemen User',
            'list'  => ['Home', 'User']
        ];

        $active_menu = 'users';
        $users = UserModel::with('role')->get();

        return view('admin.users.index', compact('breadcrumb', 'active_menu', 'users'));
    }
}
