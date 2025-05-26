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
        $breadcrumbs = (object) [
            'title' => 'Selamat Datang',
            'list'  => ['Home', 'Welcome']
        ];

        $active_menu = 'dashboard';
        return view('admin.dashboard', ['breadcrumb' => $breadcrumbs, 'active_menu' => $active_menu]);
    }

    public function create()
    {
        $roles = RoleModel::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|unique:users,username',
            'nama' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,roles_id',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $user = new UserModel();
        $user->username = $validated['username'];
        $user->nama = $validated['nama'];
        $user->password = bcrypt($validated['password']);
        $user->role_id = $validated['role_id'];

        if ($request->hasFile('avatar')) {
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        $user->save();

        return redirect()->route('admin.dashboard')->with('success', 'User berhasil ditambahkan.');
    }


    public function edit(UserModel $user)
    {
        $roles = RoleModel::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }


    public function update(Request $request, $id)
    {
        $user = UserModel::findOrFail($id);

        $request->validate([
            'username' => 'required|unique:m_user,username,' . $id . ',user_id',
            'roles_id' => 'required',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);

        $user->update([
            'username' => $request->username,
            'roles_id' => $request->roles_id,
            'nama' => $request->nama,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
            'NIM' => optional($request)->NIM,
            'NIP' => optional($request)->NIP,
            'avatar' => $request->hasFile('avatar') ? $request->file('avatar')->store('avatars', 'public') : $user->avatar,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = UserModel::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
