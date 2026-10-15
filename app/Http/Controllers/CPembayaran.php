<?php

namespace App\Http\Controllers;

use App\Models\MPembayaran;
use App\Models\MReservasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CPembayaran extends Controller
{
    public function index()
    {
        $pembayarans = MPembayaran::with([
            'reservasi.pasien.user',
            'reservasi.dokter.user',
            'reservasi.rekamMedis',
            'rincianObat',
        ])
            ->orderByDesc('updated_at')
            ->get();

        return view('admin.pembayaran.index', compact('pembayarans'));
    }

    public function generateInvoice($reservasi_id)
    {
        $reservasi = MReservasi::with([
            'rekamMedis.obat',
            'pasien.user',
            'dokter.user',
            'dokter.poli',
            'pembayaran.rincianObat',
        ])->findOrFail($reservasi_id);

        abort_unless($reservasi->pembayaran, 404);

        return view('admin.pembayaran.invoice', [
            'reservasi' => $reservasi,
            'pembayaran' => $reservasi->pembayaran,
        ]);
    }

    public function updatePayment(Request $request, $id)
    {
        $request->validate([
            'metode_bayar' => 'required|in:Tunai,Transfer',
            'bukti_bayar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'catatan_admin' => 'nullable|string|max:500',
        ]);

        $pembayaran = MPembayaran::findOrFail($id);
        abort_if($pembayaran->status === 'lunas', 403, 'Pembayaran yang sudah lunas tidak dapat diubah.');

        if ($request->hasFile('bukti_bayar')) {
            if ($pembayaran->bukti_bayar && Storage::disk('public')->exists($pembayaran->bukti_bayar)) {
                Storage::disk('public')->delete($pembayaran->bukti_bayar);
            }

            $path = $request->file('bukti_bayar')->store('bukti-pembayaran', 'public');
            $pembayaran->bukti_bayar = $path;
        }

        $pembayaran->metode_bayar = $request->metode_bayar;
        $pembayaran->catatan_admin = $request->catatan_admin;
        $pembayaran->save();

        return back()->with('success', 'Data pembayaran berhasil diperbarui.');
    }

    public function konfirmasiBayar($id)
    {
        DB::transaction(function () use ($id) {
            $pembayaran = MPembayaran::whereKey($id)->lockForUpdate()->firstOrFail();

            if ($pembayaran->status === 'lunas') {
                throw ValidationException::withMessages([
                    'pembayaran' => 'Pembayaran ini sudah dikonfirmasi lunas.',
                ]);
            }

            $reservasi = MReservasi::with('rekamMedis')->findOrFail($pembayaran->reservasi_id);
            $rekamMedis = $reservasi->rekamMedis;
            if (!$rekamMedis || $reservasi->status !== 'selesai') {
                throw ValidationException::withMessages([
                    'pembayaran' => 'Pembayaran hanya dapat dikonfirmasi setelah konsultasi dan rekam medis selesai.',
                ]);
            }

            if ($rekamMedis && $rekamMedis->obat()->exists() && $rekamMedis->status_resep !== 'siap') {
                throw ValidationException::withMessages([
                    'pembayaran' => 'Resep harus dikonfirmasi Siap oleh apoteker sebelum pembayaran.',
                ]);
            }

            $pembayaran->status = 'lunas';
            $pembayaran->metode_bayar = $pembayaran->metode_bayar ?: 'Tunai';
            $pembayaran->dikonfirmasi_oleh = auth()->id();
            $pembayaran->dikonfirmasi_pada = now();
            $pembayaran->nomor_kuitansi = 'KHB-' . now()->format('Ymd') . '-' . str_pad((string) $pembayaran->id, 6, '0', STR_PAD_LEFT);
            $pembayaran->save();

            $pembayaran->load([
                'reservasi.pasien.user',
                'reservasi.dokter.user',
                'reservasi.dokter.poli',
                'rincianObat',
                'dikonfirmasiOleh',
            ]);

            $path = 'receipts/' . $pembayaran->nomor_kuitansi . '.html';
            $html = view('admin.pembayaran.receipt', compact('pembayaran'))->render();
            if (!Storage::disk('local')->put($path, $html)) {
                throw new \RuntimeException('File kuitansi tidak dapat disimpan.');
            }

            $pembayaran->kuitansi_path = $path;
            $pembayaran->save();
        });

        return back()->with('success', 'Pembayaran berhasil dikonfirmasi LUNAS!');
    }

    public function kuitansi($id)
    {
        $pembayaran = MPembayaran::with('reservasi.pasien')->findOrFail($id);

        if (auth()->user()->role === 'pasien'
            && $pembayaran->reservasi?->pasien?->user_id !== auth()->id()) {
            abort(403);
        }

        abort_unless($pembayaran->status === 'lunas' && $pembayaran->kuitansi_path, 404);
        abort_unless(Storage::disk('local')->exists($pembayaran->kuitansi_path), 404);

        return Storage::disk('local')->response(
            $pembayaran->kuitansi_path,
            basename($pembayaran->kuitansi_path),
            ['Content-Type' => 'text/html; charset=UTF-8'],
            'inline'
        );
    }
}