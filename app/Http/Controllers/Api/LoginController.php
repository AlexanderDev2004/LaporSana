<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $credentials = $request->only('username', 'password');

        try {
            if (!$token = Auth::guard('api')->attempt($credentials)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Username atau password salah.',
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal membuat token.',
            ], 500);
        }

        $user = Auth::guard('api')->user();

        return response()->json([
            'status' => true,
            'message' => 'Login berhasil.',
            'token' => $token,
            'token_type' => 'bearer',
            'user' => $user,
        ], 200);
    }

    public function logout()
    {
        try {
            Auth::guard('api')->logout();

            return response()->json([
                'status' => true,
                'message' => 'Berhasil logout.',
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Token tidak valid atau sudah logout.',
            ], 400);
        }
    }

    public function profile()
    {
        return response()->json([
            'status' => true,
            'user' => Auth::guard('api')->user(),
        ]);
    }
}
