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
            return back()->withErrors(['msg' => 'Username atau password salah']);
        }

        Auth::login($user);

        // Ambil role dan arahkan ke dashboard masing-masing
        $role = $user->roles_id;
        switch ($role) {
            case 1:
                return redirect('/admin/dashboard');
            case 2:
                return redirect('/mahasiswa/dashboard');
            case 3:
                return redirect('/dosen/dashboard');
            case 4:
                return redirect('/tendik/dashboard');
            case 5:
                return redirect('/sarana/dashboard');
            case 6:
                return redirect('/teknisi/dashboard');
            default:
                Auth::logout();
                return back()->withErrors(['msg' => 'Role tidak dikenali']);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('login');
    }
}
