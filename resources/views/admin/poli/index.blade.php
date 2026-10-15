@extends('layout.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Kelola Data Poli</h3>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahPoli">
        <i class="fa-solid fa-plus me-1"></i> Tambah Poli
    </button>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama Poli</th>
                    <th>Deskripsi</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($polis as $index => $poli)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $poli->nama_poli }}</strong></td>
                    <td>{{ $poli->deskripsi ?? '-' }}</td>
                    <td class="text-center">
                        <!-- Tombol Edit -->
                        <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#modalEditPoli{{ $poli->id }}">
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        </button>
                        <!-- Form Hapus -->
                            <form action="{{ route('poli.destroy', $poli->id) }}" method="POST" class="d-inline" onsubmit="return confirmDelete(this)">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i> Hapus</button>
                        </form>
                    </td>
                </tr>

                <!-- Modal Edit Poli -->
                <div class="modal fade" id="modalEditPoli{{ $poli->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('poli.update', $poli->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Data Poli</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Poli</label>
                                        <input type="text" name="nama_poli" class="form-control" value="{{ $poli->nama_poli }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Deskripsi</label>
                                        <textarea name="deskripsi" class="form-control" rows="3">{{ $poli->deskripsi }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">Belum ada data poli.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Poli -->
<div class="modal fade" id="modalTambahPoli" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('poli.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Poli Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Poli</label>
                        <input type="text" name="nama_poli" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection