<?php

namespace Tests\Feature;

use App\Models\MDokter;
use App\Models\MJadwalPraktik;
use App\Models\MPasien;
use App\Models\MPoli;
use App\Models\MUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiReservasiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_receives_json_401_instead_of_login_page(): void
    {
        $response = $this->postJson('/api/reservasi', []);

        $response->assertUnauthorized()
            ->assertJsonPath('status', false)
            ->assertJsonPath('message', 'Anda harus login terlebih dahulu.')
            ->assertJsonStructure(['status', 'message', 'data', 'errors']);
    }

    public function test_patient_can_create_reservation_with_json_response(): void
    {
        [$patientUser, $schedule] = $this->createReservationContext();

        $response = $this->actingAs($patientUser)->postJson('/api/reservasi', [
            'dokter_id' => $schedule->dokter_id,
            'jadwal_praktik_id' => $schedule->id,
            'tanggal_reservasi' => now()->addDay()->toDateString(),
        ]);

        $response->assertCreated()
            ->assertJsonPath('status', true)
            ->assertJsonPath('message', 'Reservasi berhasil dibuat.')
            ->assertJsonStructure(['status', 'message', 'data', 'errors']);

        $this->assertDatabaseHas('reservasis', [
            'pasien_id' => $patientUser->pasien->id,
            'jadwal_praktik_id' => $schedule->id,
            'status' => 'menunggu',
        ]);
    }

    public function test_invalid_reservation_body_returns_422(): void
    {
        [$patientUser] = $this->createReservationContext();

        $response = $this->actingAs($patientUser)->postJson('/api/reservasi', []);

        $response->assertStatus(422)
            ->assertJsonPath('status', false)
            ->assertJsonStructure(['status', 'message', 'data', 'errors']);
    }

    public function test_non_patient_cannot_create_reservation(): void
    {
        $admin = MUser::create([
            'name' => 'Admin API',
            'email' => 'admin-reservasi@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->postJson('/api/reservasi', []);

        $response->assertForbidden()
            ->assertJsonPath('status', false);
    }

    public function test_patient_cannot_choose_schedule_from_other_doctor(): void
    {
        [$patientUser, $validSchedule] = $this->createReservationContext();

        $poli = MPoli::create(['nama_poli' => 'Poli Lain', 'deskripsi' => 'Layanan Lain']);
        $otherDoctorUser = MUser::create([
            'name' => 'Dokter Lain',
            'email' => 'dokter-lain@test.com',
            'password' => Hash::make('password'),
            'role' => 'dokter',
        ]);
        $otherDoctor = MDokter::create([
            'user_id' => $otherDoctorUser->id,
            'poli_id' => $poli->id,
            'no_str' => 'STR-API-002',
            'spesialisasi' => 'Umum',
        ]);
        $otherSchedule = MJadwalPraktik::create([
            'dokter_id' => $otherDoctor->id,
            'hari' => 'Selasa',
            'jam_mulai' => '09:00',
            'jam_selesai' => '12:00',
            'kuota' => 5,
        ]);

        $response = $this->actingAs($patientUser)->postJson('/api/reservasi', [
            'dokter_id' => $validSchedule->dokter_id,
            'jadwal_praktik_id' => $otherSchedule->id,
            'tanggal_reservasi' => now()->addDay()->toDateString(),
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('status', false);
    }

    private function createReservationContext(): array
    {
        $poli = MPoli::create(['nama_poli' => 'Poli API', 'deskripsi' => 'Layanan API']);
        $doctorUser = MUser::create([
            'name' => 'Dokter API',
            'email' => 'dokter-reservasi@test.com',
            'password' => Hash::make('password'),
            'role' => 'dokter',
        ]);
        $doctor = MDokter::create([
            'user_id' => $doctorUser->id,
            'poli_id' => $poli->id,
            'no_str' => 'STR-API-001',
            'spesialisasi' => 'Umum',
        ]);
        $patientUser = MUser::create([
            'name' => 'Pasien API',
            'email' => 'pasien-reservasi@test.com',
            'password' => Hash::make('password'),
            'role' => 'pasien',
        ]);
        MPasien::create([
            'user_id' => $patientUser->id,
            'no_rm' => 'RM-API-001',
            'nik' => '1234567890123456',
            'tanggal_lahir' => '1995-05-15',
            'alamat' => 'Jl. API',
            'no_telp' => '081234567890',
        ]);
        $schedule = MJadwalPraktik::create([
            'dokter_id' => $doctor->id,
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '12:00',
            'kuota' => 5,
        ]);

        return [$patientUser->fresh(), $schedule];
    }
}
