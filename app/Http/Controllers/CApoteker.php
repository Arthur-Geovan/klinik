<?php

namespace App\Http\Controllers;

use App\Models\MDetailPembayaran;
use App\Models\MObat;
use App\Models\MPembayaran;
use App\Models\MRekamMedis;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CApoteker extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->role === 'apoteker', 403);

        $resep = MRekamMedis::with([
            'reservasi.pasien.user',
            'reservasi.dokter.user',
            'obat',
            'reservasi.pembayaran',
        ])->whereHas('obat')->latest()->get();

        return view('apoteker.resep.index', compact('resep'));
    }

    public function konfirmasiSiap($id)
    {
        abort_unless(auth()->user()->role === 'apoteker', 403);

        DB::transaction(function () use ($id) {
            $rekamMedis = MRekamMedis::whereKey($id)->lockForUpdate()->firstOrFail();

            if ($rekamMedis->status_resep !== 'menunggu') {
                throw ValidationException::withMessages([
                    'resep' => 'Resep ini tidak lagi berstatus Menunggu.',
                ]);
            }

            $items = $rekamMedis->obat()->get();
            if ($items->isEmpty()) {
                throw ValidationException::withMessages([
                    'resep' => 'Resep ini tidak memiliki rincian obat.',
                ]);
            }

            $reservasi = $rekamMedis->reservasi()->with('dokter')->firstOrFail();
            $pembayaran = MPembayaran::where('reservasi_id', $reservasi->id)
                ->lockForUpdate()
                ->first();

            if ($pembayaran?->status === 'lunas') {
                throw ValidationException::withMessages([
                    'resep' => 'Pembayaran sudah lunas sehingga rincian resep tidak dapat diubah.',
                ]);
            }

            $pembayaran ??= new MPembayaran(['reservasi_id' => $reservasi->id]);
            $pembayaran->biaya_konsultasi = $reservasi->dokter?->tarif_konsultasi ?? 50000;
            $pembayaran->biaya_pengecekan = $reservasi->dokter?->tarif_pengecekan ?? 0;
            $pembayaran->total_obat = 0;
            $pembayaran->status = 'belum_bayar';
            $pembayaran->metode_bayar = $pembayaran->metode_bayar ?: 'Tunai';
            $pembayaran->save();

            $pembayaran->rincianObat()->delete();
            $totalObat = 0;

            foreach ($items as $item) {
                $obat = MObat::whereKey($item->id)->lockForUpdate()->firstOrFail();
                $subtotal = $obat->harga * $item->pivot->jumlah;
                $totalObat += $subtotal;

                MDetailPembayaran::create([
                    'pembayaran_id' => $pembayaran->id,
                    'obat_id' => $obat->id,
                    'nama_obat' => $obat->nama_obat,
                    'jumlah' => $item->pivot->jumlah,
                    'harga_satuan' => $obat->harga,
                    'subtotal' => $subtotal,
                ]);
            }

            $pembayaran->total_obat = $totalObat;
            $pembayaran->total_bayar = $pembayaran->biaya_konsultasi
                + $pembayaran->biaya_pengecekan
                + $totalObat;
            $pembayaran->save();

            $rekamMedis->update([
                'status_resep' => 'siap',
                'disiapkan_oleh' => auth()->id(),
                'disiapkan_pada' => now(),
            ]);
        });

        return back()->with('success', 'Resep dikonfirmasi siap. Tagihan pasien telah diperbarui.');
    }

    public function tandaiDiserahkan($id)
    {
        abort_unless(auth()->user()->role === 'apoteker', 403);

        DB::transaction(function () use ($id) {
            $rekamMedis = MRekamMedis::whereKey($id)->lockForUpdate()->firstOrFail();

            if ($rekamMedis->status_resep !== 'siap') {
                throw ValidationException::withMessages([
                    'resep' => 'Hanya resep berstatus Siap yang dapat diserahkan.',
                ]);
            }

            $statusPembayaran = MPembayaran::where('reservasi_id', $rekamMedis->reservasi_id)
                ->lockForUpdate()
                ->value('status');

            if ($statusPembayaran !== 'lunas') {
                throw ValidationException::withMessages([
                    'resep' => 'Resep baru dapat diserahkan setelah pembayaran Lunas.',
                ]);
            }

            $rekamMedis->update([
                'status_resep' => 'diserahkan',
                'diserahkan_oleh' => auth()->id(),
                'diserahkan_pada' => now(),
            ]);
        });

        return back()->with('success', 'Resep ditandai sudah diserahkan.');
    }
}
