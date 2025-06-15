<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('auth.login');
    }

    public function postlogin(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = UserModel::where('username', $credentials['username'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Username atau password salah'
                ], 401);
            }
            return back()->withErrors(['msg' => 'Username atau password salah']);
        }

        Auth::login($user);
        $role = $user->roles_id;

        $redirect = match ($role) {
            1 => '/admin/dashboard',
            2, 3, 4 => '/pelapor/dashboard',
            5 => '/sarpras/dashboard',
            6 => '/teknisi/dashboard',
            default => null,
        };

        if (!$redirect) {
            Auth::logout();
            return response()->json([
                'message' => 'Role tidak dikenali'
            ], 400);
        }

        if ($request->ajax()) {
            return response()->json([
                'redirect' => $redirect
            ]);
        }

        return redirect($redirect);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('login');
    }
}
