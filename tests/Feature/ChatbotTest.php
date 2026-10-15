<?php

namespace Tests\Feature;

use App\Models\MDokter;
use App\Models\MJadwalPraktik;
use App\Models\MPasien;
use App\Models\MPoli;
use App\Models\MReservasi;
use App\Models\MUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ChatbotTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_open_chatbot(): void
    {
        $patient = $this->createUser('pasien');

        $this->actingAs($patient)
            ->get('/chatbot')
            ->assertOk()
            ->assertSee('Tanya AI Klinik');
    }

    public function test_chatbot_prioritizes_emergency_warning(): void
    {
        $patient = $this->createUser('pasien');

        $this->actingAs($patient)
            ->postJson('/chatbot/message', ['message' => 'Saya mengalami sesak napas'])
            ->assertOk()
            ->assertJsonPath('reply', 'Keluhan ini dapat menjadi tanda keadaan darurat. Segera hubungi 112 atau datang ke IGD terdekat. Jangan menunggu balasan chat ini dan jangan menyetir sendiri jika kondisi berat.');
    }

    public function test_chatbot_can_answer_available_doctor_schedule_from_database(): void
    {
        $patient = $this->createUser('pasien', 'pasien-schedule@test.com');
        $this->createPasienForUser($patient);

        $poli = MPoli::create(['nama_poli' => 'Umum']);
        $dokterUser = $this->createUser('dokter', 'dokter-schedule@test.com', 'Dr. Rina');
        $dokter = MDokter::create([
            'user_id' => $dokterUser->id,
            'poli_id' => $poli->id,
            'no_str' => 'STR-001',
            'spesialisasi' => 'Dokter Umum',
        ]);
        MJadwalPraktik::create([
            'dokter_id' => $dokter->id,
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '12:00:00',
            'kuota' => 10,
        ]);

        $this->actingAs($patient)
            ->postJson('/chatbot/message', ['message' => 'jadwal dokter umum hari senin'])
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('reply', fn ($reply) => Str::contains(Str::lower($reply), 'dr. rina')
                    && Str::contains($reply, '08:00'))
            );
    }

    public function test_chatbot_can_report_patient_reservation_status(): void
    {
        config()->set('services.ai.provider', 'openai');
        config()->set('services.ai.enabled', true);
        config()->set('services.ai.api_key', 'test-key');
        Http::fake();

        $patient = $this->createUser('pasien', 'pasien-status@test.com');
        $pasien = $this->createPasienForUser($patient);

        $poli = MPoli::create(['nama_poli' => 'Umum']);
        $dokterUser = $this->createUser('dokter', 'dokter-status@test.com', 'Dr. Budi');
        $dokter = MDokter::create([
            'user_id' => $dokterUser->id,
            'poli_id' => $poli->id,
            'no_str' => 'STR-002',
            'spesialisasi' => 'Dokter Umum',
        ]);
        $jadwal = MJadwalPraktik::create([
            'dokter_id' => $dokter->id,
            'hari' => 'Selasa',
            'jam_mulai' => '09:00:00',
            'jam_selesai' => '11:00:00',
            'kuota' => 8,
        ]);
        MReservasi::create([
            'pasien_id' => $pasien->id,
            'dokter_id' => $dokter->id,
            'jadwal_praktik_id' => $jadwal->id,
            'tanggal_reservasi' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $this->actingAs($patient)
            ->postJson('/chatbot/message', ['message' => 'status reservasi saya'])
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('reply', fn ($reply) => Str::contains(Str::lower($reply), 'dr. budi')
                    && Str::contains(Str::lower($reply), 'pending'))
            );

            Http::assertNothingSent();
    }

    public function test_chatbot_uses_ai_api_when_configured(): void
    {
        config()->set('services.ai.provider', 'openai');
        config()->set('services.ai.enabled', true);
        config()->set('services.ai.api_key', 'test-key');

        Http::fake([
            'https://api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [[
                    'message' => ['content' => 'Jawaban AI dari OpenAI untuk jadwal dokter.'],
                ]],
            ], 200),
        ]);

        $patient = $this->createUser('pasien', 'pasien-ai@test.com');
        $otherPatientUser = $this->createUser('pasien', 'other-patient@test.com', 'Sensitive Other Patient');
        $otherPatient = $this->createPasienForUser($otherPatientUser);
        $poli = MPoli::create(['nama_poli' => 'Umum']);
        $doctorUser = $this->createUser('dokter', 'doctor-ai@test.com', 'Dr. AI Test');
        $doctor = MDokter::create([
            'user_id' => $doctorUser->id,
            'poli_id' => $poli->id,
            'no_str' => 'STR-AI-001',
            'spesialisasi' => 'Dokter Umum',
        ]);
        $schedule = MJadwalPraktik::create([
            'dokter_id' => $doctor->id,
            'hari' => 'Selasa',
            'jam_mulai' => '09:00:00',
            'jam_selesai' => '11:00:00',
            'kuota' => 8,
        ]);
        MReservasi::create([
            'pasien_id' => $otherPatient->id,
            'dokter_id' => $doctor->id,
            'jadwal_praktik_id' => $schedule->id,
            'tanggal_reservasi' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $this->actingAs($patient)
            ->postJson('/chatbot/message', ['message' => 'Berikan informasi jadwal dokter hari selasa'])
            ->assertOk()
            ->assertJsonPath('reply', 'Jawaban AI dari OpenAI untuk jadwal dokter.');

        Http::assertSent(fn ($request) => !Str::contains($request->body(), 'Sensitive Other Patient')
            && !Str::contains($request->body(), 'status pending'));
    }

    public function test_chatbot_uses_the_configured_gemini_model(): void
    {
        config()->set('services.ai.provider', 'gemini');
        config()->set('services.ai.enabled', true);
        config()->set('services.ai.api_key', 'test-key');
        config()->set('services.ai.model', 'gemini-3.8-flash');

        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.8-flash:generateContent*' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [['text' => 'Gemini siap membantu.']]],
                ]],
            ], 200),
        ]);

        $patient = $this->createUser('pasien', 'pasien-gemini@test.com');

        $this->actingAs($patient)
            ->postJson('/chatbot/message', ['message' => 'Apa yang perlu disiapkan sebelum berkunjung?'])
            ->assertOk()
            ->assertJsonPath('reply', 'Gemini siap membantu.');

        Http::assertSent(fn ($request) => Str::contains($request->url(), '/models/gemini-3.8-flash:generateContent'));
    }

    public function test_non_patient_cannot_use_chatbot(): void
    {
        $admin = $this->createUser('admin');

        $this->actingAs($admin)
            ->postJson('/chatbot/message', ['message' => 'Halo'])
            ->assertForbidden();
    }

    private function createUser(string $role, ?string $email = null, ?string $name = null): MUser
    {
        return MUser::create([
            'name' => $name ?? ucfirst($role).' Chatbot',
            'email' => $email ?? $role.'-chatbot@test.com',
            'password' => Hash::make('password'),
            'role' => $role,
        ]);
    }

    private function createPasienForUser(MUser $user): MPasien
    {
        return MPasien::create([
            'user_id' => $user->id,
            'no_rm' => 'RM-' . uniqid(),
            'nik' => (string) random_int(1000000000000000, 9999999999999999),
            'tanggal_lahir' => '1995-01-01',
            'alamat' => 'Jl. Test No. 123',
            'no_telp' => '081234567890',
        ]);
    }
}
