@extends('layout.app')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="h3 mb-1">Dashboard Apoteker</h2>
            <p class="text-muted mb-0">Ringkasan resep dan persediaan obat.</p>
        </div>
        <a href="{{ route('apoteker.resep.index') }}" class="btn btn-primary">
            <i class="fa-solid fa-prescription-bottle-medical me-2"></i>Resep Masuk
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Resep Menunggu</div>
                        <div class="fs-2 fw-bold">{{ $resep_menunggu }}</div>
                    </div>
                    <i class="fa-solid fa-prescription-bottle-medical text-primary fs-2"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <a href="{{ route('obat.index') }}" class="card border-0 shadow-sm h-100 text-decoration-none text-dark">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Stok Menipis (maksimal 10)</div>
                        <div class="fs-2 fw-bold">{{ $stok_menipis->count() }}</div>
                    </div>
                    <i class="fa-solid fa-triangle-exclamation text-warning fs-2"></i>
                </div>
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>Stok Obat Menipis</strong>
            <a href="{{ route('obat.index') }}" class="btn btn-sm btn-outline-primary">Kelola Data Obat</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th class="ps-3">Nama Obat</th><th>Satuan</th><th class="text-end pe-3">Stok</th></tr>
                </thead>
                <tbody>
                    @forelse($stok_menipis as $obat)
                        <tr>
                            <td class="ps-3">{{ $obat->nama_obat }}</td>
                            <td>{{ $obat->satuan }}</td>
                            <td class="text-end pe-3"><span class="badge text-bg-warning">{{ $obat->stok }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">Tidak ada stok menipis.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
