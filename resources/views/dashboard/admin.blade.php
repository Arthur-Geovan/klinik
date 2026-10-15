@extends('layout.app')

@section('content')
<style>
    .admin-dashboard {
        max-width: 1180px;
        margin: 0 auto;
        padding: 4px 4px 24px;
    }

    .dashboard-heading {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 28px;
        padding: 20px 22px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 10px 30px rgba(23, 50, 77, 0.06);
        border: 1px solid rgba(171, 192, 208, 0.18);
    }

    .dashboard-heading h2 {
        margin-bottom: 5px;
        color: #17324d;
        font-size: clamp(1.9rem, 2vw, 2.8rem);
        letter-spacing: -0.05em;
        font-weight: 800;
    }

    .dashboard-heading p {
        margin: 0;
        color: #8290a3;
        font-size: 14px;
    }

    .stat-card {
        position: relative;
        min-height: 142px;
        overflow: hidden;
        border: 0;
        border-radius: 18px;
        box-shadow: 0 12px 26px rgba(31, 45, 61, 0.08);
        transition: transform 180ms ease, box-shadow 180ms ease;
    }

    .stat-link {
        display: block;
        color: inherit;
        text-decoration: none;
        animation: dashboard-reveal 420ms ease both;
    }

    .stat-link:nth-child(2) { animation-delay: 80ms; }
    .stat-link:nth-child(3) { animation-delay: 160ms; }

    .stat-link:hover,
    .stat-link:focus-visible {
        color: inherit;
        text-decoration: none;
        transform: translateY(-4px);
    }

    .stat-link:hover .stat-card,
    .stat-link:focus-visible .stat-card {
        box-shadow: 0 13px 26px rgba(31, 45, 61, 0.16);
    }

    .stat-link:focus-visible {
        outline: 3px solid rgba(42, 167, 155, 0.35);
        outline-offset: 4px;
        border-radius: 10px;
    }

    .stat-card::after {
        position: absolute;
        right: -28px;
        bottom: -46px;
        width: 145px;
        height: 145px;
        border: 24px solid rgba(255, 255, 255, 0.12);
        border-radius: 50%;
        content: '';
    }

    .stat-card .card-body {
        position: relative;
        z-index: 1;
        padding: 23px 24px;
    }

    .stat-icon {
        display: inline-flex;
        width: 42px;
        height: 42px;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.2);
        font-size: 17px;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,0.12);
    }

    .stat-card h6 {
        margin-bottom: 5px;
        font-size: 0.82rem;
        font-weight: 600;
        opacity: 0.95;
        letter-spacing: 0.02em;
    }

    .stat-card h3 {
        margin: 0;
        font-size: clamp(1.5rem, 2vw, 2.2rem);
        font-weight: 800;
        letter-spacing: -0.04em;
    }

    .stock-panel {
        overflow: hidden;
        border: 1px solid rgba(171, 192, 208, 0.18);
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.92);
        box-shadow: 0 12px 26px rgba(31, 45, 61, 0.07);
        animation: dashboard-reveal 500ms 220ms ease both;
    }

    .stock-panel .card-header {
        padding: 18px 22px;
        border-bottom: 1px solid #edf0f4;
        background: linear-gradient(180deg, rgba(255,255,255,0.95), rgba(246,250,252,0.95));
    }

    .stock-panel .card-body {
        padding: 8px 22px 18px;
    }

    .stock-panel .list-group-item {
        padding: 16px 0;
        border-color: #edf0f4;
        color: #344054;
        font-size: 0.96rem;
    }

    .stock-name {
        font-weight: 600;
    }

    .stock-unit {
        color: #8a96a8;
        font-size: 13px;
    }

    .admin-dashboard .bg-primary { background-color: #236b8e !important; }
    .admin-dashboard .bg-success { background-color: #2aa79b !important; }
    .admin-dashboard .bg-warning { background-color: #d7a43b !important; }
    .admin-dashboard .text-primary { color: #236b8e !important; }
    .admin-dashboard .text-warning { color: #b27a15 !important; }

    @keyframes dashboard-reveal {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .stat-link,
        .stock-panel {
            animation: none;
        }

        .stat-card {
            transition: none;
        }
    }

    @media (max-width: 575.98px) {
        .dashboard-heading {
            display: block;
            margin-bottom: 20px;
        }

        .dashboard-heading h2 {
            font-size: 25px;
        }
    }
</style>

<div class="admin-dashboard">
    <div class="dashboard-heading">
        <div>
            <h2>Dashboard Admin</h2>
            <p>Ringkasan aktivitas dan kondisi operasional klinik.</p>
        </div>
        <span class="text-muted small"><i class="fa-regular fa-calendar me-1"></i> Hari ini</span>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <a href="{{ route('reservasi.index') }}" class="stat-link" aria-label="Buka halaman reservasi">
                <div class="card stat-card bg-primary text-white">
                    <div class="card-body">
                        <span class="stat-icon"><i class="fa-solid fa-calendar-check"></i></span>
                        <h6>Reservasi Hari Ini</h6>
                        <h3>{{ $reservasi_hari_ini }}</h3>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('pembayaran.index') }}" class="stat-link" aria-label="Buka halaman pembayaran">
                <div class="card stat-card bg-success text-white">
                    <div class="card-body">
                        <span class="stat-icon"><i class="fa-solid fa-wallet"></i></span>
                        <h6>Total Pendapatan</h6>
                        <h3>Rp {{ number_format($total_pendapatan, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('obat.index') }}" class="stat-link" aria-label="Buka halaman data obat">
                <div class="card stat-card bg-warning text-dark">
                    <div class="card-body">
                        <span class="stat-icon"><i class="fa-solid fa-triangle-exclamation"></i></span>
                        <h6>Peringatan Stok Obat</h6>
                        <h3>{{ count($stok_menipis) }} Item</h3>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="card stock-panel">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
            <span class="fw-bold"><i class="fa-solid fa-boxes-stacked text-warning me-2"></i>Stok Obat Menipis</span>
            <span class="text-muted small">Batas minimum: 10</span>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                @forelse($stok_menipis as $o)
                    <li class="list-group-item d-flex justify-content-between align-items-center gap-3">
                        <span><span class="stock-name">{{ $o->nama_obat }}</span> <span class="stock-unit">({{ $o->satuan }})</span></span>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-danger rounded-pill px-3 py-2">Sisa: {{ $o->stok }}</span>
                            <form action="{{ route('obat.tambahStok', $o->id) }}" method="POST" class="d-inline-flex align-items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="tambahan_stok" value="10" min="1" class="form-control form-control-sm" style="width: 78px; text-align: center;" aria-label="Jumlah stok yang ditambah">
                                <button type="submit" class="btn btn-sm btn-success" title="Tambah stok">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </form>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Semua stok obat dalam kondisi aman.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<div class="card stock-panel mt-4">
    <div class="card-header bg-white d-flex align-items-center justify-content-between">
        <span class="fw-bold"><i class="fa-solid fa-paperclip text-primary me-2"></i>Lampiran Rekam Medis</span>
        <span class="text-muted small">Hasil lab dan foto pasien</span>
    </div>
    <div class="card-body">
        <div class="list-group list-group-flush">
            @forelse($lampiran_rekam_medis as $rekamMedis)
                @foreach($rekamMedis->lampiran as $lampiran)
                    <div class="list-group-item px-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <div class="fw-semibold">{{ $rekamMedis->reservasi->pasien->user->name ?? 'Pasien' }}</div>
                            <small class="text-muted">
                                Dokter: {{ $rekamMedis->reservasi->dokter->user->name ?? '-' }}
                                &middot; {{ $lampiran->keterangan ?? 'Lampiran Rekam Medis' }}
                            </small>
                        </div>
                        <a href="{{ asset('storage/' . $lampiran->file_path) }}" class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener">
                            <i class="fa-solid fa-download me-1"></i> Buka Lampiran
                        </a>
                    </div>
                @endforeach
            @empty
                <div class="py-3 text-muted">Belum ada lampiran rekam medis.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection