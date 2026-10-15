@extends('layout.app')

@section('content')
<h2 class="mb-4 fw-bold">Dashboard Dokter</h2>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white fw-bold">
        <i class="fa-solid fa-user-clock me-2 text-primary"></i> Daftar Pasien Antrean Hari Ini
    </div>
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>No. RM</th>
                    <th>Nama Pasien</th>
                    <th>Status Reservasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pasien_hari_ini as $index => $res)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><span class="badge bg-secondary">{{ $res->pasien->no_rm ?? '-' }}</span></td>
                    <td><strong>{{ $res->pasien->user->name ?? '-' }}</strong></td>
                    <td>
                        <span class="badge bg-{{ $res->status == 'selesai' ? 'success' : 'info' }}">
                            {{ ucfirst($res->status) }}
                        </span>
                    </td>
                    <td>
                        @if($res->status != 'selesai')
                            <!-- Tombol Modal Periksa Pasien -->
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalRekamMedis{{ $res->id }}">
                                <i class="fa-solid fa-stethoscope me-1"></i> Periksa / Isi RM
                            </button>
                        @else
                            <span class="text-success"><i class="fa-solid fa-circle-check me-1"></i> Selesai Diperiksa</span>
                        @endif
                    </td>
                </tr>

                <!-- Modal Input Rekam Medis Dokter -->
                <div class="modal fade" id="modalRekamMedis{{ $res->id }}" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form action="{{ route('rekam-medis.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="reservasi_id" value="{{ $res->id }}">
                                <div class="modal-header">
                                    <h5 class="modal-title">Input Rekam Medis: {{ $res->pasien->user->name ?? '' }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Keluhan Utama</label>
                                        <textarea name="keluhan" class="form-control" rows="2" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Diagnosa Dokter</label>
                                        <textarea name="diagnosa" class="form-control" rows="2" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Catatan Tambahan</label>
                                        <textarea name="catatan" class="form-control" rows="2"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label mb-0">Resep Obat</label>
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="tambahObat({{ $res->id }})">
                                                <i class="fa-solid fa-plus me-1"></i> Tambah Obat
                                            </button>
                                        </div>
                                        <div id="obat-list-{{ $res->id }}">
                                            <div class="row g-2 obat-row mb-2">
                                                <div class="col-md-5">
                                                    <select name="obats[0][id]" class="form-select">
                                                        <option value="">-- Tidak ada obat --</option>
                                                        @foreach($obats as $obat)
                                                            <option value="{{ $obat->id }}">{{ $obat->nama_obat }} (stok: {{ $obat->stok }})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" name="obats[0][dosis]" class="form-control" placeholder="Dosis, contoh: 3x1">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="number" name="obats[0][jumlah]" class="form-control" min="1" placeholder="Jumlah">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Upload Lampiran (Hasil Lab / Foto jika ada)</label>
                                        <input type="file" name="lampiran" class="form-control">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-success">Simpan Rekam Medis</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">Belum ada antrean pasien untuk hari ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<script>
    function tambahObat(reservasiId) {
        const list = document.getElementById(`obat-list-${reservasiId}`);
        const index = list.querySelectorAll('.obat-row').length;
        const row = list.querySelector('.obat-row').cloneNode(true);

        row.querySelectorAll('select, input').forEach((field) => {
            field.name = field.name.replace(/obats\[\d+\]/, `obats[${index}]`);
            field.value = '';
        });

        list.appendChild(row);
    }
</script>
@endsection