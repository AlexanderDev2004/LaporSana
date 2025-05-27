<?php

namespace App\Http\Controllers;

use App\Models\RoleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Manajemen Roles',
            'list'  => ['Home', 'Roles']
        ];

        $active_menu = 'roles';
        $roles = RoleModel::all();

        return view('admin.roles.index', compact('breadcrumb', 'active_menu', 'roles'));
    }

    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Role',
            'list'  => ['Home', 'Roles', 'Tambah']
        ];

        $active_menu = 'roles';

        return view('admin.roles.create', compact('breadcrumb', 'active_menu'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'roles_nama' => 'required|unique:m_roles,roles_nama|string|max:50',
            'roles_kode' => 'required|unique:m_roles,roles_kode|string|max:10',
            'roles_deskripsi' => 'nullable|string|max:255'
        ]);

        try {
            RoleModel::create([
                'roles_nama' => $request->roles_nama,
                'roles_kode' => $request->roles_kode,
                'roles_deskripsi' => $request->roles_deskripsi
            ]);

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role berhasil ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error creating role: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal menambahkan role')
                ->withInput();
        }
    }

    public function edit(RoleModel $role)
    {
        $breadcrumb = (object) [
            'title' => 'Edit Role',
            'list'  => ['Home', 'Roles', 'Edit']
        ];

        $active_menu = 'roles';

        return view('admin.roles.edit', compact('breadcrumb', 'active_menu', 'role'));
    }

    public function update(Request $request, RoleModel $role)
    {
        $request->validate([
            'roles_nama' => 'required|unique:m_roles,roles_nama,' . $role->roles_id . ',roles_id|string|max:50',
            'roles_kode' => 'required|unique:m_roles,roles_kode,' . $role->roles_id . ',roles_id|string|max:10',
            'roles_deskripsi' => 'nullable|string|max:255'
        ]);

        try {
            $role->update([
                'roles_nama' => $request->roles_nama,
                'roles_kode' => $request->roles_kode,
                'roles_deskripsi' => $request->roles_deskripsi
            ]);

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error updating role: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal memperbarui role')
                ->withInput();
        }
    }

    public function destroy(RoleModel $role)
    {
        try {
            $role->delete();
            return redirect()->route('admin.roles.index')
                ->with('success', 'Role berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting role: ' . $e->getMessage());
            return redirect()->route('admin.roles.index')
                ->with('error', 'Gagal menghapus role');
        }
    }

    public function show(RoleModel $role)
    {
        $breadcrumb = (object) [
            'title' => 'Detail Role',
            'list'  => ['Home', 'Roles', 'Detail']
        ];

        $active_menu = 'roles';

        return view('admin.roles.show', compact('breadcrumb', 'active_menu', 'role'));
    }
}
