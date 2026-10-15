<?php

namespace Tests\Feature;

use App\Models\MDokter;
use App\Models\MJadwalPraktik;
use App\Models\MPasien;
use App\Models\MPoli;
use App\Models\MReservasi;
use App\Models\MUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasienLampiranTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_upload_medical_attachment_for_own_reservation(): void
    {
        $patientUser = MUser::create([
            'name' => 'Pasien Uji',
            'email' => 'pasien-lampiran@test.com',
            'password' => Hash::make('password'),
            'role' => 'pasien',
        ]);

        $patient = MPasien::create([
            'user_id' => $patientUser->id,
            'no_rm' => 'RM-001',
            'nik' => '3201010101010001',
            'tanggal_lahir' => '1995-01-01',
            'alamat' => 'Jl. Uji No. 1',
            'no_telp' => '081234567890',
        ]);

        $poli = MPoli::create(['nama_poli' => 'Umum', 'deskripsi' => 'Poli umum']);
        $dokterUser = MUser::create([
            'name' => 'Dokter Uji',
            'email' => 'dokter-lampiran@test.com',
            'password' => Hash::make('password'),
            'role' => 'dokter',
        ]);
        $dokter = MDokter::create([
            'user_id' => $dokterUser->id,
            'poli_id' => $poli->id,
            'no_str' => 'STR-001',
            'spesialisasi' => 'Dokter Umum',
        ]);
        $jadwal = MJadwalPraktik::create([
            'dokter_id' => $dokter->id,
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '12:00',
            'kuota' => 10,
        ]);

        $reservation = MReservasi::create([
            'pasien_id' => $patient->id,
            'dokter_id' => $dokter->id,
            'jadwal_praktik_id' => $jadwal->id,
            'tanggal_reservasi' => now()->addDay()->toDateString(),
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($patientUser)
            ->post('/dashboard/lampiran', [
                'reservasi_id' => $reservation->id,
                'keterangan' => 'Hasil lab dan foto pemeriksaan',
                'lampiran' => UploadedFile::fake()->create('hasil-lab.pdf', 1024, 'application/pdf'),
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('rekam_medis', [
            'reservasi_id' => $reservation->id,
        ]);
        $this->assertDatabaseHas('lampirans', [
            'keterangan' => 'Hasil lab dan foto pemeriksaan',
        ]);
    }
}
