<?php

namespace Database\Seeders;

use App\Models\MDokter;
use App\Models\MJadwalPraktik;
use App\Models\MObat;
use App\Models\MPasien;
use App\Models\MPoli;
use App\Models\MRekamMedis;
use App\Models\MReservasi;
use App\Models\MUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        MJadwalPraktik::query()->delete();
        MUser::query()->delete();
        MPasien::query()->delete();
        MDokter::query()->delete();
        MPoli::query()->delete();
        MObat::query()->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Seed Data Poli
        $poliUmum = MPoli::create([
            'nama_poli' => 'Poli Umum',
            'deskripsi' => 'Layanan pemeriksaan kesehatan umum dan pertolongan pertama.',
        ]);

        $poliGigi = MPoli::create([
            'nama_poli' => 'Poli Gigi',
            'deskripsi' => 'Layanan kesehatan gigi dan mulut.',
        ]);

        $poliAnak = MPoli::create([
            'nama_poli' => 'Poli Anak',
            'deskripsi' => 'Layanan kesehatan khusus bayi dan anak-anak.',
        ]);

        $poliTambahan = [
            ['Poli Kandungan', 'Layanan pemeriksaan kehamilan dan kesehatan reproduksi wanita.'],
            ['Poli Mata', 'Layanan pemeriksaan dan perawatan kesehatan mata.'],
            ['Poli THT', 'Layanan pemeriksaan telinga, hidung, dan tenggorokan.'],
            ['Poli Kulit dan Kelamin', 'Layanan pemeriksaan kesehatan kulit dan kelamin.'],
            ['Poli Saraf', 'Layanan pemeriksaan gangguan saraf dan sistem neurologis.'],
            ['Poli Jantung', 'Layanan pemeriksaan dan konsultasi kesehatan jantung.'],
            ['Poli Paru', 'Layanan pemeriksaan dan perawatan kesehatan paru-paru.'],
            ['Poli Bedah', 'Layanan konsultasi dan tindakan bedah.'],
            ['Poli Gizi', 'Layanan konsultasi gizi dan pola makan sehat.'],
            ['Poli Lansia', 'Layanan pemeriksaan kesehatan khusus lanjut usia.'],
            ['Poli Fisioterapi', 'Layanan pemulihan gerak dan fungsi tubuh.'],
            ['Poli Psikologi', 'Layanan konsultasi kesehatan mental dan psikologi.'],
        ];

        foreach ($poliTambahan as [$nama, $deskripsi]) {
            MPoli::create([
                'nama_poli' => $nama,
                'deskripsi' => $deskripsi,
            ]);
        }

        // 2. Seed User ADMIN
        MUser::create([
            'name'          => 'Administrator Utama',
            'email'         => 'admin@klinik.com',
            'password'      => Hash::make('password'),
            'login_password' => 'password',
            'role'          => 'admin',
        ]);

        // 3. Seed User DOKTER & Profile Dokter (minimal 3 akun doktor)
        $userDokter = MUser::create([
            'name'          => 'dr. Budi Santoso, Sp.PD',
            'email'         => 'dokter@klinik.com',
            'password'      => Hash::make('password'),
            'login_password' => 'password',
            'role'          => 'dokter',
        ]);

        MDokter::create([
            'user_id' => $userDokter->id,
            'poli_id' => $poliUmum->id,
            'no_str' => 'STR-123456789',
            'spesialisasi' => 'Spesialis Penyakit Dalam',
            'foto' => null,
        ]);

        $dokterDaftar = [
            ['name' => 'dr. Budi Santoso, Sp.PD', 'email' => 'budi.santoso@klinik.com', 'poli' => 'Poli Umum', 'no_str' => 'STR-1001', 'spesialisasi' => 'Spesialis Penyakit Dalam', 'hari' => ['Senin','Selasa','Rabu','Kamis','Jumat'], 'jam_mulai' => '08:00:00', 'jam_selesai' => '12:00:00', 'kuota' => 8],
            ['name' => 'dr. Siti Rahma, Sp.A', 'email' => 'siti.rahma@klinik.com', 'poli' => 'Poli Anak', 'no_str' => 'STR-1002', 'spesialisasi' => 'Spesialis Anak', 'hari' => ['Rabu','Kamis','Jumat','Sabtu','Minggu'], 'jam_mulai' => '09:00:00', 'jam_selesai' => '12:00:00', 'kuota' => 7],
            ['name' => 'dr. Andi Wijaya, Sp.GK', 'email' => 'andi.wijaya@klinik.com', 'poli' => 'Poli Gizi', 'no_str' => 'STR-1003', 'spesialisasi' => 'Spesialis Gizi Klinik', 'hari' => ['Senin','Selasa','Rabu','Kamis','Jumat'], 'jam_mulai' => '10:00:00', 'jam_selesai' => '13:00:00', 'kuota' => 6],
            ['name' => 'dr. Rina Maharani, Sp.OG', 'email' => 'rina.maharani@klinik.com', 'poli' => 'Poli Kandungan', 'no_str' => 'STR-1004', 'spesialisasi' => 'Spesialis Obstetri dan Ginekologi', 'hari' => ['Senin','Selasa','Rabu','Kamis','Jumat'], 'jam_mulai' => '08:30:00', 'jam_selesai' => '12:00:00', 'kuota' => 8],
            ['name' => 'dr. Fajar Nugroho, Sp.M', 'email' => 'fajar.nugroho@klinik.com', 'poli' => 'Poli Mata', 'no_str' => 'STR-1005', 'spesialisasi' => 'Spesialis Mata', 'hari' => ['Senin','Selasa','Rabu','Kamis','Jumat'], 'jam_mulai' => '13:00:00', 'jam_selesai' => '16:00:00', 'kuota' => 5],
            ['name' => 'dr. Maya Lestari, Sp.THT', 'email' => 'maya.lestari@klinik.com', 'poli' => 'Poli THT', 'no_str' => 'STR-1006', 'spesialisasi' => 'Spesialis Telinga Hidung Tenggorokan', 'hari' => ['Senin','Rabu','Kamis','Jumat','Sabtu'], 'jam_mulai' => '09:00:00', 'jam_selesai' => '12:30:00', 'kuota' => 6],
            ['name' => 'dr. Dimas Pratama, Sp.KK', 'email' => 'dimas.pratama@klinik.com', 'poli' => 'Poli Kulit dan Kelamin', 'no_str' => 'STR-1007', 'spesialisasi' => 'Spesialis Kulit dan Kelamin', 'hari' => ['Selasa','Rabu','Kamis','Jumat','Sabtu'], 'jam_mulai' => '13:30:00', 'jam_selesai' => '17:00:00', 'kuota' => 7],
            ['name' => 'dr. Nisa Kurnia, Sp.S', 'email' => 'nisa.kurnia@klinik.com', 'poli' => 'Poli Saraf', 'no_str' => 'STR-1008', 'spesialisasi' => 'Spesialis Saraf', 'hari' => ['Senin','Selasa','Rabu','Kamis','Jumat'], 'jam_mulai' => '08:00:00', 'jam_selesai' => '11:00:00', 'kuota' => 6],
            ['name' => 'dr. Hendra Saputra, Sp.JP', 'email' => 'hendra.saputra@klinik.com', 'poli' => 'Poli Jantung', 'no_str' => 'STR-1009', 'spesialisasi' => 'Spesialis Jantung dan Pembuluh Darah', 'hari' => ['Senin','Selasa','Rabu','Kamis','Jumat'], 'jam_mulai' => '10:00:00', 'jam_selesai' => '14:00:00', 'kuota' => 8],
            ['name' => 'dr. Taufik Hidayat, Sp.P', 'email' => 'taufik.hidayat@klinik.com', 'poli' => 'Poli Paru', 'no_str' => 'STR-1010', 'spesialisasi' => 'Spesialis Paru', 'hari' => ['Senin','Selasa','Rabu','Kamis','Jumat'], 'jam_mulai' => '08:00:00', 'jam_selesai' => '11:00:00', 'kuota' => 5],
            ['name' => 'dr. Citra Permata, Sp.B', 'email' => 'citra.permata@klinik.com', 'poli' => 'Poli Bedah', 'no_str' => 'STR-1011', 'spesialisasi' => 'Spesialis Bedah', 'hari' => ['Senin','Selasa','Rabu','Kamis','Jumat'], 'jam_mulai' => '09:00:00', 'jam_selesai' => '12:00:00', 'kuota' => 6],
            ['name' => 'dr. Arif Setiawan, Sp.KFR', 'email' => 'arif.setiawan@klinik.com', 'poli' => 'Poli Fisioterapi', 'no_str' => 'STR-1012', 'spesialisasi' => 'Spesialis Kedokteran Fisik dan Rehabilitasi', 'hari' => ['Senin','Rabu','Kamis','Jumat','Sabtu'], 'jam_mulai' => '08:00:00', 'jam_selesai' => '11:30:00', 'kuota' => 7],
            ['name' => 'dr. Laila Anindita, M.Psi', 'email' => 'laila.anindita@klinik.com', 'poli' => 'Poli Psikologi', 'no_str' => 'STR-1013', 'spesialisasi' => 'Psikolog Klinis', 'hari' => ['Selasa','Rabu','Kamis','Jumat','Sabtu'], 'jam_mulai' => '10:00:00', 'jam_selesai' => '15:00:00', 'kuota' => 6],
            ['name' => 'dr. Pradana Wibowo, Sp.ENT', 'email' => 'pradana.wibowo@klinik.com', 'poli' => 'Poli THT', 'no_str' => 'STR-1014', 'spesialisasi' => 'Spesialis THT', 'hari' => ['Senin','Selasa','Kamis','Jumat','Minggu'], 'jam_mulai' => '09:30:00', 'jam_selesai' => '13:00:00', 'kuota' => 6],
            ['name' => 'dr. Eka Putri, Sp.OG', 'email' => 'eka.putri@klinik.com', 'poli' => 'Poli Kandungan', 'no_str' => 'STR-1015', 'spesialisasi' => 'Spesialis Kebidanan', 'hari' => ['Selasa','Rabu','Kamis','Sabtu','Minggu'], 'jam_mulai' => '08:00:00', 'jam_selesai' => '11:30:00', 'kuota' => 7],
            ['name' => 'dr. Yuda Saputra, Sp.U', 'email' => 'yuda.saputra@klinik.com', 'poli' => 'Poli Umum', 'no_str' => 'STR-1016', 'spesialisasi' => 'Spesialis Urologi', 'hari' => ['Senin','Rabu','Jumat','Sabtu','Minggu'], 'jam_mulai' => '07:30:00', 'jam_selesai' => '11:00:00', 'kuota' => 5],
            ['name' => 'dr. Gita Puspita, Sp.A', 'email' => 'gita.puspita@klinik.com', 'poli' => 'Poli Anak', 'no_str' => 'STR-1017', 'spesialisasi' => 'Spesialis Anak', 'hari' => ['Selasa','Rabu','Kamis','Jumat','Sabtu'], 'jam_mulai' => '08:30:00', 'jam_selesai' => '12:30:00', 'kuota' => 6],
            ['name' => 'dr. Rendra Kusuma, Sp.Paru', 'email' => 'rendra.kusuma@klinik.com', 'poli' => 'Poli Paru', 'no_str' => 'STR-1018', 'spesialisasi' => 'Spesialis Paru', 'hari' => ['Senin','Selasa','Rabu','Kamis','Jumat'], 'jam_mulai' => '09:00:00', 'jam_selesai' => '13:00:00', 'kuota' => 6],
            ['name' => 'dr. Mita Ardianti, Sp.Rad', 'email' => 'mita.ardianti@klinik.com', 'poli' => 'Poli Gigi', 'no_str' => 'STR-1019', 'spesialisasi' => 'Spesialis Radiologi', 'hari' => ['Senin','Rabu','Kamis','Jumat','Sabtu'], 'jam_mulai' => '11:00:00', 'jam_selesai' => '15:00:00', 'kuota' => 5],
            ['name' => 'dr. Farid Rahman, Sp.Jantung', 'email' => 'farid.rahman@klinik.com', 'poli' => 'Poli Jantung', 'no_str' => 'STR-1020', 'spesialisasi' => 'Spesialis Jantung', 'hari' => ['Selasa','Rabu','Kamis','Jumat','Sabtu'], 'jam_mulai' => '08:00:00', 'jam_selesai' => '12:00:00', 'kuota' => 7],
            ['name' => 'dr. Salsabila Noor, Sp.KIA', 'email' => 'salsabila.noor@klinik.com', 'poli' => 'Poli Kandungan', 'no_str' => 'STR-1021', 'spesialisasi' => 'Spesialis KIA', 'hari' => ['Senin','Selasa','Rabu','Kamis','Jumat'], 'jam_mulai' => '09:00:00', 'jam_selesai' => '13:00:00', 'kuota' => 7],
            ['name' => 'dr. Bagus Santika, Sp.THT', 'email' => 'bagus.santika@klinik.com', 'poli' => 'Poli THT', 'no_str' => 'STR-1022', 'spesialisasi' => 'Spesialis THT', 'hari' => ['Senin','Rabu','Kamis','Jumat','Minggu'], 'jam_mulai' => '10:00:00', 'jam_selesai' => '14:00:00', 'kuota' => 6],
            ['name' => 'dr. Dinda Ayu, Sp.Usus', 'email' => 'dinda.ayu@klinik.com', 'poli' => 'Poli Umum', 'no_str' => 'STR-1023', 'spesialisasi' => 'Spesialis Gastroenterologi', 'hari' => ['Selasa','Rabu','Kamis','Jumat','Sabtu'], 'jam_mulai' => '08:30:00', 'jam_selesai' => '12:30:00', 'kuota' => 6],
            ['name' => 'dr. Ilham Ramadhan, Sp.Gigi', 'email' => 'ilham.ramadhan@klinik.com', 'poli' => 'Poli Gigi', 'no_str' => 'STR-1024', 'spesialisasi' => 'Spesialis Gigi', 'hari' => ['Senin','Selasa','Rabu','Kamis','Jumat'], 'jam_mulai' => '08:00:00', 'jam_selesai' => '12:00:00', 'kuota' => 7],
            ['name' => 'dr. Nova Rahma, Sp.Kes', 'email' => 'nova.rahma@klinik.com', 'poli' => 'Poli Gizi', 'no_str' => 'STR-1025', 'spesialisasi' => 'Spesialis Kesehatan Masyarakat', 'hari' => ['Selasa','Kamis','Jumat','Sabtu','Minggu'], 'jam_mulai' => '09:00:00', 'jam_selesai' => '13:00:00', 'kuota' => 5],
            ['name' => 'dr. Hanif Putra, Sp.Anak', 'email' => 'hanif.putra@klinik.com', 'poli' => 'Poli Anak', 'no_str' => 'STR-1026', 'spesialisasi' => 'Spesialis Anak', 'hari' => ['Senin','Selasa','Rabu','Kamis','Jumat'], 'jam_mulai' => '08:00:00', 'jam_selesai' => '11:30:00', 'kuota' => 6],
        ];

        foreach ($dokterDaftar as $dokterData) {
            $poli = MPoli::firstOrCreate(
                ['nama_poli' => $dokterData['poli']],
                ['deskripsi' => 'Layanan ' . $dokterData['poli'] . ' untuk pemeriksaan dan konsultasi spesialis.']
            );

            $userDokter = MUser::create([
                'name' => $dokterData['name'],
                'email' => $dokterData['email'],
                'password' => Hash::make('password'),
                'login_password' => 'password',
                'role' => 'dokter',
            ]);

            $dokter = MDokter::create([
                'user_id' => $userDokter->id,
                'poli_id' => $poli->id,
                'no_str' => $dokterData['no_str'],
                'spesialisasi' => $dokterData['spesialisasi'],
                'foto' => null,
            ]);

            foreach ($dokterData['hari'] as $hari) {
                MJadwalPraktik::create([
                    'dokter_id' => $dokter->id,
                    'hari' => $hari,
                    'jam_mulai' => $dokterData['jam_mulai'],
                    'jam_selesai' => $dokterData['jam_selesai'],
                    'kuota' => $dokterData['kuota'],
                ]);
            }
        }

        // 4. Seed User PASIEN & Profile Pasien (minimal 3 akun pasien)
        $userPasien = MUser::create([
            'name'          => 'Ahmad Rizki',
            'email'         => 'pasien@klinik.com',
            'password'      => Hash::make('password'),
            'login_password' => 'password',
            'role'          => 'pasien',
        ]);

        MPasien::create([
            'user_id' => $userPasien->id,
            'no_rm' => 'RM-2026-0001',
            'nik' => '6372010101950001',
            'tanggal_lahir' => '1995-05-15',
            'alamat' => 'Jl. Ahmad Yani No. 12, Banjarbaru',
            'no_telp' => '081234567890',
        ]);

        $pasienDummy = [
            ['Siti Nurhaliza', 'siti.nurhaliza@klinik.com', 'RM-2026-0002', '6372010101950002', '1997-04-12', 'Jl. Merdeka No. 8, Banjarbaru', '081234567891'],
            ['Budi Hartono', 'budi.hartono@klinik.com', 'RM-2026-0003', '6372010101950003', '1988-11-01', 'Jl. A. Yani No. 15, Landasan Ulin', '081234567892'],
            ['Dewi Lestari', 'dewi.lestari@klinik.com', 'RM-2026-0004', '6372010101950004', '1991-08-22', 'Jl. Cempaka No. 30, Banjarbaru', '081234567893'],
            ['Rizal Hidayat', 'rizal.hidayat@klinik.com', 'RM-2026-0005', '6372010101950005', '1994-02-19', 'Jl. Panglima Batur No. 44, Banjarbaru', '081234567894'],
            ['Maya Putri', 'maya.putri@klinik.com', 'RM-2026-0006', '6372010101950006', '2000-06-10', 'Jl. Sudirman No. 77, Banjarbaru', '081234567895'],
            ['Agus Pratama', 'agus.pratama@klinik.com', 'RM-2026-0007', '6372010101950007', '1986-12-03', 'Jl. Mistar Cokrokusumo No. 9, Banjarbaru', '081234567896'],
        ];

        foreach ($pasienDummy as [$nama, $email, $noRm, $nik, $tanggalLahir, $alamat, $noTelp]) {
            $userPasienDummy = MUser::create([
                'name'          => $nama,
                'email'         => $email,
                'password'      => Hash::make('password'),
                'login_password' => 'password',
                'role'          => 'pasien',
            ]);

            MPasien::create([
                'user_id' => $userPasienDummy->id,
                'no_rm' => $noRm,
                'nik' => $nik,
                'tanggal_lahir' => $tanggalLahir,
                'alamat' => $alamat,
                'no_telp' => $noTelp,
            ]);
        }

        $dokterIds = MDokter::query()->pluck('id')->toArray();
        $jadwalDokterIds = MJadwalPraktik::query()->pluck('id')->toArray();
        $statusReservasi = ['pending', 'dikonfirmasi', 'selesai'];

        $pasienIds = MPasien::query()->pluck('id')->toArray();
        $reservasiIds = [];
        for ($i = 0; $i < 6; $i++) {
            $dokterId = $dokterIds[$i % count($dokterIds)];
            $jadwalId = MJadwalPraktik::where('dokter_id', $dokterId)->value('id');

            if (! $jadwalId) {
                continue;
            }

            $reservasi = MReservasi::create([
                'pasien_id' => $pasienIds[$i % count($pasienIds)],
                'dokter_id' => $dokterId,
                'jadwal_praktik_id' => $jadwalId,
                'tanggal_reservasi' => now()->addDays($i + 1)->toDateString(),
                'status' => $statusReservasi[$i % count($statusReservasi)],
            ]);

            $reservasiIds[] = $reservasi->id;
        }

        $rekamDummy = [
            ['reservasi_id' => $reservasiIds[0] ?? null, 'keluhan' => 'Demam dan batuk selama 3 hari', 'diagnosa' => 'ISPA ringan', 'catatan' => 'Disarankan istirahat dan minum air hangat.'],
            ['reservasi_id' => $reservasiIds[1] ?? null, 'keluhan' => 'Nyeri kepala dan pusing', 'diagnosa' => 'Hipertensi ringan', 'catatan' => 'Pantau tekanan darah secara rutin.'],
            ['reservasi_id' => $reservasiIds[2] ?? null, 'keluhan' => 'Gatal pada kulit dan ruam', 'diagnosa' => 'Alergi ringan', 'catatan' => 'Hindari pemicu dan gunakan salep anti gatal.'],
        ];

        foreach ($rekamDummy as $data) {
            if (! $data['reservasi_id']) {
                continue;
            }

            MRekamMedis::create([
                'reservasi_id' => $data['reservasi_id'],
                'keluhan' => $data['keluhan'],
                'diagnosa' => $data['diagnosa'],
                'catatan' => $data['catatan'],
            ]);
        }

        // 5. Seed 1000 data obat dengan nama obat yang benar-benar real
        $obatReal = [
            ['Paracetamol', 'Tablet', 120, 4500],
            ['Amoxicillin', 'Kaplet', 90, 13500],
            ['Ibuprofen', 'Tablet', 110, 8500],
            ['Cetirizine', 'Tablet', 100, 7600],
            ['Omeprazole', 'Kapsul', 80, 14800],
            ['Metformin', 'Tablet', 70, 9800],
            ['Amlodipine', 'Tablet', 65, 15600],
            ['Simvastatin', 'Tablet', 48, 16300],
            ['Captopril', 'Tablet', 60, 14000],
            ['Furosemide', 'Tablet', 55, 8900],
            ['Glibenclamide', 'Tablet', 42, 9400],
            ['Prednisolone', 'Tablet', 58, 11000],
            ['Salbutamol', 'Tube', 62, 13000],
            ['Loratadine', 'Tablet', 76, 8200],
            ['Domperidone', 'Tablet', 40, 9700],
            ['Methylprednisolone', 'Tablet', 30, 17500],
            ['Vitamin C', 'Tablet', 200, 6200],
            ['Vitamin B Complex', 'Tablet', 180, 11600],
            ['Vitamin D3', 'Kapsul', 120, 13200],
            ['Calcium Carbonate', 'Tablet', 90, 8600],
            ['Magnesium Hydroxide', 'Tablet', 60, 7400],
            ['Antasida', 'Tablet', 70, 6800],
            ['Ranitidine', 'Tablet', 46, 7700],
            ['Lansoprazole', 'Kapsul', 50, 16800],
            ['Cefadroxil', 'Kaplet', 44, 19000],
            ['Cefixime', 'Kaplet', 38, 21000],
            ['Azithromycin', 'Tablet', 36, 24600],
            ['Ciprofloxacin', 'Tablet', 52, 18000],
            ['Levofloxacin', 'Tablet', 34, 22000],
            ['Clindamycin', 'Kapsul', 28, 21400],
            ['Erythromycin', 'Tablet', 32, 20600],
            ['Metronidazole', 'Tablet', 80, 9400],
            ['Albendazole', 'Tablet', 58, 9800],
            ['Ivermectin', 'Tablet', 32, 12600],
            ['Hydroxyzine', 'Tablet', 30, 11900],
            ['Ketoconazole', 'Tablet', 29, 17400],
            ['Fluconazole', 'Tablet', 36, 18200],
            ['Dexamethasone', 'Tablet', 48, 12100],
            ['Betamethasone', 'Tube', 24, 28400],
            ['Hydrocortisone', 'Tube', 22, 26500],
            ['Gentamicin', 'Botol', 12, 44000],
            ['Neomycin', 'Salep', 18, 26000],
            ['Chloramphenicol', 'Tetes', 16, 33000],
            ['Piroxicam', 'Tablet', 42, 19800],
            ['Meloxicam', 'Tablet', 38, 21400],
            ['Diclofenac', 'Tablet', 44, 13400],
            ['Naproxen', 'Tablet', 48, 14300],
            ['Ketorolac', 'Tablet', 26, 19600],
            ['Phenobarbital', 'Tablet', 34, 17100],
            ['Carbamazepine', 'Tablet', 30, 18800],
            ['Valproic Acid', 'Tablet', 24, 20600],
            ['Diazepam', 'Tablet', 30, 17500],
            ['Amitriptyline', 'Tablet', 20, 14800],
            ['Sertraline', 'Tablet', 22, 22300],
            ['Escitalopram', 'Tablet', 20, 24000],
            ['Alprazolam', 'Tablet', 18, 16200],
            ['Bromhexine', 'Sirup', 50, 9300],
            ['Guaifenesin', 'Sirup', 40, 8600],
            ['Ambroxol', 'Sirup', 55, 9200],
            ['Chlorpheniramine', 'Tablet', 75, 7200],
            ['Mupirocin', 'Salep', 18, 27200],
            ['Silver Sulfadiazine', 'Salep', 16, 32000],
            ['Retinol', 'Kapsul', 26, 22200],
            ['Tretinoin', 'Kapsul', 15, 28800],
            ['Minocycline', 'Kaplet', 20, 24100],
            ['Tetracycline', 'Kaplet', 18, 20300],
            ['Isotretinoin', 'Kapsul', 10, 38500],
            ['Orlistat', 'Kapsul', 26, 23400],
            ['Levothyroxine', 'Tablet', 36, 21000],
            ['Loperamide', 'Kaplet', 40, 7400],
            ['Bismuth Subsalicylate', 'Tablet', 28, 8800],
            ['Oralit', 'Sachet', 210, 4300],
            ['Multivitamin', 'Tablet', 120, 9800],
            ['Co-trimoxazole', 'Tablet', 45, 16400],
            ['Sulfamethoxazole', 'Tablet', 22, 17400],
            ['Trimethoprim', 'Tablet', 26, 15900],
            ['Acyclovir', 'Tablet', 20, 24700],
            ['Oseltamivir', 'Kapsul', 18, 28900],
            ['Sucralfate', 'Tablet', 32, 16800],
            ['Misoprostol', 'Tablet', 24, 21400],
            ['Propranolol', 'Tablet', 28, 14900],
            ['Losartan', 'Tablet', 30, 18200],
            ['Atorvastatin', 'Tablet', 34, 20500],
            ['Cefuroxime', 'Tablet', 20, 21900],
            ['Clarithromycin', 'Tablet', 18, 23700],
            ['Doxycycline', 'Kapsul', 24, 22800],
            ['Nitrofurantoin', 'Tablet', 28, 16900],
            ['Nifedipine', 'Tablet', 22, 15100],
            ['Bisoprolol', 'Tablet', 18, 17300],
            ['Pravastatin', 'Tablet', 19, 19600],
            ['Miconazole', 'Tablet', 26, 14200],
            ['Terfenadine', 'Tablet', 16, 12100],
            ['Cefalexin', 'Kaplet', 26, 19600],
            ['Pseudoephedrine', 'Tablet', 32, 8600],
            ['Triprolidine', 'Tablet', 30, 6800],
            ['Dextromethorphan', 'Sirup', 40, 7400],
            ['Glyceryl Guaiacolate', 'Sirup', 35, 9100],
            ['N-Acetylcysteine', 'Tablet', 18, 21200],
            ['Acetylsalicylic Acid', 'Tablet', 60, 7400],
            ['Colchicine', 'Tablet', 12, 20800],
            ['Allopurinol', 'Tablet', 28, 18200],
            ['Methotrexate', 'Tablet', 12, 26100],
            ['Sildenafil', 'Tablet', 14, 24400],
            ['Tadalafil', 'Tablet', 12, 28600],
            ['Finasteride', 'Tablet', 16, 22000],
            ['Minoxidil', 'Sirup', 18, 17200],
            ['Methyl Salicylate', 'Tube', 18, 8500],
            ['Olanzapine', 'Tablet', 18, 23800],
            ['Haloperidol', 'Tablet', 16, 20800],
            ['Risperidone', 'Tablet', 12, 25200],
            ['Cefixime', 'Tablet', 18, 22100],
            ['Aspirin', 'Tablet', 80, 5200],
            ['Bepirovir', 'Tablet', 10, 31600],
        ];

        $obatList = [];
        foreach ($obatReal as $index => $obat) {
            [$namaObat, $satuan, $stok, $harga] = $obat;
            $obatList[] = [
                'nama_obat' => $namaObat,
                'satuan' => $satuan,
                'stok' => $stok,
                'harga' => $harga,
            ];
        }

        while (count($obatList) < 1000) {
            $obat = $obatReal[count($obatList) % count($obatReal)];
            $obatList[] = [
                'nama_obat' => $obat[0],
                'satuan' => $obat[1],
                'stok' => $obat[2] + (count($obatList) % 9),
                'harga' => $obat[3] + (count($obatList) * 120),
            ];
        }

        foreach ($obatList as $obat) {
            MObat::create([
                'nama_obat' => $obat['nama_obat'],
                'satuan' => $obat['satuan'],
                'stok' => $obat['stok'],
                'harga' => $obat['harga'],
            ]);
        }
    }
}
