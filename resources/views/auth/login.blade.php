<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login | Klinik Harapan Bangsa Ngawi Timur</title>

    <link href="{{ asset('assets/lib/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('assets/lib/Ionicons/css/ionicons.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/amanda.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --clinic-navy: #17324d;
            --clinic-blue: #236b8e;
            --clinic-teal: #2aa79b;
            --clinic-mist: #f2f7f8;
            --clinic-ink: #203246;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background:
                linear-gradient(135deg, rgba(8, 26, 52, 0.55), rgba(24, 97, 145, 0.30)),
                url('{{ asset('assets/img/Gemini_Generated_Image_c3fao6c3fao6c3fa.jpg') }}') center/cover no-repeat fixed;
            color: var(--clinic-ink);
        }

        .am-signin-wrapper {
            width: 100%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
        }

        .am-signin-box {
            width: 100%;
            max-width: 780px;
            margin: 0 auto;
            filter: drop-shadow(0 18px 36px rgba(16, 27, 48, 0.24));
        }

        .am-signin-box > .row {
            min-height: 420px;
        }

        .am-signin-box > .row > div:first-child {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 20px;
            background:
                linear-gradient(145deg, rgba(14, 43, 69, 0.76) 0%, rgba(19, 78, 110, 0.72) 40%, rgba(34, 167, 155, 0.58) 100%),
                url('{{ asset('assets/img/Gemini_Generated_Image_c3fao6c3fao6c3fa.jpg') }}') center/cover no-repeat;
        }

        .am-signin-box > .row > div:last-child {
            background: #ffffff;
        }

        .am-signin-box > .row > div:first-child {
            position: relative;
            overflow: hidden;
        }

        .am-signin-box > .row > div:first-child::after {
            content: '';
            position: absolute;
            width: 180px;
            height: 180px;
            right: -82px;
            bottom: -90px;
            border: 28px solid rgba(255, 255, 255, 0.12);
            border-radius: 50%;
        }

        .login-brand {
            position: relative;
            z-index: 1;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .login-brand-mark {
            display: inline-flex;
            width: 44px;
            height: 44px;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
            border: 1px solid rgba(255, 255, 255, 0.45);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.16);
            font-size: 22px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.14), 0 2px 4px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.18);
        }

        .login-brand h2 {
            margin-bottom: 10px;
            letter-spacing: 0;
        }

        .login-brand .login-lead {
            margin-bottom: 12px;
            font-size: 16px;
            font-weight: 600;
        }

        .login-brand .login-copy {
            max-width: 265px;
            margin: 0 auto;
            color: rgba(255, 255, 255, 0.82);
            line-height: 1.65;
        }

        .login-form-panel {
            min-height: 390px;
        }

        .login-eyebrow {
            margin-bottom: 8px;
            color: var(--clinic-teal);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }

        .login-form-panel h5 {
            margin-bottom: 28px;
            font-size: 24px;
            font-weight: 700;
        }

        .login-input-wrap {
            position: relative;
        }

        .login-input-wrap .icon {
            position: absolute;
            top: 11px;
            left: 13px;
            z-index: 2;
            color: #8a96a8;
            font-size: 18px;
        }

        .login-input-wrap .form-control {
            height: 46px;
            padding-left: 42px;
            border-radius: 4px;
        }

        .login-submit {
            height: 46px;
            border-radius: 4px;
            background: var(--clinic-navy);
            border-color: var(--clinic-navy);
            color: #ffffff;
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        .login-submit:hover,
        .login-submit:focus {
            background: var(--clinic-teal);
            border-color: var(--clinic-teal);
            color: #ffffff;
        }

        .login-register {
            margin-top: 20px;
            color: #7b8798;
            font-size: 13px;
        }

        .login-register a {
            color: var(--clinic-blue);
            font-weight: 700;
        }

        @media (max-width: 991.98px) {
            .am-signin-box > .row > div:first-child {
                padding: 30px;
            }

            .login-form-panel {
                min-height: 0;
            }
        }
    </style>
</head>
<body>
    <div class="am-signin-wrapper">
        <div class="am-signin-box">
            <div class="row no-gutters">
                <div class="col-lg-5">
                    <div class="login-brand">
                        <span class="login-brand-mark"><i class="fa-solid fa-hospital"></i></span>
                        <h2>Klinik Harapan Bangsa Ngawi Timur</h2>
                        <p class="login-lead">Layanan kesehatan lebih teratur.</p>
                        <p class="login-copy">Kelola reservasi, rekam medis, pembayaran, dan layanan pasien dalam satu sistem.</p>

                        <hr>
                        <p>Belum punya akun?<br><a href="{{ route('register') }}">Daftar sebagai pasien</a></p>
                    </div>
                </div>
                <div class="col-lg-7 login-form-panel">
                    <div class="login-eyebrow">Portal Klinik Harapan Bangsa Ngawi Timur</div>
                    <h5 class="tx-gray-800 mg-b-25">Masuk ke Akun Anda</h5>

                    <form action="{{ route('login.post') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-control-label" for="email">Email:</label>
                            <div class="login-input-wrap">
                                <i class="icon ion-ios-email-outline"></i>
                                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Masukkan alamat email" required autofocus>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-control-label" for="password">Password:</label>
                            <div class="login-input-wrap">
                                <i class="icon ion-ios-locked-outline"></i>
                                <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-block login-submit">Masuk</button>
                        <p class="login-register text-center">Belum memiliki akun? <a href="{{ route('register') }}">Daftar pasien baru</a></p>
                    </form>
                </div>
            </div>
            <p class="tx-center tx-white-5 tx-12 mg-t-15">&copy; {{ date('Y') }} Klinik Harapan Bangsa Ngawi Timur. Semua hak dilindungi.</p>
        </div>
    </div>

    <script src="{{ asset('assets/lib/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/lib/popper.js/popper.js') }}"></script>
    <script src="{{ asset('assets/lib/bootstrap/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/js/amanda.js') }}"></script>

    @if(session('status'))
        <script>
            Swal.fire({
                title: @json(session('status')['judul'] ?? 'Informasi'),
                text: @json(session('status')['pesan'] ?? ''),
                icon: @json(session('status')['icon'] ?? 'info'),
                showConfirmButton: false,
                timer: 1500
            });
        </script>
    @elseif(session('success'))
        <script>
            Swal.fire({
                title: 'Berhasil',
                text: @json(session('success')),
                icon: 'success',
                showConfirmButton: false,
                timer: 1500
            });
        </script>
    @endif

    @if($errors->any())
        <script>
            Swal.fire({
                title: 'Gagal masuk',
                text: @json($errors->first()),
                icon: 'error'
            });
        </script>
    @endif
</body>
</html>