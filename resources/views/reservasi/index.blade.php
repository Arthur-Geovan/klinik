@extends('layout.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Data Reservasi Klinik</h3>
    @if(Auth::user()->role === 'pasien')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahReservasi">
        <i class="fa-solid fa-calendar-plus me-1"></i> Buat Reservasi Baru
    </button>
    @endif
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama Pasien</th>
                    <th>Dokter Tujuan</th>
                    <th>Tanggal Reservasi</th>
                    <th>Status</th>
                    @if(Auth::user()->role !== 'pasien')<th>Aksi</th>@endif
                </tr>
            </thead>
            <tbody>
                @php
                    $currentDate = null;
                @endphp
                @forelse($reservasis as $index => $res)
                    @php
                        $reservationDate = \Carbon\Carbon::parse($res->tanggal_reservasi)->format('Y-m-d');
                    @endphp

                    @if($currentDate !== $reservationDate)
                        <tr class="table-secondary">
                            <td colspan="{{ Auth::user()->role !== 'pasien' ? 6 : 5 }}" class="fw-semibold text-secondary py-2">
                                {{ \Carbon\Carbon::parse($res->tanggal_reservasi)->translatedFormat('l, d F Y') }}
                            </td>
                        </tr>
                        @php $currentDate = $reservationDate; @endphp
                    @endif

                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $res->pasien->user->name ?? '-' }}</td>
                        <td>{{ $res->dokter->user->name ?? '-' }} ({{ $res->dokter->spesialisasi ?? '' }})</td>
                        <td>{{ $res->tanggal_reservasi }}</td>
                        <td>
                            <span class="badge bg-{{ $res->status == 'dikonfirmasi' ? 'info' : ($res->status == 'selesai' ? 'success' : ($res->status == 'batal' ? 'danger' : 'warning')) }}">
                                {{ ucfirst($res->status) }}
                            </span>
                        </td>
                        @if(Auth::user()->role !== 'pasien')
                        <td>
                            <form action="{{ route('reservasi.updateStatus', $res->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()" class="form-select form-select-sm">
                                    <option value="menunggu" {{ $res->status == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                    <option value="dikonfirmasi" {{ $res->status == 'dikonfirmasi' ? 'selected' : '' }}>Konfirmasi</option>
                                    <option value="selesai" {{ $res->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="batal" {{ $res->status == 'batal' ? 'selected' : '' }}>Batal</option>
                                </select>
                            </form>
                        </td>
                        @endif
                    </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted">Belum ada reservasi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if(Auth::user()->role === 'pasien')
<div class="modal fade" id="modalTambahReservasi" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('reservasi.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Buat Reservasi Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih Dokter</label>
                        <select name="dokter_id" class="form-select" required>
                            <option value="">-- Pilih Dokter --</option>
                            @foreach(\App\Models\MDokter::with('user')->get() as $dokter)
                                <option value="{{ $dokter->id }}">{{ $dokter->user->name ?? '-' }} - {{ $dokter->spesialisasi ?? 'Umum' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pilih Jadwal Praktik</label>
                        <select name="jadwal_praktik_id" class="form-select" required disabled>
                            <option value="">-- Pilih Jadwal --</option>
                            @foreach(\App\Models\MJadwalPraktik::with('dokter.user')->get() as $jadwal)
                                <option value="{{ $jadwal->id }}" data-doctor-id="{{ $jadwal->dokter_id }}">
                                    {{ $jadwal->dokter->user->name ?? '-' }} | {{ $jadwal->hari }} | {{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }} | Kuota: {{ $jadwal->kuota }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Reservasi</label>
                        <input type="date" name="tanggal_reservasi" class="form-control" min="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan Reservasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const doctorSelect = document.querySelector('select[name="dokter_id"]');
        const scheduleSelect = document.querySelector('select[name="jadwal_praktik_id"]');

        if (!doctorSelect || !scheduleSelect) {
            return;
        }

        const allScheduleOptions = Array.from(scheduleSelect.options).slice(1);

        function refreshScheduleOptions() {
            const selectedDoctorId = doctorSelect.value;

            if (!selectedDoctorId) {
                scheduleSelect.innerHTML = '<option value="">-- Pilih Jadwal --</option>';
                scheduleSelect.disabled = true;
                return;
            }

            const matchingOptions = allScheduleOptions.filter(option => {
                return option.dataset.doctorId === selectedDoctorId;
            });

            scheduleSelect.innerHTML = '<option value="">-- Pilih Jadwal --</option>' + matchingOptions.map(option => option.outerHTML).join('');
            scheduleSelect.disabled = matchingOptions.length === 0;
        }

        doctorSelect.addEventListener('change', refreshScheduleOptions);
        refreshScheduleOptions();
    });
</script>
@endsection