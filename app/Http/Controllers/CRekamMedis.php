<?php

namespace App\Http\Controllers;

use App\Models\MRekamMedis;
use App\Models\MReservasi;
use App\Models\MObat;
use App\Models\MPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CRekamMedis extends Controller
{
    public function storeLampiran(Request $request)
    {
        $request->validate([
            'reservasi_id' => ['required', 'exists:reservasis,id'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'lampiran' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5000'],
        ]);

        $reservasi = MReservasi::with('pasien')->findOrFail($request->reservasi_id);

        if ($reservasi->pasien_id !== auth()->user()->pasien?->id) {
            abort(403, 'Anda hanya dapat melampirkan file pada reservasi milik Anda sendiri.');
        }

        $rekamMedis = MRekamMedis::firstOrCreate(
            ['reservasi_id' => $reservasi->id],
            [
                'keluhan' => 'Lampiran pasien - menunggu input dokter',
                'diagnosa' => 'Lampiran pasien - menunggu input dokter',
                'catatan' => 'File ini diunggah pasien untuk keperluan lampiran rekam medis.',
            ]
        );

        $files = $request->file('lampiran');
        if (!is_array($files)) {
            $files = [$files];
        }

        foreach ($files as $file) {
            $path = $file->store('lampiran_rekam_medis', 'public');
            $rekamMedis->lampiran()->create([
                'file_path' => $path,
                'keterangan' => $request->keterangan ?: 'Lampiran rekam medis pasien',
            ]);
        }

        return back()->with('success', 'Lampiran rekam medis berhasil ditambahkan. Dokter dan admin dapat melihat file yang Anda unggah.');
    }

    public function store(Request $request)
    {
        $obats = collect($request->input('obats', []))
            ->filter(fn ($obat) => !empty($obat['id']))
            ->values()
            ->all();
        $request->merge(['obats' => $obats]);

        $request->validate([
            'reservasi_id' => 'required|exists:reservasis,id',
            'keluhan'      => 'required|string',
            'diagnosa'     => 'required|string',
            'catatan'      => 'nullable|string',
            'obats'        => 'nullable|array',
            'obats.*.dosis' => 'required|string',
            'obats.*.jumlah'=> 'required|integer|min:1',
            'obats.*.id'    => 'required|distinct|exists:obats,id',
            'lampiran'     => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5000',
        ]);

        DB::transaction(function () use ($request, $obats) {
            $reservasi = MReservasi::with('dokter')->findOrFail($request->reservasi_id);
            $rekamMedis = MRekamMedis::firstOrNew(['reservasi_id' => $request->reservasi_id]);

            if ($rekamMedis->exists && $rekamMedis->status_resep !== 'menunggu') {
                abort(403, 'Resep yang sudah disiapkan apoteker tidak dapat diubah.');
            }

            $pembayaran = MPembayaran::where('reservasi_id', $request->reservasi_id)->first();
            if ($pembayaran?->status === 'lunas') {
                abort(403, 'Rekam medis tidak dapat diubah setelah pembayaran lunas.');
            }

            if ($rekamMedis->exists) {
                $resepLama = $rekamMedis->obat()->get();
                foreach ($resepLama as $obatLama) {
                    MObat::whereKey($obatLama->id)->lockForUpdate()->increment('stok', $obatLama->pivot->jumlah);
                }
                $rekamMedis->obat()->detach();
            }

            $rekamMedis->fill([
                'keluhan'  => $request->keluhan,
                'diagnosa' => $request->diagnosa,
                'catatan'  => $request->catatan,
                'status_resep' => 'menunggu',
            ]);
            $rekamMedis->save();

            foreach ($obats as $item) {
                $obat = MObat::whereKey($item['id'])->lockForUpdate()->firstOrFail();

                if ($obat->stok < $item['jumlah']) {
                    throw new \Exception("Stok obat {$obat->nama_obat} tidak mencukupi!");
                }

                $rekamMedis->obat()->attach($obat->id, [
                    'dosis'  => $item['dosis'],
                    'jumlah' => $item['jumlah'],
                ]);

                $obat->decrement('stok', $item['jumlah']);
            }

            if ($request->hasFile('lampiran')) {
                $filePath = $request->file('lampiran')->store('lampiran_rekam_medis', 'public');
                $rekamMedis->lampiran()->delete();
                $rekamMedis->lampiran()->create([
                    'file_path'  => $filePath,
                    'keterangan' => 'Lampiran Rekam Medis / Hasil Lab',
                ]);
            }

            $reservasi->update(['status' => 'selesai']);

            $biayaKonsultasi = $reservasi->dokter?->tarif_konsultasi ?? 50000;
            $biayaPengecekan = $reservasi->dokter?->tarif_pengecekan ?? 0;
            $pembayaran ??= new MPembayaran(['reservasi_id' => $reservasi->id]);
            $pembayaran->fill([
                'biaya_konsultasi' => $biayaKonsultasi,
                'biaya_pengecekan' => $biayaPengecekan,
                'total_obat' => 0,
                'total_bayar' => $biayaKonsultasi + $biayaPengecekan,
                'metode_bayar' => $pembayaran->metode_bayar ?: 'Tunai',
                'status' => $pembayaran->exists ? $pembayaran->status : 'belum_bayar',
            ]);
            $pembayaran->save();
        });

        return back()->with('success', 'Rekam Medis & Resep Obat berhasil disimpan!');
    }
}