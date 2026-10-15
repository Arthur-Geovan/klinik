<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\MDokter;
use App\Models\MPoli;
use App\Models\MUser;
use Illuminate\Support\Facades\Hash;

$desiredDoctors = [
    ['dr. Budi Santoso, Sp.PD', 'Poli Umum', 'STR-123456789', 'Spesialis Penyakit Dalam'],
    ['dr. Siti Rahma, Sp.A', 'Poli Anak', 'STR-234567890', 'Spesialis Anak'],
    ['dr. Andi Wijaya, Sp.GK', 'Poli Gizi', 'STR-345678901', 'Spesialis Gizi Klinik'],
    ['dr. Rina Maharani, Sp.OG', 'Poli Kandungan', 'STR-456789012', 'Spesialis Obstetri dan Ginekologi'],
    ['dr. Fajar Nugroho, Sp.M', 'Poli Mata', 'STR-567890123', 'Spesialis Mata'],
    ['dr. Maya Lestari, Sp.THT', 'Poli THT', 'STR-678901234', 'Spesialis Telinga Hidung Tenggorokan'],
    ['dr. Dimas Pratama, Sp.KK', 'Poli Kulit dan Kelamin', 'STR-789012345', 'Spesialis Kulit dan Kelamin'],
    ['dr. Nisa Kurnia, Sp.S', 'Poli Saraf', 'STR-890123456', 'Spesialis Saraf'],
    ['dr. Hendra Saputra, Sp.JP', 'Poli Jantung', 'STR-901234567', 'Spesialis Jantung dan Pembuluh Darah'],
    ['dr. Taufik Hidayat, Sp.P', 'Poli Paru', 'STR-012345678', 'Spesialis Paru'],
    ['dr. Citra Permata, Sp.B', 'Poli Bedah', 'STR-112233445', 'Spesialis Bedah'],
    ['dr. Arif Setiawan, Sp.KFR', 'Poli Fisioterapi', 'STR-223344556', 'Spesialis Kedokteran Fisik dan Rehabilitasi'],
    ['dr. Laila Anindita, M.Psi', 'Poli Psikologi', 'STR-334455667', 'Psikolog Klinis'],
    ['dr. Galih Pratama, Sp.OT', 'Poli Bedah', 'STR-445566778', 'Spesialis Orthopedi'],
    ['dr. Novita Sari, Sp.PD', 'Poli Umum', 'STR-556677889', 'Spesialis Penyakit Dalam'],
    ['dr. Rasyid Ramadhan, Sp.A', 'Poli Anak', 'STR-667788990', 'Spesialis Anak'],
    ['dr. Della Putri, Sp.OG', 'Poli Kandungan', 'STR-778899001', 'Spesialis Obstetri dan Ginekologi'],
    ['dr. Irfan Maulana, Sp.M', 'Poli Mata', 'STR-889900112', 'Spesialis Mata'],
    ['dr. Yulia Fitri, Sp.THT', 'Poli THT', 'STR-990011223', 'Spesialis Telinga Hidung Tenggorokan'],
    ['dr. Rahman Hidayat, Sp.KK', 'Poli Kulit dan Kelamin', 'STR-101112233', 'Spesialis Kulit dan Kelamin'],
    ['dr. Siska Wulandari, Sp.S', 'Poli Saraf', 'STR-121314151', 'Spesialis Saraf'],
    ['dr. Agung Wijaya, Sp.JP', 'Poli Jantung', 'STR-131415161', 'Spesialis Jantung dan Pembuluh Darah'],
    ['dr. Putri Handayani, Sp.P', 'Poli Paru', 'STR-141516171', 'Spesialis Paru'],
    ['dr. Damar Susilo, Sp.B', 'Poli Bedah', 'STR-151617181', 'Spesialis Bedah'],
    ['dr. Eni Pratiwi, Sp.KFR', 'Poli Fisioterapi', 'STR-161718191', 'Spesialis Kedokteran Fisik dan Rehabilitasi'],
    ['dr. Miftahul Huda, M.Psi', 'Poli Psikologi', 'STR-171819202', 'Psikolog Klinis'],
];

$existingDoctorNames = MDokter::with('user')->get()->pluck('user.name')->filter()->all();
$existingNoStr = MDokter::pluck('no_str')->filter()->all();

$countAdded = 0;

foreach ($desiredDoctors as [$nama, $poliName, $noStr, $spesialisasi]) {
    if (in_array($noStr, $existingNoStr, true)) {
        continue;
    }

    $poli = MPoli::where('nama_poli', $poliName)->first();
    if (! $poli) {
        continue;
    }

    $email = strtolower(str_replace(['.', ',', ' ', '(', ')', '/', '\\', 'Sp', 'dr'], '', $nama));
    $email = preg_replace('/[^a-z0-9]+/', '', $email) . '@klinik.com';

    $user = MUser::firstOrCreate(
        ['email' => $email],
        [
            'name' => $nama,
            'password' => Hash::make('password'),
            'role' => 'dokter',
        ]
    );

    if (! MDokter::where('user_id', $user->id)->exists()) {
        MDokter::create([
            'user_id' => $user->id,
            'poli_id' => $poli->id,
            'no_str' => $noStr,
            'spesialisasi' => $spesialisasi,
            'foto' => null,
        ]);
        $countAdded++;
    }

    $existingNoStr[] = $noStr;
}

echo 'Added/ensured doctors: ' . MDokter::count() . PHP_EOL;
