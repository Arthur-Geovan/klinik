@extends('layout.app')

@section('content')
<style>
    body {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background:
            linear-gradient(135deg, rgba(8, 26, 52, 0.55), rgba(24, 97, 145, 0.30)),
            url('{{ asset('assets/img/Gemini_Generated_Image_c3fao6c3fao6c3fa.jpg') }}') center/cover no-repeat fixed;
        color: #203246;
    }

    .register-shell {
        min-height: calc(100vh - 120px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 15px;
    }

    .register-card {
        width: min(760px, 100%);
        background: rgba(255, 255, 255, 0.82);
        border: 1px solid rgba(255,255,255,0.4);
        border-radius: 28px;
        box-shadow: 0 26px 60px rgba(16, 31, 52, 0.20);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        overflow: hidden;
    }

    .guide-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(14, 22, 31, 0.38);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1200;
        padding: 20px;
    }

    .guide-modal {
        width: min(720px, 100%);
        background: rgba(255, 255, 255, 0.96);
        border-radius: 18px;
        box-shadow: 0 30px 80px rgba(11, 28, 46, 0.22);
        border: 1px solid rgba(16, 31, 52, 0.07);
        overflow: hidden;
    }

    .guide-header {
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        padding: 20px 20px 10px;
    }

    .guide-title {
        margin: 0;
        font-size: clamp(1.3rem, 2vw, 2rem);
        font-weight: 800;
        color: #1a1a1a;
        text-align: center;
    }

    .guide-close {
        position: absolute;
        right: 18px;
        top: 14px;
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 50%;
        background: rgba(22, 35, 52, 0.07);
        color: #203247;
        font-size: 1.15rem;
        line-height: 1;
        cursor: pointer;
    }

    .guide-body {
        padding: 0 30px 18px;
    }

    .guide-body ol {
        margin: 0;
        padding-left: 22px;
        color: #1d2a39;
        font-size: 1.02rem;
        line-height: 1.8;
    }

    .guide-body li {
        margin-bottom: 8px;
        padding-left: 4px;
    }

    .guide-body li strong {
        font-weight: 800;
    }

    .guide-footer {
        padding: 0 30px 24px;
        text-align: center;
    }

    .guide-confirm {
        width: 100%;
        border: 0;
        border-radius: 10px;
        background: linear-gradient(135deg, #1a8fe8, #0f6ed8);
        color: #fff;
        font-weight: 700;
        font-size: 1rem;
        padding: 12px 18px;
        box-shadow: 0 12px 24px rgba(17, 110, 216, 0.22);
    }

    .register-card-body {
        padding: 30px 30px 22px;
    }

    .register-title {
        margin-bottom: 26px;
        font-size: clamp(1.8rem, 2vw, 2.4rem);
        font-weight: 800;
        text-align: center;
        color: #0f5bb7;
        letter-spacing: -0.03em;
    }

    .register-card .form-label {
        font-size: 0.82rem;
        font-weight: 700;
        color: #35506d;
        margin-bottom: 8px;
    }

    .register-card .form-control,
    .register-card textarea.form-control {
        border: 1px solid rgba(144, 170, 201, 0.5);
        border-radius: 12px;
        background: rgba(255,255,255,0.72);
        min-height: 46px;
        box-shadow: none;
        transition: all 0.2s ease;
    }

    .register-card .form-control:focus,
    .register-card textarea.form-control:focus {
        border-color: rgba(24, 115, 209, 0.7);
        box-shadow: 0 0 0 0.2rem rgba(24, 115, 209, 0.12);
        background: #fff;
    }

    .register-card textarea.form-control {
        min-height: 110px;
    }

    .register-submit {
        width: 100%;
        border: 0;
        border-radius: 14px;
        background: linear-gradient(135deg, #19a34d, #0b8d5d);
        box-shadow: 0 12px 24px rgba(25, 163, 77, 0.28);
        font-weight: 700;
        font-size: 1.02rem;
        padding: 14px 18px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .register-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 28px rgba(25, 163, 77, 0.34);
    }

    .register-login {
        margin-top: 18px;
        text-align: center;
        font-size: 0.92rem;
        color: #405b7c;
    }

    .register-login a {
        color: #0d6efd;
        font-weight: 700;
    }

    .register-home {
        display: block;
        margin-top: 12px;
        padding: 11px 18px;
        border: 1px solid rgba(53, 80, 109, 0.35);
        border-radius: 12px;
        color: #35506d;
        font-weight: 700;
        text-align: center;
        text-decoration: none;
        transition: background-color 0.2s ease, border-color 0.2s ease;
    }

    .register-home:hover {
        background: rgba(53, 80, 109, 0.08);
        border-color: rgba(53, 80, 109, 0.6);
        color: #203246;
    }

    @media (max-width: 767.98px) {
        .register-card-body {
            padding: 20px 18px 18px;
        }

        .float-whatsapp {
            right: 14px;
            bottom: 16px;
            padding: 9px 12px 9px 10px;
            font-size: 0.76rem;
        }
    }
</style>

<div class="guide-backdrop" id="guideBackdrop">
    <div class="guide-modal" role="dialog" aria-modal="true" aria-labelledby="guideTitle">
        <div class="guide-header">
            <h3 class="guide-title" id="guideTitle">Panduan</h3>
            <button type="button" class="guide-close" aria-label="Tutup panduan" id="closeGuide">×</button>
        </div>
        <div class="guide-body">
            <ol>
                <li>Mohon isi dan lengkapi data diri anda pada form pendaftaran.</li>
                <li>Pastikan <strong>Nomor Induk Kependudukan</strong> yang didaftarkan adalah NIK Pasien.</li>
                <li>Jika pendaftaran anda berhasil maka data diri, nomor antrian, spesialis dan dokter tujuan anda akan ditampilkan kembali.</li>
                <li>Mohon simpan halaman <strong>Bukti Pendaftaran</strong> Anda dengan cara screenshot layar hp anda.</li>
                <li>Mohon datang dan <strong>Konfirmasi Pendaftaran</strong> Anda 60 menit sebelum praktek dokter dimulai. Selambat-lambatnya 30 menit sebelum praktek dimulai.</li>
                <li>Setelah selesai mendaftar di mohon untuk pasien bpjs datang membawa Kertas Rujukan anda.</li>
            </ol>
        </div>
        <div class="guide-footer">
            <button type="button" class="guide-confirm" id="confirmGuide">Mengerti</button>
        </div>
    </div>
</div>

<div class="register-shell">
    <div class="register-card">
        <div class="register-card-body">
            <h3 class="register-title">Registrasi Pasien Baru</h3>

            <form action="{{ route('register.post') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">NIK (Nomor Induk Kependudukan)</label>
                        <input type="number" name="nik" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">No. Telepon/WA</label>
                    <input type="text" name="no_telp" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea name="alamat" class="form-control" rows="2" required></textarea>
                </div>

                <button type="submit" class="btn btn-success register-submit">Daftar Sekarang</button>
            </form>

            <div class="register-login">
                Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a>
            </div>
            <a href="{{ url('/') }}" class="register-home">Kembali ke Halaman Awal</a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const backdrop = document.getElementById('guideBackdrop');
        const closeBtn = document.getElementById('closeGuide');
        const confirmBtn = document.getElementById('confirmGuide');

        if (!backdrop) return;

        const hideGuide = () => {
            backdrop.style.display = 'none';
        };

        closeBtn?.addEventListener('click', hideGuide);
        confirmBtn?.addEventListener('click', hideGuide);
    });
</script>
@endsection