@extends('layout.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="h3 mb-1">Resep Masuk</h2>
        <p class="text-muted mb-0">Daftar obat yang diresepkan dokter untuk disiapkan.</p>
    </div>
    <a href="{{ route('obat.index') }}" class="btn btn-outline-primary"><i class="fa-solid fa-pills me-2"></i>Data Obat</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Pasien / No. RM</th>
                    <th>Dokter</th>
                    <th>Tanggal</th>
                    <th>Daftar Obat</th>
                    <th>Status</th>
                    <th class="text-end pe-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($resep as $rekamMedis)
                    @php
                        $reservasi = $rekamMedis->reservasi;
                        $pembayaran = $reservasi?->pembayaran;
                    @endphp
                    <tr>
                        <td class="ps-3">
                            <div class="fw-semibold">{{ $reservasi?->pasien?->user?->name ?? '-' }}</div>
                            <small class="text-muted">{{ $reservasi?->pasien?->no_rm ?? '-' }}</small>
                        </td>
                        <td>{{ $reservasi?->dokter?->user?->name ?? '-' }}</td>
                        <td>{{ $reservasi?->tanggal_reservasi ?? '-' }}</td>
                        <td>
                            @foreach($rekamMedis->obat as $obat)
                                <div class="mb-1">
                                    <span class="fw-semibold">{{ $obat->nama_obat }}</span>
                                    <span class="text-muted">× {{ $obat->pivot->jumlah }} {{ $obat->satuan }}</span>
                                    <div class="small text-muted">Aturan pakai: {{ $obat->pivot->dosis ?: '-' }}</div>
                                </div>
                            @endforeach
                        </td>
                        <td>
                            @php($badge = ['menunggu' => 'warning', 'siap' => 'info', 'diserahkan' => 'success'][$rekamMedis->status_resep] ?? 'secondary')
                            <span class="badge text-bg-{{ $badge }}">{{ ucfirst($rekamMedis->status_resep) }}</span>
                        </td>
                        <td class="text-end pe-3">
                            @if($rekamMedis->status_resep === 'menunggu')
                                <form action="{{ route('apoteker.resep.siap', $rekamMedis->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-primary">Konfirmasi Obat Siap</button>
                                </form>
                            @elseif($rekamMedis->status_resep === 'siap' && $pembayaran?->status === 'lunas')
                                <form action="{{ route('apoteker.resep.diserahkan', $rekamMedis->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success">Tandai Diserahkan</button>
                                </form>
                            @elseif($rekamMedis->status_resep === 'siap')
                                <span class="small text-muted">Menunggu pembayaran lunas</span>
                            @else
                                <span class="small text-muted">Selesai</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-5">Belum ada resep masuk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
