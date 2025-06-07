<?php

namespace App\Http\Controllers;

use App\Models\RoleModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
{
    // Ambil user yang sedang login dan relasi role-nya
    $user = auth()->user()->load('role');

    // Buat breadcrumb
    $breadcrumb = (object) [
        'title' => 'Profil Saya',
        'list'  => ['Home', 'Profil']
    ];

    // Aktifkan menu sidebar
    $active_menu = 'profile';

    // Tampilkan view
    return view('admin.profile.show', compact('user', 'breadcrumb', 'active_menu'));
}

public function edit()
{
    $user = auth()->user();

    $breadcrumb = (object) [
        'title' => 'Edit Profil Saya',
        'list'  => ['Home', 'Profil', 'Edit']
    ];

    $active_menu = 'profile';

    return view('admin.profile.edit', compact('user', 'active_menu', 'breadcrumb'));
}

public function update(Request $request)
{
    $user = auth()->user();

    $validated = $request->validate([
        // Username tidak diubah sendiri, jadi skip validasi unique username
        'name' => 'required|string|max:100',
        'NIM' => 'nullable|string|max:20',
        'NIP' => 'nullable|string|max:20',
        'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'password' => 'nullable|string|min:6',
    ]);

    try {
        $data = [
            'name' => $validated['name'],
            'NIM' => $validated['NIM'] ?? null,
            'NIP' => $validated['NIP'] ?? null,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::exists('public/' . $user->avatar)) {
                Storage::delete('public/' . $user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()->route('admin.profile.show')->with('success', 'Profil berhasil diperbarui.');
    } catch (\Exception $e) {
        Log::error('Gagal update profil: '.$e->getMessage());
        return back()->withErrors(['error' => 'Gagal memperbarui profil'])->withInput();
    }
}

};