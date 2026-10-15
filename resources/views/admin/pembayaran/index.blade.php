@extends('layout.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Kelola Pembayaran</h3>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 40%;">Pasien</th>
                    <th class="text-center" style="width: 18%;">Total</th>
                    <th class="text-center" style="width: 18%;">Status</th>
                    <th class="text-center" style="width: 24%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pembayarans as $index => $pembayaran)
                    @php $reservasi = $pembayaran->reservasi; @endphp
                    <tr>
                        <td class="px-3 py-3">
                            <div class="d-flex justify-content-between align-items-center gap-3">
                                <div>
                                    <div class="fw-semibold fs-6">{{ $reservasi->pasien->user->name ?? '-' }}</div>
                                    <div class="small text-muted">{{ $reservasi->pasien->no_rm ?? '-' }}</div>
                                    <div class="small text-muted">{{ $reservasi->pasien->user->email ?? '-' }}</div>
                                </div>
                                <div class="text-end small text-muted">
                                    {{ $reservasi->tanggal_reservasi ?? '-' }}
                                </div>
                            </div>
                        </td>
                        <td class="text-center px-3 py-3">
                            <strong>Rp {{ number_format($pembayaran->total_bayar ?? 0, 0, ',', '.') }}</strong>
                        </td>
                        <td class="text-center px-3 py-3">
                            @if($pembayaran->status == 'lunas')
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold">
                                    <i class="fa-solid fa-circle-check me-1"></i> Lunas
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded-pill fw-semibold">
                                    <i class="fa-solid fa-clock me-1"></i> Belum Bayar
                                </span>
                            @endif
                        </td>
                        <td class="text-center px-3 py-3">
                            <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap">
                                @if($pembayaran->bukti_bayar)
                                    <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#modalBukti{{ $pembayaran->id }}">
                                        <i class="fa-solid fa-image me-1"></i> Lihat Bukti
                                    </button>
                                @endif
                                @if($pembayaran->status === 'lunas' && $pembayaran->kuitansi_path)
                                    <a href="{{ route('pembayaran.kuitansi', $pembayaran->id) }}" class="btn btn-sm btn-success" target="_blank" rel="noopener">
                                        <i class="fa-solid fa-receipt me-1"></i> Slip
                                    </a>
                                @endif
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalPembayaran{{ $pembayaran->id }}">
                                    <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                                </button>
                                <a href="{{ route('pembayaran.invoice', $reservasi->id) }}" class="btn btn-sm btn-secondary">
                                    <i class="fa-solid fa-file-invoice me-1"></i> Invoice
                                </a>
                            </div>
                        </td>
                    </tr>

                    @if($pembayaran->bukti_bayar)
                    <div class="modal fade" id="modalBukti{{ $pembayaran->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Bukti Pembayaran</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <img src="{{ asset('storage/' . $pembayaran->bukti_bayar) }}" alt="Bukti pembayaran pasien" class="img-fluid rounded border shadow-sm" style="max-height: 80vh;">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="modal fade" id="modalPembayaran{{ $pembayaran->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <form action="{{ route('pembayaran.update', $pembayaran->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">Update Pembayaran</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Pasien</label>
                                                <input type="text" class="form-control" value="{{ $reservasi->pasien->user->name ?? '-' }}" disabled>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Tanggal Reservasi</label>
                                                <input type="text" class="form-control" value="{{ $reservasi->tanggal_reservasi ?? '-' }}" disabled>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Total Bayar</label>
                                                <input type="text" class="form-control" value="Rp {{ number_format($pembayaran->total_bayar ?? 0, 0, ',', '.') }}" disabled>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Metode Pembayaran</label>
                                                <select name="metode_bayar" class="form-select" required>
                                                    <option value="Tunai" {{ $pembayaran->metode_bayar == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                                                    <option value="Transfer" {{ $pembayaran->metode_bayar == 'Transfer' ? 'selected' : '' }}>Transfer</option>
                                                </select>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label">Catatan Admin</label>
                                                <textarea name="catatan_admin" class="form-control" rows="3" placeholder="Contoh: bukti transfer valid, menunggu konfirmasi akhir...">{{ old('catatan_admin', $pembayaran->catatan_admin) }}</textarea>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label">Bukti Pembayaran</label>
                                                <input type="file" name="bukti_bayar" class="form-control" accept="image/*">
                                                @if($pembayaran->bukti_bayar)
                                                    <div class="mt-2">
                                                        <img src="{{ asset('storage/' . $pembayaran->bukti_bayar) }}" alt="Bukti pembayaran" class="img-fluid rounded border" style="max-height: 220px;">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        @if($pembayaran->status !== 'lunas')
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        @endif
                                    </div>
                                </form>
                                @if($pembayaran->status !== 'lunas')
                                    <form action="{{ route('pembayaran.konfirmasi', $pembayaran->id) }}" method="POST" class="px-3 pb-3">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fa-solid fa-circle-check me-1"></i> Konfirmasi Pembayaran
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Belum ada data pembayaran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
