<?php

namespace Tests\Feature;

use App\Models\MDokter;
use App\Models\MJadwalPraktik;
use App\Models\MPasien;
use App\Models\MPoli;
use App\Models\MRekamMedis;
use App\Models\MReservasi;
use App\Models\MUser;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_database_seeder_creates_realistic_jadwal_praktik_entries(): void
    {
        (new DatabaseSeeder())->run();

        $this->assertGreaterThanOrEqual(26, MDokter::count());
        $this->assertGreaterThanOrEqual(120, MJadwalPraktik::count());
    }

    public function test_database_seeder_creates_safe_dummy_pasien_and_reservasi_data(): void
    {
        (new DatabaseSeeder())->run();

        $this->assertGreaterThanOrEqual(3, MUser::where('role', 'dokter')->count());
        $this->assertGreaterThanOrEqual(3, MPasien::count());
        $this->assertGreaterThanOrEqual(3, MReservasi::count());
    }

    public function test_database_seeder_creates_dummy_rekam_medis_for_doctor_checkup(): void
    {
        (new DatabaseSeeder())->run();

        $this->assertGreaterThanOrEqual(3, \App\Models\MRekamMedis::count());
    }

    public function test_doctor_dashboard_shows_upcoming_patient_reservations(): void
    {
        $poli = MPoli::create([
            'nama_poli' => 'Poli Umum',
            'deskripsi' => 'Layanan umum',
        ]);

        $doctorUser = MUser::create([
            'name' => 'Dr. Budi',
            'email' => 'dokter@test.com',
            'password' => bcrypt('password'),
            'role' => 'dokter',
        ]);

        $dokter = MDokter::create([
            'user_id' => $doctorUser->id,
            'poli_id' => $poli->id,
            'no_str' => 'STR-TEST-001',
            'spesialisasi' => 'Umum',
        ]);

        $patientUser = MUser::create([
            'name' => 'Ahmad Rizki',
            'email' => 'pasien@test.com',
            'password' => bcrypt('password'),
            'role' => 'pasien',
        ]);

        $patient = MPasien::create([
            'user_id' => $patientUser->id,
            'no_rm' => 'RM-TEST-001',
            'nik' => '1234567890123456',
            'tanggal_lahir' => '1995-05-15',
            'alamat' => 'Jl. Test',
            'no_telp' => '081234567890',
        ]);

        MReservasi::create([
            'pasien_id' => $patient->id,
            'dokter_id' => $dokter->id,
            'jadwal_praktik_id' => null,
            'tanggal_reservasi' => now()->addDay()->toDateString(),
            'status' => 'dikonfirmasi',
        ]);

        $response = $this->actingAs($doctorUser)->get('/dashboard');

        $response->assertOk();
        $response->assertSeeText('Ahmad Rizki');
    }

    public function test_doctor_can_save_medical_record_with_attachment(): void
    {
        Storage::fake('public');

        $poli = MPoli::create([
            'nama_poli' => 'Poli Umum',
            'deskripsi' => 'Layanan umum',
        ]);

        $doctorUser = MUser::create([
            'name' => 'Dr. Budi',
            'email' => 'dokter-upload@test.com',
            'password' => bcrypt('password'),
            'role' => 'dokter',
        ]);

        $dokter = MDokter::create([
            'user_id' => $doctorUser->id,
            'poli_id' => $poli->id,
            'no_str' => 'STR-TEST-002',
            'spesialisasi' => 'Umum',
        ]);

        $patientUser = MUser::create([
            'name' => 'Ahmad Rizki',
            'email' => 'pasien-upload@test.com',
            'password' => bcrypt('password'),
            'role' => 'pasien',
        ]);

        $patient = MPasien::create([
            'user_id' => $patientUser->id,
            'no_rm' => 'RM-TEST-002',
            'nik' => '1234567890123457',
            'tanggal_lahir' => '1995-05-15',
            'alamat' => 'Jl. Test 2',
            'no_telp' => '081234567891',
        ]);

        $reservasi = MReservasi::create([
            'pasien_id' => $patient->id,
            'dokter_id' => $dokter->id,
            'jadwal_praktik_id' => null,
            'tanggal_reservasi' => now()->toDateString(),
            'status' => 'selesai',
        ]);

        $response = $this->actingAs($doctorUser)->post('/rekam-medis', [
            'reservasi_id' => $reservasi->id,
            'keluhan' => 'Sakit kepala',
            'diagnosa' => 'Hipertermi ringan',
            'catatan' => 'Konsultasi lanjutan',
            'lampiran' => UploadedFile::fake()->create('hasil-lab.pdf', 150, 'application/pdf'),
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('rekam_medis', [
            'reservasi_id' => $reservasi->id,
            'keluhan' => 'Sakit kepala',
        ]);
        $this->assertDatabaseHas('lampirans', [
            'keterangan' => 'Lampiran Rekam Medis / Hasil Lab',
        ]);
    }

    public function test_doctor_can_update_existing_medical_record_for_same_reservation(): void
    {
        $poli = MPoli::create([
            'nama_poli' => 'Poli Umum',
            'deskripsi' => 'Layanan umum',
        ]);

        $doctorUser = MUser::create([
            'name' => 'Dr. Update',
            'email' => 'update@test.com',
            'password' => bcrypt('password'),
            'role' => 'dokter',
        ]);

        $dokter = MDokter::create([
            'user_id' => $doctorUser->id,
            'poli_id' => $poli->id,
            'no_str' => 'STR-TEST-003',
            'spesialisasi' => 'Umum',
        ]);

        $patientUser = MUser::create([
            'name' => 'Pasien Update',
            'email' => 'pasien-update@test.com',
            'password' => bcrypt('password'),
            'role' => 'pasien',
        ]);

        $patient = MPasien::create([
            'user_id' => $patientUser->id,
            'no_rm' => 'RM-TEST-003',
            'nik' => '1234567890123458',
            'tanggal_lahir' => '1993-01-10',
            'alamat' => 'Jl. Test 3',
            'no_telp' => '081234567892',
        ]);

        $reservasi = MReservasi::create([
            'pasien_id' => $patient->id,
            'dokter_id' => $dokter->id,
            'jadwal_praktik_id' => null,
            'tanggal_reservasi' => now()->toDateString(),
            'status' => 'dikonfirmasi',
        ]);

        MRekamMedis::create([
            'reservasi_id' => $reservasi->id,
            'keluhan' => 'Awal',
            'diagnosa' => 'Sakit ringan',
            'catatan' => 'Catatan lama',
        ]);

        $response = $this->actingAs($doctorUser)->from('/dashboard')->post('/rekam-medis', [
            'reservasi_id' => $reservasi->id,
            'keluhan' => 'Perbaikan',
            'diagnosa' => 'Sakit sedang',
            'catatan' => 'Catatan baru',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('rekam_medis', [
            'reservasi_id' => $reservasi->id,
            'diagnosa' => 'Sakit sedang',
        ]);
    }

    public function test_confirmed_reservation_creates_payment_record(): void
    {
        $poli = MPoli::create([
            'nama_poli' => 'Poli Umum',
            'deskripsi' => 'Layanan umum',
        ]);

        $doctorUser = MUser::create([
            'name' => 'Dr. Payment',
            'email' => 'dokter-payment@test.com',
            'password' => bcrypt('password'),
            'role' => 'dokter',
        ]);

        $dokter = MDokter::create([
            'user_id' => $doctorUser->id,
            'poli_id' => $poli->id,
            'no_str' => 'STR-PAY-001',
            'spesialisasi' => 'Umum',
        ]);

        $patientUser = MUser::create([
            'name' => 'Pasien Bayar',
            'email' => 'pasien-payment@test.com',
            'password' => bcrypt('password'),
            'role' => 'pasien',
        ]);

        $patient = MPasien::create([
            'user_id' => $patientUser->id,
            'no_rm' => 'RM-PAY-001',
            'nik' => '1234567890123459',
            'tanggal_lahir' => '1990-07-11',
            'alamat' => 'Jl. Payment',
            'no_telp' => '081234567893',
        ]);

        $reservasi = MReservasi::create([
            'pasien_id' => $patient->id,
            'dokter_id' => $dokter->id,
            'jadwal_praktik_id' => null,
            'tanggal_reservasi' => now()->addDay()->toDateString(),
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($doctorUser)->patch('/reservasi/' . $reservasi->id . '/status', [
            'status' => 'dikonfirmasi',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('pembayarans', [
            'reservasi_id' => $reservasi->id,
            'status' => 'belum_bayar',
        ]);
    }

    public function test_admin_can_upload_payment_proof_and_update_method(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $adminUser = MUser::create([
            'name' => 'Admin Klinik',
            'email' => 'admin-payment@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $poli = MPoli::create([
            'nama_poli' => 'Poli Umum',
            'deskripsi' => 'Layanan umum',
        ]);

        $patientUser = MUser::create([
            'name' => 'Pasien Proof',
            'email' => 'pasien-proof@test.com',
            'password' => bcrypt('password'),
            'role' => 'pasien',
        ]);

        $patient = MPasien::create([
            'user_id' => $patientUser->id,
            'no_rm' => 'RM-PROOF-001',
            'nik' => '1234567890123460',
            'tanggal_lahir' => '1991-02-12',
            'alamat' => 'Jl. Proof',
            'no_telp' => '081234567894',
        ]);

        $doctorUser = MUser::create([
            'name' => 'Dr. Proof',
            'email' => 'dokter-proof@test.com',
            'password' => bcrypt('password'),
            'role' => 'dokter',
        ]);

        $dokter = MDokter::create([
            'user_id' => $doctorUser->id,
            'poli_id' => $poli->id,
            'no_str' => 'STR-PROOF-001',
            'spesialisasi' => 'Umum',
        ]);

        $reservasi = MReservasi::create([
            'pasien_id' => $patient->id,
            'dokter_id' => $dokter->id,
            'jadwal_praktik_id' => null,
            'tanggal_reservasi' => now()->toDateString(),
            'status' => 'dikonfirmasi',
        ]);

        $reservasi->update(['status' => 'selesai']);
        MRekamMedis::create([
            'reservasi_id' => $reservasi->id,
            'keluhan' => 'Pemeriksaan selesai',
            'diagnosa' => 'Tidak memerlukan resep',
        ]);

        $pembayaran = \App\Models\MPembayaran::create([
            'reservasi_id' => $reservasi->id,
            'total_bayar' => 150000,
            'metode_bayar' => 'Tunai',
            'status' => 'belum_bayar',
        ]);

        $response = $this->actingAs($adminUser)->post('/pembayaran/' . $pembayaran->id . '/update', [
            'metode_bayar' => 'Transfer',
            'status' => 'lunas',
            'catatan_admin' => 'Bukti transfer valid',
            'bukti_bayar' => UploadedFile::fake()->image('bukti-transfer.jpg', 600, 800),
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('pembayarans', [
            'id' => $pembayaran->id,
            'metode_bayar' => 'Transfer',
            'status' => 'belum_bayar',
        ]);

        $this->actingAs($adminUser)
            ->patch(route('pembayaran.konfirmasi', $pembayaran->id))
            ->assertSessionHas('success');

        $pembayaran->refresh();
        $this->assertSame('lunas', $pembayaran->status);
        $this->assertNotNull($pembayaran->bukti_bayar);
        Storage::disk('local')->assertExists($pembayaran->kuitansi_path);
    }

    public function test_admin_can_add_stock_for_low_stock_medicine(): void
    {
        $adminUser = MUser::create([
            'name' => 'Admin Stock',
            'email' => 'admin-stock@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $obat = \App\Models\MObat::create([
            'nama_obat' => 'Isotretinoin',
            'satuan' => 'Kapsul',
            'stok' => 8,
            'harga' => 25000,
        ]);

        $response = $this->actingAs($adminUser)->patch('/admin/obat/' . $obat->id . '/tambah-stok', [
            'tambahan_stok' => 12,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('obats', [
            'id' => $obat->id,
            'stok' => 20,
        ]);
    }

    public function test_doctor_can_logout_successfully(): void
    {
        $doctorUser = MUser::create([
            'name' => 'Dr. Logout',
            'email' => 'logout@test.com',
            'password' => bcrypt('password'),
            'role' => 'dokter',
        ]);

        $response = $this->actingAs($doctorUser)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_doctor_can_login_with_seeded_email(): void
    {
        $doctorUser = MUser::create([
            'name' => 'Dr. Budi',
            'email' => 'dokter@klinik.com',
            'password' => bcrypt('password'),
            'role' => 'dokter',
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'dokter@klinik.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($doctorUser);
    }
}
