@extends('layout.app')

@section('content')
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Invoice Pembayaran</h5>
        <a href="{{ route('pembayaran.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h6 class="text-muted">Pasien</h6>
                <p class="mb-1"><strong>{{ $reservasi->pasien->user->name ?? '-' }}</strong></p>
                <p class="mb-0">{{ $reservasi->pasien->no_rm ?? '-' }}</p>
            </div>
            <div class="col-md-6 text-md-end">
                <h6 class="text-muted">Dokter</h6>
                <p class="mb-1"><strong>{{ $reservasi->dokter->user->name ?? '-' }}</strong></p>
                <p class="mb-0">{{ $reservasi->dokter->poli->nama_poli ?? '-' }}</p>
            </div>
        </div>

        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Item</th>
                    <th class="text-end">Jumlah</th>
                    <th class="text-end">Harga Satuan</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Biaya Konsultasi</td>
                    <td class="text-end">1</td>
                    <td class="text-end">Rp {{ number_format($pembayaran->biaya_konsultasi, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($pembayaran->biaya_konsultasi, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Biaya Pengecekan</td>
                    <td class="text-end">1</td>
                    <td class="text-end">Rp {{ number_format($pembayaran->biaya_pengecekan, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($pembayaran->biaya_pengecekan, 0, ',', '.') }}</td>
                </tr>
                @foreach($pembayaran->rincianObat as $item)
                    <tr>
                        <td>{{ $item->nama_obat }}</td>
                        <td class="text-end">{{ $item->jumlah }}</td>
                        <td class="text-end">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="3"><strong>Total</strong></td>
                    <td class="text-end"><strong>Rp {{ number_format($pembayaran->total_bayar, 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>

        @if($pembayaran->bukti_bayar)
            <div class="mt-4 border rounded p-3">
                <h6 class="fw-bold mb-3">Bukti Pembayaran</h6>
                <img src="{{ asset('storage/' . $pembayaran->bukti_bayar) }}" alt="Bukti pembayaran" class="img-fluid rounded border shadow-sm" style="max-height: 420px;">
            </div>
        @else
            <div class="mt-4 border rounded p-3 text-muted text-center">
                Belum ada bukti pembayaran yang diupload.
            </div>
        @endif

        <div class="mt-4 d-flex justify-content-between align-items-center">
            <span class="badge bg-{{ $pembayaran->status == 'lunas' ? 'success' : 'warning' }} text-dark">
                {{ ucfirst(str_replace('_', ' ', $pembayaran->status)) }}
            </span>
            @if($pembayaran->status != 'lunas')
                <form action="{{ route('pembayaran.konfirmasi', $pembayaran->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success">Konfirmasi Lunas</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
