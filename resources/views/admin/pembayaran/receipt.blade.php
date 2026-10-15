<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kuitansi {{ $pembayaran->nomor_kuitansi }}</title>
    <style>
        body { color: #1f2937; font: 14px/1.5 Arial, sans-serif; margin: 0; padding: 32px; }
        .receipt { border: 1px solid #cbd5e1; margin: 0 auto; max-width: 760px; padding: 32px; }
        h1 { color: #17324d; font-size: 22px; margin: 0; }
        .muted { color: #64748b; }
        .heading { align-items: flex-start; border-bottom: 2px solid #2aa79b; display: flex; justify-content: space-between; padding-bottom: 18px; }
        .columns { display: grid; gap: 20px; grid-template-columns: 1fr 1fr; margin: 24px 0; }
        table { border-collapse: collapse; margin-top: 18px; width: 100%; }
        th, td { border-bottom: 1px solid #e2e8f0; padding: 9px 6px; text-align: left; }
        th { background: #f1f5f9; }
        .number, .money { text-align: right; }
        .total { font-size: 17px; font-weight: 700; }
        .print { background: #236b8e; border: 0; color: white; cursor: pointer; margin: 20px auto; padding: 10px 16px; }
        @media print { body { padding: 0; } .receipt { border: 0; max-width: none; padding: 0; } .print { display: none; } }
        @media (max-width: 600px) { body { padding: 12px; } .receipt { padding: 18px; } .columns { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <button class="print" onclick="window.print()">Cetak / Simpan sebagai PDF</button>
    <main class="receipt">
        <header class="heading">
            <div><h1>Klinik Harapan Bangsa</h1><div class="muted">Ngawi Timur</div></div>
            <div class="number"><strong>KUITANSI</strong><br>{{ $pembayaran->nomor_kuitansi }}</div>
        </header>
        <section class="columns">
            <div><strong>Pasien</strong><br>{{ $pembayaran->reservasi?->pasien?->user?->name ?? '-' }}<br>No. RM: {{ $pembayaran->reservasi?->pasien?->no_rm ?? '-' }}</div>
            <div><strong>Dokter</strong><br>{{ $pembayaran->reservasi?->dokter?->user?->name ?? '-' }}<br>{{ $pembayaran->reservasi?->dokter?->poli?->nama_poli ?? '-' }}</div>
            <div><strong>Tanggal kunjungan</strong><br>{{ $pembayaran->reservasi?->tanggal_reservasi ?? '-' }}</div>
            <div><strong>Tanggal pembayaran</strong><br>{{ $pembayaran->dikonfirmasi_pada?->format('d-m-Y H:i') ?? '-' }}</div>
        </section>
        <table>
            <thead><tr><th>Rincian</th><th class="number">Jumlah</th><th class="money">Harga satuan</th><th class="money">Subtotal</th></tr></thead>
            <tbody>
                <tr><td>Biaya konsultasi</td><td class="number">1</td><td class="money">Rp {{ number_format($pembayaran->biaya_konsultasi, 0, ',', '.') }}</td><td class="money">Rp {{ number_format($pembayaran->biaya_konsultasi, 0, ',', '.') }}</td></tr>
                <tr><td>Biaya pengecekan</td><td class="number">1</td><td class="money">Rp {{ number_format($pembayaran->biaya_pengecekan, 0, ',', '.') }}</td><td class="money">Rp {{ number_format($pembayaran->biaya_pengecekan, 0, ',', '.') }}</td></tr>
                @foreach($pembayaran->rincianObat as $item)
                    <tr><td>{{ $item->nama_obat }}</td><td class="number">{{ $item->jumlah }}</td><td class="money">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td><td class="money">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td></tr>
                @endforeach
                <tr class="total"><td colspan="3">Total dibayar</td><td class="money">Rp {{ number_format($pembayaran->total_bayar, 0, ',', '.') }}</td></tr>
            </tbody>
        </table>
        <section class="columns">
            <div><strong>Metode pembayaran</strong><br>{{ $pembayaran->metode_bayar ?? '-' }}</div>
            <div><strong>Petugas</strong><br>{{ $pembayaran->dikonfirmasiOleh?->name ?? '-' }}</div>
        </section>
        <p class="muted">Kuitansi ini diterbitkan oleh sistem Klinik Harapan Bangsa Ngawi Timur.</p>
    </main>
</body>
</html>
