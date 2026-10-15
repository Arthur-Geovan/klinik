<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MPasien; // Menggunakan model MPasien sesuai project Anda

class ApiController extends Controller
{
    public function pasienApiKey(Request $request)
    {
        // 1. Ambil header 'x-api-key' dari request
        $apiKey = $request->header('x-api-key');
        $validApiKey = '123456';

        // 2. Cek validasi API Key
        if (!$apiKey || $apiKey !== $validApiKey) {
            return response()->json([
                'status'  => 'error',
                'message' => 'API Key tidak valid atau tidak ditemukan'
            ], 401, [], JSON_PRETTY_PRINT);
        }

        // 3. Ambil data pasien
        $data = MPasien::get();

        // 4. Return langsung array data (sesuai contoh gambar)
        return response()->json($data, 200, [], JSON_PRETTY_PRINT);
    }
}