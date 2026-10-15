<?php

namespace App\Http\Controllers;

use App\Models\MReservasi;
use App\Models\MJadwalPraktik;
use App\Models\MPembayaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CReservasi extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'pasien') {
            $reservasis = MReservasi::where('pasien_id', $user->pasien->id)
                ->with(['dokter.user', 'jadwalPraktik'])
                ->get();
        } else {
            $reservasis = MReservasi::with(['pasien.user', 'dokter.user', 'jadwalPraktik'])
                ->get();
        }

        $today = Carbon::today();

        $reservasis = $reservasis
            ->sortBy(function ($reservasi) use ($today) {
                $tanggal = Carbon::parse($reservasi->tanggal_reservasi)->startOfDay();

                if ($tanggal->gte($today)) {
                    return [$tanggal->timestamp, 0];
                }

                return [$tanggal->timestamp, 1];
            }, SORT_REGULAR)
            ->values();

        return view('reservasi.index', compact('reservasis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dokter_id'         => 'required|exists:dokters,id',
            'jadwal_praktik_id' => 'required|exists:jadwal_praktiks,id',
            'tanggal_reservasi' => 'required|date|after_or_equal:today',
        ]);

        $jadwal = MJadwalPraktik::findOrFail($request->jadwal_praktik_id);

        if ((int) $jadwal->dokter_id !== (int) $request->dokter_id) {
            return back()->withErrors([
                'jadwal_praktik_id' => 'Jadwal yang dipilih tidak sesuai dengan dokter yang dipilih.',
            ]);
        }

        $jumlahReservasi = MReservasi::where('jadwal_praktik_id', $jadwal->id)
                                     ->where('tanggal_reservasi', $request->tanggal_reservasi)
                                     ->whereIn('status', ['menunggu', 'dikonfirmasi'])
                                     ->count();

        if ($jumlahReservasi >= $jadwal->kuota) {
            return back()->withErrors(['msg' => 'Kuota pendaftaran untuk jadwal dan tanggal ini sudah penuh!']);
        }

        MReservasi::create([
            'pasien_id'         => Auth::user()->pasien->id,
            'dokter_id'         => $request->dokter_id,
            'jadwal_praktik_id' => $request->jadwal_praktik_id,
            'tanggal_reservasi' => $request->tanggal_reservasi,
            'status'            => 'menunggu',
        ]);

        return back()->with('success', 'Reservasi berhasil dibuat! Menunggu konfirmasi admin.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:menunggu,dikonfirmasi,selesai,batal',
        ]);

        $reservasi = MReservasi::with('dokter')->findOrFail($id);
        $reservasi->update(['status' => $request->status]);

        if (in_array($request->status, ['dikonfirmasi', 'selesai'])) {
            $pembayaran = MPembayaran::firstOrNew(['reservasi_id' => $reservasi->id]);

            if ($pembayaran->status !== 'lunas') {
                $pembayaran->biaya_konsultasi = $reservasi->dokter?->tarif_konsultasi ?? 50000;
                $pembayaran->biaya_pengecekan = $reservasi->dokter?->tarif_pengecekan ?? 0;
                $pembayaran->total_obat = $pembayaran->rincianObat()->sum('subtotal');
                $pembayaran->total_bayar = $pembayaran->biaya_konsultasi
                    + $pembayaran->biaya_pengecekan
                    + $pembayaran->total_obat;
                $pembayaran->metode_bayar = $pembayaran->metode_bayar ?: 'Tunai';
                $pembayaran->status = $pembayaran->exists ? $pembayaran->status : 'belum_bayar';
                $pembayaran->save();
            }
        }

        return back()->with('success', 'Status reservasi berhasil diperbarui.');
    }
}