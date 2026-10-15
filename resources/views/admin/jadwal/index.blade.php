@extends('layout.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Kelola Jadwal Praktik</h3>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahJadwal">
        <i class="fa-solid fa-plus me-1"></i> Tambah Jadwal
    </button>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Dokter</th>
                        <th>Hari</th>
                        <th>Jam</th>
                        <th>Kuota</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwalGroups as $index => $jadwal)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $jadwal['dokter'] }}</td>
                        <td>{{ $jadwal['hari'] }}</td>
                        <td>{{ $jadwal['jam_mulai'] }} - {{ $jadwal['jam_selesai'] }}</td>
                        <td>{{ $jadwal['kuota'] }}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditJadwal{{ $jadwal['id'] }}">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('jadwal.destroy', $jadwal['id']) }}" method="POST" class="d-inline" onsubmit="return confirmDelete(this)">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Belum ada jadwal praktik.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@foreach($jadwalGroups as $jadwal)
<div class="modal fade" id="modalEditJadwal{{ $jadwal['id'] }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('jadwal.update', $jadwal['id']) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Jadwal Praktik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Dokter</label>
                        <select name="dokter_id" class="form-select" required>
                            @foreach (\App\Models\MDokter::with('user')->get() as $dokter)
                                <option value="{{ $dokter->id }}" {{ $jadwal['dokter'] == ($dokter->user->name ?? '-') ? 'selected' : '' }}>{{ $dokter->user->name ?? '-' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Hari</label>
                            <input type="text" name="hari" class="form-control" value="{{ $jadwal['hari'] }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kuota</label>
                            <input type="number" name="kuota" class="form-control" min="1" value="{{ $jadwal['kuota'] }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control" value="{{ $jadwal['jam_mulai'] }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control" value="{{ $jadwal['jam_selesai'] }}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<div class="modal fade" id="modalTambahJadwal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('jadwal.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Jadwal Praktik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Dokter</label>
                        <select name="dokter_id" class="form-select" required>
                            <option value="">-- Pilih Dokter --</option>
                            @foreach (\App\Models\MDokter::with('user')->get() as $dokter)
                                <option value="{{ $dokter->id }}">{{ $dokter->user->name ?? '-' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Hari</label>
                            <input type="text" name="hari" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kuota</label>
                            <input type="number" name="kuota" class="form-control" min="1" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
