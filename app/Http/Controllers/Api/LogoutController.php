<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class LogoutController extends Controller
{
    public function logout()
    {
        try {
            
            $token = JWTAuth::getToken();

            if (!$token) {
                return response()->json([
                    'status' => false,
                    'message' => 'Token tidak ditemukan.'
                ], 400);
            }

            // Invalidasi token agar tidak bisa digunakan lagi
            JWTAuth::invalidate($token);

            return response()->json([
                'status' => true,
                'message' => 'Logout berhasil, token telah dihapus.'
            ], 200);

        } catch (JWTException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal logout: token tidak valid atau sudah kedaluwarsa.'
            ], 400);
        }
    }
}
