@extends('layout.app')

@section('content')
<div class="patient-dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title mb-0">Dashboard Pasien</h2>
    </div>

    <div class="row g-4 align-items-stretch">
        <div class="col-lg-4">
            <div class="card patient-profile-card border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-center text-center p-4">
                    <div class="profile-icon mb-3 mx-auto">
                        <i class="fa-solid fa-id-card"></i>
                    </div>
                    <h3 class="patient-name mb-1">{{ Auth::user()->name }}</h3>
                    <p class="text-muted small mb-2">No. Rekam Medis</p>
                    <span class="badge patient-rm-badge mb-4">{{ $pasien->no_rm ?? 'Belum terbit' }}</span>

                    <div class="patient-meta text-start mt-auto">
                        <p class="mb-2"><strong>NIK:</strong> {{ $pasien->nik ?? '-' }}</p>
                        <p class="mb-2"><strong>No. Telp:</strong> {{ $pasien->no_telp ?? '-' }}</p>
                        <p class="mb-0"><strong>Alamat:</strong> {{ $pasien->alamat ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card panel-card border-0 shadow-sm h-100">
                <div class="card-header panel-header border-0">
                    <i class="fa-solid fa-clock-rotate-left me-2"></i> Riwayat Aktivitas &amp; Pembayaran Saya
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 reservation-table">
                            <thead>
                                <tr>
                                    <th class="ps-4">Tanggal</th>
                                    <th>Dokter / Poli</th>
                                    <th class="text-center">Status Reservasi</th>
                                    <th class="text-center">Pembayaran</th>
                                    <th class="pe-4 text-center">Bukti / Slip</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayat_reservasi as $res)
                                @php
                                    $pembayaran = $res->pembayaran;
                                    $statusPembayaran = $pembayaran?->status ?? 'belum_bayar';
                                @endphp
                                <tr>
                                    <td class="ps-4">{{ $res->tanggal_reservasi }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $res->dokter->user->name ?? '-' }}</div>
                                        <small class="text-muted">{{ $res->dokter->poli->nama_poli ?? '' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge reservation-status bg-{{ $res->status == 'selesai' ? 'success' : ($res->status == 'dikonfirmasi' ? 'info' : ($res->status == 'batal' ? 'danger' : 'warning')) }}">
                                            {{ ucfirst($res->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($statusPembayaran === 'lunas')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-2 rounded-pill fw-semibold">Lunas</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-2 rounded-pill fw-semibold">Belum Bayar</span>
                                        @endif
                                        <div class="small text-muted mt-1">
                                            {{ $pembayaran?->metode_bayar ? 'Metode: ' . $pembayaran->metode_bayar : 'Belum ada metode' }}
                                        </div>
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                                            @if($pembayaran?->bukti_bayar)
                                                <button type="button" class="btn btn-sm btn-outline-info rounded-3" data-bs-toggle="modal" data-bs-target="#modalBuktiPasien{{ $res->id }}">
                                                    <i class="fa-solid fa-image me-1"></i> Bukti
                                                </button>
                                            @endif
                                            @if($pembayaran)
                                                @if($pembayaran->status === 'lunas' && $pembayaran->kuitansi_path)
                                                    <a href="{{ route('pembayaran.kuitansi', $pembayaran->id) }}" class="btn btn-sm btn-outline-primary rounded-3" target="_blank" rel="noopener">
                                                        <i class="fa-solid fa-file-invoice me-1"></i> Slip
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                @if($pembayaran?->bukti_bayar)
                                <div class="modal fade" id="modalBuktiPasien{{ $res->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Bukti Pembayaran {{ $res->dokter->user->name ?? 'Dokter' }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <img src="{{ asset('storage/' . $pembayaran->bukti_bayar) }}" alt="Bukti pembayaran pasien" class="img-fluid rounded border shadow-sm" style="max-height: 80vh;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Anda belum memiliki riwayat aktivitas atau pembayaran.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1 align-items-stretch">
        <div class="col-lg-5">
            <div class="card panel-card border-0 shadow-sm h-100">
                <div class="card-header panel-header border-0">
                    <i class="fa-solid fa-upload me-2"></i> Unggah Dokumen &amp; Lampiran Medis
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success small mb-3">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('dashboard.lampiran.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="reservasi_id" class="form-label">Reservasi</label>
                            <select name="reservasi_id" id="reservasi_id" class="form-select" required>
                                <option value="">Pilih reservasi</option>
                                @foreach($riwayat_reservasi as $res)
                                    @if($res->status !== 'batal')
                                        <option value="{{ $res->id }}">{{ $res->tanggal_reservasi }} - {{ $res->dokter->user->name ?? 'Dokter' }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <input type="text" name="keterangan" id="keterangan" class="form-control" placeholder="Contoh: Hasil laboratorium / foto pemeriksaan" maxlength="255">
                        </div>

                        <div class="mb-3">
                            <label for="lampiran" class="form-label">File / Foto</label>
                            <input type="file" name="lampiran" id="lampiran" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                            <small class="text-muted">Format yang didukung: PDF, JPG, JPEG, PNG. Maksimal 5 MB per file.</small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-3">
                            <i class="fa-solid fa-paperclip me-2"></i> Unggah Lampiran
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card panel-card border-0 shadow-sm h-100">
                <div class="card-header panel-header border-0">
                    <i class="fa-solid fa-paperclip me-2"></i> Dokumen &amp; Lampiran Rekam Medis
                </div>
                <div class="card-body">
                    @php($memilikiLampiran = false)
                    <div class="attachment-list">
                        @foreach($riwayat_reservasi as $res)
                            @if($res->rekamMedis && $res->rekamMedis->lampiran->isNotEmpty())
                                @php($memilikiLampiran = true)
                                @foreach($res->rekamMedis->lampiran as $lampiran)
                                    <div class="attachment-item">
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $res->tanggal_reservasi }} • {{ $res->dokter->user->name ?? 'Dokter' }}</div>
                                            <small class="text-muted">{{ $lampiran->keterangan ?? 'Lampiran Rekam Medis' }}</small>
                                        </div>
                                        <a href="{{ asset('storage/' . $lampiran->file_path) }}" class="btn btn-sm btn-outline-primary rounded-3" target="_blank" rel="noopener">
                                            <i class="fa-solid fa-download me-1"></i> Buka
                                        </a>
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                    </div>

                    @unless($memilikiLampiran)
                        <div class="empty-state text-muted">Belum ada lampiran rekam medis yang diunggah.</div>
                    @endunless
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .patient-dashboard {
        padding-bottom: 16px;
    }

    .section-title {
        font-size: clamp(2rem, 3vw, 3rem);
        font-weight: 800;
        color: #1d3557;
        letter-spacing: -0.03em;
    }

    .patient-profile-card,
    .panel-card {
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.96);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        border: 1px solid rgba(148, 163, 184, 0.18);
    }

    .profile-icon {
        width: 96px;
        height: 96px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.18), rgba(14, 165, 233, 0.08));
        color: #2563eb;
        font-size: 2.3rem;
    }

    .patient-name {
        font-size: 2rem;
        font-weight: 800;
        color: #1f2937;
    }

    .patient-rm-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 150px;
        background: linear-gradient(135deg, #1d4ed8, #2563eb);
        color: #fff;
        border-radius: 12px;
        font-size: 1.05rem;
        padding: 0.7rem 1rem;
        margin: 0 auto;
        font-weight: 700;
    }

    .patient-meta {
        font-size: 0.98rem;
        color: #374151;
        line-height: 1.7;
        border-top: 1px solid #e5e7eb;
        padding-top: 1rem;
    }

    .panel-header {
        background: transparent;
        padding: 1.2rem 1.3rem 1rem;
        font-size: 1.1rem;
        font-weight: 800;
        color: #1f2937;
    }

    .reservation-table thead th {
        background: #f3f6fb;
        color: #475569;
        font-size: 0.92rem;
        font-weight: 700;
        border-bottom: 1px solid #e2e8f0;
        padding-top: 0.95rem;
        padding-bottom: 0.95rem;
    }

    .reservation-table tbody td {
        padding-top: 1rem;
        padding-bottom: 1rem;
        border-color: #eef2f7;
        color: #1f2937;
    }

    .reservation-status {
        min-width: 110px;
        border-radius: 10px;
        padding: 0.5rem 0.75rem;
        font-size: 0.82rem;
        font-weight: 700;
    }

    .form-label {
        font-weight: 600;
        color: #334155;
    }

    .form-control,
    .form-select {
        border-radius: 12px;
        border-color: #dbe3ee;
        padding: 0.72rem 0.9rem;
        background: #fff;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #60a5fa;
        box-shadow: 0 0 0 0.2rem rgba(96, 165, 250, 0.12);
    }

    .attachment-list {
        display: flex;
        flex-direction: column;
        gap: 0.9rem;
    }

    .attachment-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 0.9rem 1rem;
    }

    .empty-state {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 120px;
        border: 1px dashed #d1d9e6;
        border-radius: 14px;
        background: #f8fafc;
    }

    @media (max-width: 991.98px) {
        .section-title {
            font-size: 2.1rem;
        }
    }
</style>
@endsection