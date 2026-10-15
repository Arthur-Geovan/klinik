<?php

namespace App\Http\Controllers;

use App\Models\MReservasi;
use App\Models\MPembayaran;
use App\Models\MObat;
use App\Models\MRekamMedis;
use Illuminate\Support\Facades\Auth;

class CDashboard extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $data = [
                'reservasi_hari_ini' => MReservasi::whereDate('tanggal_reservasi', now())->count(),
                'total_pendapatan'   => MPembayaran::where('status', 'lunas')->sum('total_bayar'),
                'stok_menipis'       => MObat::where('stok', '<=', 10)->get(),
                'lampiran_rekam_medis' => MRekamMedis::with([
                    'reservasi.pasien.user',
                    'reservasi.dokter.user',
                    'lampiran',
                ])->whereHas('lampiran')->latest()->get(),
            ];
            return view('dashboard.admin', $data);
        } elseif ($user->role === 'dokter') {
            $dokterId = $user->dokter->id ?? null;

            $data = [
                'pasien_hari_ini' => MReservasi::with(['pasien.user'])
                    ->where('dokter_id', $dokterId)
                    ->whereNotIn('status', ['batal'])
                    ->whereDate('tanggal_reservasi', '>=', now()->toDateString())
                    ->orderBy('tanggal_reservasi')
                    ->orderBy('created_at')
                    ->get(),
                'obats' => MObat::where('stok', '>', 0)->orderBy('nama_obat')->get(),
            ];

            return view('dashboard.dokter', $data);
        } elseif ($user->role === 'apoteker') {
            $data = [
                'resep_menunggu' => MRekamMedis::where('status_resep', 'menunggu')
                    ->whereHas('obat')
                    ->count(),
                'stok_menipis' => MObat::where('stok', '<=', 10)
                    ->orderBy('stok')
                    ->get(),
            ];

            return view('dashboard.apoteker', $data);
        } else {
            $pasienId = $user->pasien->id ?? null;
            $data = [
                'pasien'            => $user->pasien,
                'riwayat_reservasi' => MReservasi::with([
                    'dokter.user',
                    'dokter.poli',
                    'rekamMedis.lampiran',
                    'pembayaran',
                ])->where('pasien_id', $pasienId)->latest()->get(),
            ];
            return view('dashboard.pasien', $data);
        }
    }
}