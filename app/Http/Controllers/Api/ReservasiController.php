<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MJadwalPraktik;
use App\Models\MReservasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReservasiController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'pasien' || !$user->pasien) {
            return response()->json([
                'status' => false,
                'message' => 'Hanya akun pasien yang dapat membuat reservasi.',
                'data' => null,
                'errors' => null,
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'dokter_id' => ['required', 'exists:dokters,id'],
            'jadwal_praktik_id' => ['required', 'exists:jadwal_praktiks,id'],
            'tanggal_reservasi' => ['required', 'date', 'after_or_equal:today'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi data reservasi gagal.',
                'data' => null,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $jadwal = MJadwalPraktik::findOrFail($data['jadwal_praktik_id']);

        if ((int) $jadwal->dokter_id !== (int) $data['dokter_id']) {
            return response()->json([
                'status' => false,
                'message' => 'Jadwal praktik tidak sesuai dengan dokter.',
                'data' => null,
                'errors' => ['jadwal_praktik_id' => ['Jadwal tersebut bukan milik dokter yang dipilih.']],
            ], 422);
        }

        $jumlahReservasi = MReservasi::where('jadwal_praktik_id', $jadwal->id)
            ->where('tanggal_reservasi', $data['tanggal_reservasi'])
            ->whereIn('status', ['menunggu', 'dikonfirmasi'])
            ->count();

        if ($jumlahReservasi >= $jadwal->kuota) {
            return response()->json([
                'status' => false,
                'message' => 'Kuota pendaftaran untuk jadwal dan tanggal ini sudah penuh.',
                'data' => null,
                'errors' => ['jadwal_praktik_id' => ['Kuota sudah penuh.']],
            ], 422);
        }

        $reservasi = MReservasi::create([
            'pasien_id' => $user->pasien->id,
            'dokter_id' => $data['dokter_id'],
            'jadwal_praktik_id' => $data['jadwal_praktik_id'],
            'tanggal_reservasi' => $data['tanggal_reservasi'],
            'status' => 'menunggu',
        ])->load(['dokter.user', 'jadwalPraktik']);

        return response()->json([
            'status' => true,
            'message' => 'Reservasi berhasil dibuat.',
            'data' => $reservasi,
            'errors' => null,
        ], 201);
    }
}