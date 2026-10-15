<?php

namespace Tests\Feature;

use App\Models\MDokter;
use App\Models\MObat;
use App\Models\MPasien;
use App\Models\MPembayaran;
use App\Models\MPoli;
use App\Models\MRekamMedis;
use App\Models\MReservasi;
use App\Models\MUser;
use Database\Seeders\ApotekerSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApotekerWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_recipe_confirmation_snapshots_price_without_deducting_stock_again(): void
    {
        [, $doctor, $apoteker, $reservation, $obat] = $this->createContext();
        $obat->update(['stok' => 7]);

        $rekamMedis = MRekamMedis::create([
            'reservasi_id' => $reservation->id,
            'keluhan' => 'Demam',
            'diagnosa' => 'Demam',
            'status_resep' => 'menunggu',
        ]);
        $rekamMedis->obat()->attach($obat->id, ['dosis' => '3 kali sehari', 'jumlah' => 3]);

        $this->actingAs($apoteker)
            ->patch(route('apoteker.resep.siap', $rekamMedis->id))
            ->assertRedirect();

        $this->assertDatabaseHas('obats', ['id' => $obat->id, 'stok' => 7]);
        $this->assertDatabaseHas('rekam_medis', ['id' => $rekamMedis->id, 'status_resep' => 'siap']);
        $this->assertDatabaseHas('rincian_pembayaran_obat', [
            'nama_obat' => 'Paracetamol',
            'jumlah' => 3,
            'harga_satuan' => 1200,
            'subtotal' => 3600,
        ]);

        $pembayaran = MPembayaran::where('reservasi_id', $reservation->id)->firstOrFail();
        $this->assertSame(53600, (int) $pembayaran->total_bayar);

        $obat->update(['harga' => 2500]);
        $this->assertSame(1200, (int) $pembayaran->fresh()->rincianObat->first()->harga_satuan);

        $this->actingAs($doctor)->post(route('rekam-medis.store'), [
            'reservasi_id' => $reservation->id,
            'keluhan' => 'Demam',
            'diagnosa' => 'Demam',
            'obats' => [['id' => $obat->id, 'dosis' => '3 kali sehari', 'jumlah' => 3]],
        ])->assertForbidden();
    }

    public function test_doctor_can_replace_pending_recipe_without_double_deducting_stock(): void
    {
        [, $doctor, $apoteker, $reservation, $obat] = $this->createContext();

        $payload = [
            'reservasi_id' => $reservation->id,
            'keluhan' => 'Demam',
            'diagnosa' => 'Demam',
            'obats' => [['id' => $obat->id, 'dosis' => '3 kali sehari', 'jumlah' => 2]],
        ];

        $this->actingAs($doctor)->post(route('rekam-medis.store'), $payload)->assertRedirect();
        $this->assertDatabaseHas('obats', ['id' => $obat->id, 'stok' => 8]);

        $payload['obats'][0]['jumlah'] = 3;
        $this->actingAs($doctor)->post(route('rekam-medis.store'), $payload)->assertRedirect();
        $this->assertDatabaseHas('obats', ['id' => $obat->id, 'stok' => 7]);

        $rekamMedis = MRekamMedis::where('reservasi_id', $reservation->id)->firstOrFail();
        $this->actingAs($apoteker)
            ->patch(route('apoteker.resep.siap', $rekamMedis->id))
            ->assertRedirect();

        $this->assertDatabaseHas('obats', ['id' => $obat->id, 'stok' => 7]);
    }

    public function test_payment_requires_ready_recipe_and_receipt_is_private_and_not_duplicated(): void
    {
        [$patient, , $apoteker, $reservation, $obat, $admin] = $this->createContext();
        $rekamMedis = MRekamMedis::create([
            'reservasi_id' => $reservation->id,
            'keluhan' => 'Demam',
            'diagnosa' => 'Demam',
            'status_resep' => 'menunggu',
        ]);
        $rekamMedis->obat()->attach($obat->id, ['dosis' => '1 kali sehari', 'jumlah' => 1]);
        $pembayaran = MPembayaran::create([
            'reservasi_id' => $reservation->id,
            'biaya_konsultasi' => 50000,
            'biaya_pengecekan' => 0,
            'total_bayar' => 50000,
            'status' => 'belum_bayar',
        ]);
        Storage::fake('local');

        $this->actingAs($admin)->get(route('pembayaran.index'))->assertOk();
        $this->get(route('pembayaran.invoice', $reservation->id))
            ->assertOk()
            ->assertSee('Biaya Pengecekan');

        $this->actingAs($admin)
            ->patch(route('pembayaran.konfirmasi', $pembayaran->id))
            ->assertSessionHasErrors('pembayaran');
        $this->assertDatabaseHas('pembayarans', ['id' => $pembayaran->id, 'status' => 'belum_bayar']);

        $this->actingAs($apoteker)
            ->patch(route('apoteker.resep.siap', $rekamMedis->id))
            ->assertRedirect();

        $this->actingAs($apoteker)
            ->patch(route('apoteker.resep.diserahkan', $rekamMedis->id))
            ->assertSessionHasErrors('resep');
        $this->assertDatabaseHas('rekam_medis', ['id' => $rekamMedis->id, 'status_resep' => 'siap']);

        $this->actingAs($admin)
            ->patch(route('pembayaran.konfirmasi', $pembayaran->id))
            ->assertRedirect();

        $paidPayment = $pembayaran->fresh();
        $this->assertSame('lunas', $paidPayment->status);
        $this->assertNotNull($paidPayment->dikonfirmasi_oleh);
        $this->assertNotNull($paidPayment->dikonfirmasi_pada);
        Storage::disk('local')->assertExists($paidPayment->kuitansi_path);

        $this->actingAs($patient)
            ->get(route('pembayaran.kuitansi', $paidPayment->id))
            ->assertOk();

        $this->actingAs($apoteker)
            ->patch(route('apoteker.resep.diserahkan', $rekamMedis->id))
            ->assertRedirect();
        $this->assertDatabaseHas('rekam_medis', ['id' => $rekamMedis->id, 'status_resep' => 'diserahkan']);

        $otherPatient = $this->createPatient('pasien-lain@test.com', 'RM-OTHER', '3201010101010002');
        $this->actingAs($otherPatient)
            ->get(route('pembayaran.kuitansi', $paidPayment->id))
            ->assertForbidden();

        $receiptNumber = $paidPayment->nomor_kuitansi;
        $this->actingAs($admin)
            ->patch(route('pembayaran.konfirmasi', $paidPayment->id))
            ->assertSessionHasErrors('pembayaran');
        $this->assertSame($receiptNumber, $paidPayment->fresh()->nomor_kuitansi);
    }

    public function test_seeded_apothecary_can_login_without_exposing_password_fields(): void
    {
        $this->seed(ApotekerSeeder::class);

        $apoteker = MUser::where('email', 'apoteker@klinik.com')->firstOrFail();
        $this->assertSame('apoteker', $apoteker->role);
        $this->assertTrue(Hash::check('Apoteker#2026', $apoteker->password));
        $this->assertNull($apoteker->login_password);
        $this->assertArrayNotHasKey('password', $apoteker->toArray());
        $this->assertArrayNotHasKey('login_password', $apoteker->toArray());

        $this->post(route('login.post'), [
            'email' => 'apoteker@klinik.com',
            'password' => 'Apoteker#2026',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($apoteker);
        $this->get(route('dashboard'))->assertOk()->assertSee('Dashboard Apoteker');
        $this->get(route('apoteker.resep.index'))->assertOk()->assertSee('Resep Masuk');
        $this->get(route('obat.index'))->assertOk();
        $this->get(route('reservasi.index'))->assertForbidden();
        $this->get(route('pembayaran.index'))->assertForbidden();
    }

    private function createContext(): array
    {
        $poli = MPoli::create(['nama_poli' => 'Poli Umum', 'deskripsi' => 'Umum']);
        $doctor = MUser::create([
            'name' => 'Dokter Uji',
            'email' => 'dokter-workflow@test.com',
            'password' => Hash::make('password'),
            'role' => 'dokter',
        ]);
        $dokter = MDokter::create([
            'user_id' => $doctor->id,
            'poli_id' => $poli->id,
            'no_str' => 'STR-WORKFLOW-001',
            'spesialisasi' => 'Umum',
            'tarif_konsultasi' => 50000,
            'tarif_pengecekan' => 0,
        ]);
        $patient = $this->createPatient('pasien-workflow@test.com', 'RM-WORKFLOW', '3201010101010001');
        $apoteker = MUser::create([
            'name' => 'Apoteker Uji',
            'email' => 'apoteker-workflow@test.com',
            'password' => Hash::make('password'),
            'role' => 'apoteker',
        ]);
        $admin = MUser::create([
            'name' => 'Admin Uji',
            'email' => 'admin-workflow@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        $reservation = MReservasi::create([
            'pasien_id' => $patient->pasien->id,
            'dokter_id' => $dokter->id,
            'tanggal_reservasi' => now()->toDateString(),
            'status' => 'selesai',
        ]);
        $obat = MObat::create([
            'nama_obat' => 'Paracetamol',
            'satuan' => 'tablet',
            'stok' => 10,
            'harga' => 1200,
        ]);

        return [$patient, $doctor, $apoteker, $reservation, $obat, $admin];
    }

    private function createPatient(string $email, string $noRm, string $nik): MUser
    {
        $patient = MUser::create([
            'name' => 'Pasien Uji',
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => 'pasien',
        ]);
        MPasien::create([
            'user_id' => $patient->id,
            'no_rm' => $noRm,
            'nik' => $nik,
            'tanggal_lahir' => '1995-01-01',
            'alamat' => 'Jl. Uji',
            'no_telp' => '081234567890',
        ]);

        return $patient->fresh('pasien');
    }
}
