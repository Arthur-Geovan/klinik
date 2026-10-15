<?php

use Illuminate\Support\Facades\Route;
use App\Models\MDokter;
use App\Models\MJadwalPraktik;

// Import Seluruh Controller Berawalan 'C'
use App\Http\Controllers\CLogin;
use App\Http\Controllers\CDashboard;
use App\Http\Controllers\CPoli;
use App\Http\Controllers\CDokter;
use App\Http\Controllers\CJadwalPraktik;
use App\Http\Controllers\CObat;
use App\Http\Controllers\CReservasi;
use App\Http\Controllers\CRekamMedis;
use App\Http\Controllers\CPembayaran;
use App\Http\Controllers\CUlasan;
use App\Http\Controllers\CAIChatbot;
use App\Http\Controllers\CApoteker;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Manajemen Klinik
|--------------------------------------------------------------------------
*/

// Halaman Utama aplikasi
Route::get('/', function () {
    $hariIni = now()->locale('id')->translatedFormat('l');
    $dokterCount = MDokter::count();
    $clinicName = 'Klinik Harapan Bangsa Ngawi Timur';
    $clinicTagline = 'Layanan Kesehatan Modern untuk Masyarakat Ngawi Timur';
    $clinicPhone = '+62 812-3456-7890';
    $clinicAddress = 'Jl. Raya Ngawi Timur, Kabupaten Ngawi, Jawa Timur';
    $clinicEmail = 'info@klinikhbpngawitimur.com';

    $jadwalHariIni = MJadwalPraktik::with(['dokter.user', 'dokter.poli'])
        ->where(function ($query) use ($hariIni) {
            $query->whereRaw('LOWER(hari) = ?', [mb_strtolower($hariIni)])
                ->orWhereRaw('LOWER(hari) LIKE ?', ['%' . mb_strtolower($hariIni) . '%']);
        })
        ->orderBy('jam_mulai')
        ->limit(3)
        ->get();

    if ($jadwalHariIni->isEmpty()) {
        $jadwalHariIni = MJadwalPraktik::with(['dokter.user', 'dokter.poli'])
            ->orderBy('jam_mulai')
            ->limit(3)
            ->get();
    }

    $dokterList = MDokter::with(['user', 'poli'])
        ->orderBy('id')
        ->limit(4)
        ->get();

    return view('welcome', compact(
        'clinicName',
        'clinicTagline',
        'clinicPhone',
        'clinicAddress',
        'clinicEmail',
        'dokterCount',
        'jadwalHariIni',
        'dokterList'
    ));
});

