<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MDokter;
use Illuminate\Http\JsonResponse;

class DokterApiController extends Controller
{
    public function index(): JsonResponse
    {
        $dokters = MDokter::query()
            ->select([
                'id',
                'user_id',
                'poli_id',
                'no_str',
                'spesialisasi',
                'foto',
                'tarif_konsultasi',
                'tarif_pengecekan',
                'created_at',
                'updated_at',
            ])
            ->with([
                'user:id,name',
                'poli:id,nama_poli',
                'jadwalPraktik:id,dokter_id,hari,jam_mulai,jam_selesai,kuota',
            ])
            ->orderBy('id')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Data dokter berhasil diambil.',
            'data' => $dokters,
        ]);
    }
}