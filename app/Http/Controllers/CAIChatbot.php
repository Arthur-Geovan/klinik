<?php

namespace App\Http\Controllers;

use App\Models\MJadwalPraktik;
use App\Models\MReservasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CAIChatbot extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->role === 'pasien', 403);

        return view('chatbot.index');
    }

    public function message(Request $request)
    {
        abort_unless(auth()->user()->role === 'pasien', 403);

        $validated = $request->validate([
            'message' => ['required', 'string', 'min:2', 'max:1000'],
        ], [
            'message.required' => 'Pertanyaan wajib diisi.',
            'message.max' => 'Pertanyaan maksimal 1000 karakter.',
        ]);

        return response()->json([
            'reply' => $this->replyTo($validated['message']),
        ]);
    }

    private function replyTo(string $message): string
    {
        $normalized = Str::lower($message);

        if ($this->containsAny($normalized, [
            'sesak', 'nyeri dada', 'tidak sadar', 'pingsan', 'kejang',
            'pendarahan hebat', 'perdarahan hebat', 'sulit bernapas',
        ])) {
            return 'Keluhan ini dapat menjadi tanda keadaan darurat. Segera hubungi 112 atau datang ke IGD terdekat. Jangan menunggu balasan chat ini dan jangan menyetir sendiri jika kondisi berat.';
        }

        if ($this->isReservationStatusQuestion($normalized)) {
            return $this->getReservationStatusReply();
        }

        $aiReply = $this->tryAiReply($message);
        if ($aiReply) {
            return $aiReply;
        }

        if ($this->containsAny($normalized, ['jadwal', 'jam praktik', 'dokter buka', 'dokter praktek', 'praktik'])) {
            return $this->getScheduleReply($normalized);
        }

        if ($this->containsAny($normalized, ['reservasi', 'buat janji', 'daftar dokter', 'appointment', 'booking'])) {
            return 'Untuk membuat janji, buka menu Reservasi lalu pilih dokter, jadwal praktik, dan tanggal yang tersedia. Jika Anda sudah membuat reservasi, statusnya dapat dilihat di halaman tersebut.';
        }

        if ($this->containsAny($normalized, ['poli', 'spesialis'])) {
            return 'Silakan pilih poli berdasarkan keluhan Anda di halaman Reservasi. Jika ragu atau keluhan Anda memburuk, konsultasikan langsung dengan tenaga medis.';
        }

        if ($this->containsAny($normalized, ['obat', 'minum obat', 'dosis'])) {
            return 'Saya tidak dapat menentukan obat atau dosis secara aman melalui chat. Gunakan obat sesuai resep dokter dan hubungi klinik apabila ada efek samping atau keluhan berlanjut.';
        }

        if ($this->containsAny($normalized, ['halo', 'hai', 'hi', 'pagi', 'siang', 'sore', 'malam'])) {
            return 'Halo! Saya asisten informasi e-Klinik. Anda dapat bertanya tentang reservasi, jadwal dokter, poli, atau persiapan kunjungan.';
        }

        return 'Saya belum dapat menjawab pertanyaan itu dengan tepat. Untuk diagnosis atau konsultasi medis, silakan buat reservasi dan sampaikan keluhan Anda langsung kepada dokter. Jika ada tanda darurat, segera hubungi 112 atau datang ke IGD terdekat.';
    }

    private function tryAiReply(string $message): ?string
    {
        if (!(bool) config('services.ai.enabled', false)) {
            return null;
        }

        $provider = config('services.ai.provider', 'openai');
        $apiKey = config('services.ai.api_key');

        if (empty($apiKey)) {
            return null;
        }

        try {
            $context = $this->buildAiContext();

            if ($provider === 'gemini') {
                $model = config('services.ai.model') ?: 'gemini-3.8-flash';
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $apiKey, [
                    'contents' => [[
                        'parts' => [[
                            'text' => "Kamu adalah asisten AI klinik Indonesia. Jawab singkat, jelas, dan aman. Berikut konteks klinik:\n\n{$context}\n\nPertanyaan pasien: {$message}.",
                        ]],
                    ]],
                ]);

                if (!$response->successful()) {
                    return null;
                }

                return trim(data_get($response->json(), 'candidates.0.content.parts.0.text') ?? '');
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => config('services.ai.model') ?: 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => "Kamu adalah asisten AI klinik Indonesia yang menjawab pertanyaan pasien tentang jadwal dokter, reservasi, dan informasi umum. Jawab singkat, jelas, dan profesional. Hindari diagnosis medis yang pasti. Jika ada tanda darurat, sarankan agar pasien segera ke IGD atau hubungi 112."],
                    ['role' => 'user', 'content' => "Konteks klinik:\n{$context}\n\nPertanyaan pasien:\n{$message}"],
                ],
                'temperature' => 0.4,
            ]);

            if (!$response->successful()) {
                return null;
            }

            return trim(data_get($response->json(), 'choices.0.message.content') ?? '');
        } catch (\Throwable $e) {
            \Log::warning('AI chatbot request failed: ' . $e->getMessage());

            return null;
        }
    }

    private function buildAiContext(): string
    {
        $schedules = MJadwalPraktik::with(['dokter.user', 'dokter.poli'])->latest()->limit(10)->get();

        $scheduleText = $schedules->map(function ($schedule) {
            $dokterName = $schedule->dokter?->user?->name ?? 'Dokter';
            $poliName = $schedule->dokter?->poli?->nama_poli ?? 'Poli';
            $jamMulai = \Carbon\Carbon::parse($schedule->jam_mulai)->format('H:i');
            $jamSelesai = \Carbon\Carbon::parse($schedule->jam_selesai)->format('H:i');

            return "$dokterName ($poliName) - $schedule->hari $jamMulai-$jamSelesai, kuota $schedule->kuota";
        })->implode('; ');

        return "Jadwal dokter saat ini: {$scheduleText}.";
    }

    private function isReservationStatusQuestion(string $message): bool
    {
        return $this->containsAny($message, [
            'status reservasi', 'reservasi saya', 'cek reservasi', 'status saya',
            'cek status', 'reservasiku', 'status janji', 'jadwal saya',
        ]);
    }

    private function getReservationStatusReply(): string
    {
        $patient = auth()->user()->pasien;

        if (!$patient) {
            return 'Data pasien Anda belum tersedia. Silakan lengkapi profil pasien untuk melihat status reservasi.';
        }

        $reservations = MReservasi::with(['dokter.user', 'jadwalPraktik'])
            ->where('pasien_id', $patient->id)
            ->latest()
            ->limit(3)
            ->get();

        if ($reservations->isEmpty()) {
            return 'Anda belum memiliki reservasi aktif. Silakan buka menu Reservasi untuk membuat janji baru.';
        }

        $details = $reservations->map(function ($reservation) {
            $dokter = $reservation->dokter?->user?->name ?? 'Dokter';
            $tanggal = $reservation->tanggal_reservasi ? \Carbon\Carbon::parse($reservation->tanggal_reservasi)->translatedFormat('d F Y') : '-';
            $status = ucfirst($reservation->status ?? 'pending');

            return "$dokter - $tanggal - status: $status";
        })->implode('; ');

        return "Status reservasi Anda: $details.";
    }

    private function getScheduleReply(string $message): string
    {
        $day = null;
        foreach (['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'] as $dayName) {
            if (Str::contains($message, $dayName)) {
                $day = $dayName;
                break;
            }
        }

        $query = MJadwalPraktik::with(['dokter.user', 'dokter.poli']);

        if ($day) {
            $query = $query->where('hari', 'like', '%' . ucfirst($day) . '%');
        }

        $specialty = null;
        foreach (['umum', 'gigi', 'mata', 'kulit', 'anak', 'tht', 'jantung', 'saraf'] as $specialtyName) {
            if (Str::contains($message, $specialtyName)) {
                $specialty = $specialtyName;
                break;
            }
        }

        if ($specialty) {
            $query = $query->whereHas('dokter', function ($dokterQuery) use ($specialty) {
                $dokterQuery->where('spesialisasi', 'like', '%' . ucfirst($specialty) . '%')
                    ->orWhereHas('poli', function ($poliQuery) use ($specialty) {
                        $poliQuery->where('nama_poli', 'like', '%' . ucfirst($specialty) . '%');
                    });
            });
        }

        $schedules = $query->limit(5)->get();

        if ($schedules->isEmpty()) {
            return 'Belum ada jadwal dokter yang sesuai dengan pencarian Anda. Silakan buka menu Reservasi untuk melihat jadwal yang tersedia.';
        }

        $details = $schedules->map(function ($schedule) {
            $dokterName = $schedule->dokter?->user?->name ?? 'Dokter';
            $poliName = $schedule->dokter?->poli?->nama_poli ?? 'Poli';
            $jamMulai = \Carbon\Carbon::parse($schedule->jam_mulai)->format('H:i');
            $jamSelesai = \Carbon\Carbon::parse($schedule->jam_selesai)->format('H:i');

            return "$dokterName ($poliName) - $schedule->hari $jamMulai-$jamSelesai, kuota $schedule->kuota";
        })->implode('; ');

        return "Jadwal yang tersedia: $details.";
    }

    private function containsAny(string $message, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (Str::contains($message, $keyword)) {
                return true;
            }
        }

        return false;
    }
}