// =========================================================================
// ROUTE GUEST (Hanya bisa diakses jika BELUM LOGIN)
// =========================================================================
Route::middleware('guest')->group(function () {
    // Auth Login
    Route::get('/login', [CLogin::class, 'showLoginForm'])->name('login');
    Route::post('/login', [CLogin::class, 'login'])->name('login.post');

    // Auth Register Pasien
    Route::get('/register', [CLogin::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [CLogin::class, 'register'])->name('register.post');
});

// =========================================================================
// ROUTE AUTHENTICATED (Wajib LOGIN terlebih dahulu)
// =========================================================================
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [CLogin::class, 'logout'])->name('logout');
    Route::post('/notifikasi/dibaca', function () {
        session(['notifications_seen_at' => now()->toDateTimeString()]);

        return response()->json(['success' => true]);
    })->name('notifications.read');

    // Dashboard Utama (Auto Adaptif Sesuai Role: Admin / Dokter / Pasien)
    Route::get('/dashboard', [CDashboard::class, 'index'])->name('dashboard');

    // ---------------------------------------------------------------------
    // ASISTEN INFORMASI AI (Khusus Pasien)
    // ---------------------------------------------------------------------
    Route::middleware('role:pasien')->group(function () {
        Route::get('/chatbot', [CAIChatbot::class, 'index'])->name('chatbot.index');
        Route::post('/chatbot/message', [CAIChatbot::class, 'message'])->name('chatbot.message');
    });

    // ---------------------------------------------------------------------
    // MODUL RESERVASI (Pasien, Admin, Dokter)
    // ---------------------------------------------------------------------
    Route::get('/reservasi', [CReservasi::class, 'index'])->middleware('role:admin,dokter,pasien')->name('reservasi.index');
    Route::post('/reservasi', [CReservasi::class, 'store'])->middleware('role:pasien')->name('reservasi.store');
    Route::patch('/reservasi/{id}/status', [CReservasi::class, 'updateStatus'])->middleware('role:admin,dokter')->name('reservasi.updateStatus');

    // ---------------------------------------------------------------------
    // MODUL REKAM MEDIS & RESEP OBAT (Khusus Dokter)
    // ---------------------------------------------------------------------
    Route::post('/rekam-medis', [CRekamMedis::class, 'store'])->middleware('role:dokter')->name('rekam-medis.store');
    Route::post('/dashboard/lampiran', [CRekamMedis::class, 'storeLampiran'])->middleware('role:pasien')->name('dashboard.lampiran.store');

    // ---------------------------------------------------------------------
    // MODUL PEMBAYARAN & INVOICE (Admin & Kasir)
    // ---------------------------------------------------------------------
    Route::middleware('role:admin')->group(function () {
        Route::get('/pembayaran', [CPembayaran::class, 'index'])->name('pembayaran.index');
        Route::get('/pembayaran/invoice/{reservasi_id}', [CPembayaran::class, 'generateInvoice'])->name('pembayaran.invoice');
        Route::post('/pembayaran/{id}/update', [CPembayaran::class, 'updatePayment'])->name('pembayaran.update');
        Route::patch('/pembayaran/{id}/konfirmasi', [CPembayaran::class, 'konfirmasiBayar'])->name('pembayaran.konfirmasi');
    });
    Route::get('/pembayaran/{id}/kuitansi', [CPembayaran::class, 'kuitansi'])->middleware('role:admin,pasien')->name('pembayaran.kuitansi');

    // ---------------------------------------------------------------------
    // MODUL ULASAN & RATING (Pasien ke Dokter)
    // ---------------------------------------------------------------------
    Route::post('/dokter/{dokter_id}/ulasan', [CUlasan::class, 'store'])->middleware('role:pasien')->name('ulasan.store');

    // ---------------------------------------------------------------------
    // MODUL DATA MASTER (Khusus Admin)
    // ---------------------------------------------------------------------
    
    // Data Master Poli
    Route::middleware('role:admin')->group(function () {
    Route::get('/admin/poli', [CPoli::class, 'index'])->name('poli.index');
    Route::post('/admin/poli', [CPoli::class, 'store'])->name('poli.store');
    Route::put('/admin/poli/{id}', [CPoli::class, 'update'])->name('poli.update');
    Route::delete('/admin/poli/{id}', [CPoli::class, 'destroy'])->name('poli.destroy');

    // Data Master Dokter
    Route::get('/admin/dokter', [CDokter::class, 'index'])->name('dokter.index');
    Route::post('/admin/dokter', [CDokter::class, 'store'])->name('dokter.store');
    Route::put('/admin/dokter/{id}', [CDokter::class, 'update'])->name('dokter.update');
    Route::delete('/admin/dokter/{id}', [CDokter::class, 'destroy'])->name('dokter.destroy');

    // Data Master Jadwal Praktik
    Route::get('/admin/jadwal', [CJadwalPraktik::class, 'index'])->name('jadwal.index');
    Route::post('/admin/jadwal', [CJadwalPraktik::class, 'store'])->name('jadwal.store');
    Route::put('/admin/jadwal/{id}', [CJadwalPraktik::class, 'update'])->name('jadwal.update');
    Route::delete('/admin/jadwal/{id}', [CJadwalPraktik::class, 'destroy'])->name('jadwal.destroy');
    });

    // Data Master Obat & Stok
    Route::middleware('role:admin,apoteker')->group(function () {
        Route::get('/admin/obat', [CObat::class, 'index'])->name('obat.index');
        Route::post('/admin/obat', [CObat::class, 'store'])->name('obat.store');
        Route::put('/admin/obat/{id}', [CObat::class, 'update'])->name('obat.update');
        Route::patch('/admin/obat/{id}/tambah-stok', [CObat::class, 'tambahStok'])->name('obat.tambahStok');
        Route::delete('/admin/obat/{id}', [CObat::class, 'destroy'])->name('obat.destroy');
    });

    Route::middleware('role:apoteker')->prefix('apoteker')->name('apoteker.')->group(function () {
        Route::get('/resep', [CApoteker::class, 'index'])->name('resep.index');
        Route::patch('/resep/{id}/siap', [CApoteker::class, 'konfirmasiSiap'])->name('resep.siap');
        Route::patch('/resep/{id}/diserahkan', [CApoteker::class, 'tandaiDiserahkan'])->name('resep.diserahkan');
    });

});