<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return view('auth.login');
        }
        return redirect('/');
    }

    public function postlogin(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = UserModel::where('username', $credentials['username'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Username atau password salah'
            ], 401);
        }

        $role = $user->roles_id;
        $redirects = [
            1 => ['url' => '/admin/dashboard', 'role' => 'Admin'],
            2 => ['url' => '/mahasiswa/dashboard', 'role' => 'Mahasiswa'],
            3 => ['url' => '/dosen/dashboard', 'role' => 'Dosen'],
            4 => ['url' => '/tendik/dashboard', 'role' => 'Tendik'],
            5 => ['url' => '/sarana/dashboard', 'role' => 'Sarana Prasarana'],
            6 => ['url' => '/teknis/dashboard', 'role' => 'Teknis'],
        ];

        if (!array_key_exists($role, $redirects)) {
            return response()->json([
                'status' => false,
                'message' => 'Role tidak valid'
            ], 403);
        }

        Auth::login($user);
        return response()->json([
            'status' => true,
            'message' => 'Login Berhasil sebagai ' . $redirects[$role]['role'],
            'redirect' => url($redirects[$role]['url']),
            'avatar' => $user->avatar
        ]);
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('login');
    }
}
