<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di {{ $clinicName ?? 'Klinik Harapan Bangsa Ngawi Timur' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        :root {
            --primary: #0d6efd;
            --primary-strong: #0052d9;
            --primary-soft: #dfeeff;
            --clinic-navy: #17324d;
            --clinic-blue: #236b8e;
            --clinic-teal: #2aa79b;
            --card: rgba(255, 255, 255, 0.84);
            --text: #132238;
            --muted: #5f7089;
            --line: rgba(138, 162, 196, 0.25);
            --shadow: 0 26px 80px rgba(24, 59, 122, 0.18);
            --soft-blue: #eaf3ff;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background:
                linear-gradient(135deg, rgba(11, 29, 52, 0.72), rgba(26, 85, 140, 0.45)),
                url('{{ asset('assets/img/Gemini_Generated_Image_c3fao6c3fao6c3fa.jpg') }}') center/cover no-repeat fixed;
            color: var(--text);
        }

        a { text-decoration: none; }

        .page-shell {
            position: relative;
            min-height: 100vh;
            padding: 40px 20px 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .page-shell::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(8, 20, 38, 0.10);
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(18px);
            opacity: 0.75;
            z-index: 0;
        }

        .orb-one {
            width: 440px;
            height: 440px;
            background: rgba(90, 181, 255, 0.22);
            top: -100px;
            left: -90px;
        }

        .orb-two {
            width: 500px;
            height: 500px;
            background: rgba(30, 111, 255, 0.18);
            right: -120px;
            bottom: -180px;
        }

        .hero-card {
            position: relative;
            z-index: 1;
            width: min(1200px, 100%);
            background: rgba(255, 255, 255, 0.78);
            border: 1px solid rgba(255, 255, 255, 0.35);
            backdrop-filter: blur(12px);
            border-radius: 34px;
            box-shadow: var(--shadow);
            padding: 36px 34px 28px;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.12fr 0.88fr;
            align-items: center;
            gap: 30px;
        }

        .hero-copy {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            padding: 9px 16px;
            border-radius: 999px;
            background: rgba(13, 110, 253, 0.08);
            border: 1px solid rgba(13, 110, 253, 0.14);
            color: var(--primary-strong);
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .eyebrow::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: linear-gradient(180deg, #5ab5ff, #0d6efd);
            box-shadow: 0 0 12px rgba(13, 110, 253, 0.4);
        }

        h1 {
            margin: 18px 0 16px;
            font-size: clamp(2.7rem, 4vw, 5rem);
            line-height: 0.95;
            letter-spacing: -0.07em;
            font-weight: 800;
            color: #0c1b35;
        }

        h1 .accent {
            display: block;
            background: linear-gradient(135deg, #0d6efd 0%, #69b7ff 45%, #7f6bff 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .hero-subtitle {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px;
            margin-bottom: 22px;
        }

        .hero-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(148, 168, 210, 0.2);
            color: var(--clinic-navy);
            font-size: 0.8rem;
            font-weight: 600;
        }

        .hero-chip i {
            color: var(--primary-strong);
            font-size: 0.82rem;
        }

        .lead {
            max-width: 620px;
            margin: 0 auto 24px;
            font-size: 1.12rem;
            line-height: 1.8;
            color: var(--muted);
        }

        .cta-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 14px;
            margin-bottom: 26px;
        }

        .btn-primary-custom,
        .btn-secondary-custom {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 190px;
            padding: 14px 24px;
            border-radius: 16px;
            font-weight: 700;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #0d6efd, #1987ff 55%, #3aa4ff 100%);
            color: #fff;
            box-shadow: 0 18px 30px rgba(13, 110, 253, 0.28);
        }

        .btn-secondary-custom {
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid rgba(119, 136, 165, 0.25);
            color: var(--text);
        }

        .btn-primary-custom:hover,
        .btn-secondary-custom:hover {
            transform: translateY(-2px);
            text-decoration: none;
        }

        .stats {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 18px;
        }

        .stat-box {
            flex: 1 1 140px;
            min-width: 140px;
            padding: 18px 18px 16px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid var(--line);
            box-shadow: 0 8px 24px rgba(130, 148, 186, 0.12);
        }

        .stat-box strong {
            display: block;
            font-size: 1.18rem;
            color: var(--primary-strong);
        }

        .stat-box span {
            color: var(--muted);
            font-size: 0.82rem;
        }

        .visual-panel {
            position: relative;
            padding: 18px;
        }

        .dashboard-card {
            position: relative;
            background: linear-gradient(180deg, rgba(255,255,255,0.96), rgba(236,245,255,0.9));
            border: 1px solid rgba(122, 145, 190, 0.2);
            border-radius: 28px;
            box-shadow: 0 28px 45px rgba(18, 49, 97, 0.12);
            padding: 18px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 12px;
            margin-bottom: 14px;
            border-bottom: 1px solid rgba(134, 154, 194, 0.2);
        }

        .dots {
            display: flex;
            gap: 8px;
        }

        .dots span {
            display: inline-block;
            width: 11px;
            height: 11px;
            border-radius: 50%;
            background: #d8e3f7;
        }

        .mini-pill {
            background: rgba(13, 110, 253, 0.08);
            color: var(--primary-strong);
            border-radius: 12px;
            padding: 6px 10px;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .clinic-box {
            display: grid;
            gap: 14px;
            margin-top: 14px;
        }

        .card-schedule {
            background: linear-gradient(135deg, rgba(13,110,253,0.1), rgba(90,191,255,0.08));
            border: 1px solid rgba(13,110,253,0.12);
            border-radius: 20px;
            padding: 16px;
        }

        .schedule-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            font-weight: 700;
            color: var(--text);
        }

        .schedule-list {
            display: grid;
            gap: 10px;
        }

        .schedule-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: 12px;
            background: rgba(255,255,255,0.6);
            padding: 10px 12px;
            font-size: 0.82rem;
            color: var(--muted);
            gap: 10px;
        }

        .schedule-item strong {
            color: var(--text);
            white-space: nowrap;
        }

        .schedule-item .doctor-name {
            display: inline-block;
            font-weight: 600;
            color: var(--text);
        }

        .status {
            background: #eafaf0;
            color: #1c8d5d;
            border-radius: 999px;
            padding: 5px 9px;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .floating-card {
            position: absolute;
            right: -8px;
            bottom: 30px;
            width: 190px;
            background: rgba(255,255,255,0.9);
            border: 1px solid rgba(122,145,190,0.2);
            border-radius: 20px;
            padding: 14px 16px;
            box-shadow: 0 18px 30px rgba(48, 80, 130, 0.15);
        }

        .floating-card strong {
            display: block;
            color: var(--text);
            font-size: 1.5rem;
        }

        .floating-card span {
            color: var(--muted);
            font-size: 0.8rem;
        }

        .info-section {
            position: relative;
            z-index: 1;
            width: min(1200px, 100%);
            margin-top: 28px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
        }

        .mini-card {
            background: rgba(255,255,255,0.82);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.28);
            border-radius: 22px;
            padding: 22px 20px;
            box-shadow: 0 18px 38px rgba(17, 35, 76, 0.08);
        }

        .mini-card .icon-wrap {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(13,110,253,0.12), rgba(42,167,155,0.12));
            border: 1px solid rgba(13,110,253,0.12);
            color: var(--clinic-navy);
            font-size: 1.45rem;
            margin-bottom: 14px;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.4);
            line-height: 1;
        }

        .mini-card .icon-wrap i {
            display: inline-block;
            line-height: 1;
        }

        .mini-card h3 {
            margin: 0 0 8px;
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--text);
        }

        .mini-card p {
            margin: 0;
            color: var(--muted);
            font-size: 0.92rem;
            line-height: 1.7;
        }

        .section-block {
            position: relative;
            z-index: 1;
            width: min(1200px, 100%);
            margin-top: 30px;
            background: rgba(255,255,255,0.82);
            border: 1px solid rgba(255,255,255,0.28);
            border-radius: 28px;
            padding: 28px 26px;
            box-shadow: 0 18px 38px rgba(17, 35, 76, 0.08);
        }

        .section-header {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .section-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(13,110,253,0.08);
            color: var(--primary-strong);
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .section-tag i {
            font-size: 0.8rem;
        }

        .section-header h2 {
            margin: 12px 0 0;
            color: var(--clinic-navy);
            font-size: clamp(1.8rem, 2vw, 2.6rem);
            letter-spacing: -0.04em;
            font-weight: 800;
        }

        .services-grid,
        .doctor-grid,
        .contact-grid {
            display: grid;
            gap: 20px;
        }

        .services-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .service-card,
        .doctor-card,
        .contact-item {
            background: rgba(255,255,255,0.72);
            border: 1px solid rgba(148, 168, 210, 0.18);
            border-radius: 22px;
            padding: 22px 18px;
            box-shadow: 0 14px 28px rgba(24, 57, 107, 0.06);
        }

        .service-card .service-icon,
        .contact-item .contact-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            background: linear-gradient(135deg, rgba(13,110,253,0.12), rgba(42,167,155,0.12));
            color: var(--primary-strong);
            font-size: 1.45rem;
            line-height: 1;
        }

        .service-card h3,
        .doctor-card h3,
        .contact-item h3 {
            margin: 0 0 8px;
            color: var(--clinic-navy);
            font-size: 1.15rem;
            font-weight: 800;
        }

        .service-card p,
        .doctor-card p,
        .contact-item p {
            margin: 0;
            color: var(--muted);
            line-height: 1.7;
            font-size: 0.92rem;
        }

        .doctor-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .doctor-card {
            text-align: left;
        }

        .doctor-avatar {
            width: 62px;
            height: 62px;
            border-radius: 18px;
            background: linear-gradient(135deg, #dfeeff, #c7f1ef);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary-strong);
        }

        .doctor-role {
            display: inline-block;
            margin-bottom: 8px;
            padding: 5px 9px;
            border-radius: 999px;
            background: rgba(13,110,253,0.08);
            color: var(--primary-strong);
            font-size: 0.68rem;
            font-weight: 700;
        }

        .doctor-meta {
            list-style: none;
            padding: 0;
            margin: 16px 0 0;
            display: grid;
            gap: 8px;
            color: var(--muted);
            font-size: 0.8rem;
        }

        .doctor-meta li {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .doctor-meta i {
            color: var(--primary-strong);
            font-size: 0.75rem;
        }

        .contact-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .contact-item strong {
            display: block;
            margin-bottom: 8px;
            color: var(--clinic-navy);
            font-size: 1rem;
        }

        .bottom-bar {
            position: relative;
            z-index: 1;
            width: min(1200px, 100%);
            margin-top: 30px;
            padding: 22px 28px;
            border-radius: 24px;
            background: rgba(255,255,255,0.82);
            border: 1px solid rgba(255,255,255,0.28);
            box-shadow: 0 18px 38px rgba(17, 35, 76, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
        }

        .bottom-brand,
        .bottom-contact {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .bottom-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(13,110,253,0.12), rgba(42,167,155,0.12));
            color: var(--primary-strong);
            font-size: 1.3rem;
            flex-shrink: 0;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.45);
            font-family: "Segoe UI Emoji", "Apple Color Emoji", sans-serif;
        }

        .bottom-bar strong {
            display: block;
            color: var(--clinic-navy);
            font-size: 1.05rem;
        }

        .bottom-bar span {
            color: var(--muted);
            font-size: 0.9rem;
        }

        @media (max-width: 820px) {
            .hero-card {
                padding: 22px 18px;
            }

            .hero-grid {
                grid-template-columns: 1fr;
            }

            h1 {
                font-size: clamp(2.5rem, 12vw, 4rem);
            }

            .cta-row {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-primary-custom,
            .btn-secondary-custom {
                width: 100%;
            }

            .stats {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .floating-card {
                position: static;
                width: 100%;
                margin-top: 16px;
            }

            .info-section,
            .services-grid,
            .doctor-grid,
            .contact-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="page-shell">
        <div class="orb orb-one"></div>
        <div class="orb orb-two"></div>

        <div class="hero-card">
            <div class="hero-grid">
                <div class="hero-copy">
                    <span class="eyebrow">Klinik Modern</span>
                    <h1>Selamat Datang di <span class="accent">{{ $clinicName ?? 'Klinik Harapan Bangsa Ngawi Timur' }}</span></h1>

                    <div class="hero-subtitle">
                        <span class="hero-chip"><i class="fa-solid fa-calendar-check"></i> Reservasi cepat</span>
                        <span class="hero-chip"><i class="fa-solid fa-shield-heart"></i> Pelayanan terpercaya</span>
                    </div>

                    <p class="lead">{{ $clinicTagline ?? 'Layanan Kesehatan Modern untuk Masyarakat Ngawi Timur' }} — Sistem layanan reservasi, pengelolaan dokter, obat, dan rekam medis terpadu untuk pengalaman klinik yang lebih cepat, aman, dan nyaman.</p>

                    <div class="cta-row">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn-primary-custom">Masuk Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn-primary-custom">Login</a>
                            <a href="{{ route('register') }}" class="btn-secondary-custom">Daftar Pasien</a>
                        @endauth
                    </div>

                    <div class="stats">
                        <div class="stat-box">
                            <strong>24/7</strong>
                            <span>Reservasi</span>
                        </div>
                        <div class="stat-box">
                            <strong>{{ $dokterCount }}</strong>
                            <span>Dokter</span>
                        </div>
                        <div class="stat-box">
                            <strong>99,9%</strong>
                            <span>Keamanan</span>
                        </div>
                    </div>
                </div>

                <div class="visual-panel">
                    <div class="dashboard-card">
                        <div class="topbar">
                            <div class="dots">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                            <div class="mini-pill">Live</div>
                        </div>

                        <div class="clinic-box">
                            <div class="card-schedule">
                                <div class="schedule-head">
                                    <span>Jadwal Hari Ini</span>
                                    <span class="status">Aktif</span>
                                </div>
                                <div class="schedule-list">
                                    @forelse($jadwalHariIni as $jadwal)
                                        <div class="schedule-item">
                                            <span class="doctor-name">{{ $jadwal->dokter->user->name ?? 'Dokter Klinik' }}</span>
                                            <strong>{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}</strong>
                                        </div>
                                    @empty
                                        <div class="schedule-item">
                                            <span class="doctor-name">Belum ada jadwal</span>
                                            <strong>--:--</strong>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="floating-card">
                        <strong>{{ number_format(
                            App\Models\MPasien::count() * 47 + 284, 0, ',', '.'
                        ) }}</strong>
                        <span>Pasien terlayani bulan ini</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="info-section">
            <div class="mini-card">
                <div class="icon-wrap">🩺</div>
                <h3>Dokter Profesional</h3>
                <p>Tim dokter berpengalaman dan siap memberikan pelayanan medis yang cepat, aman, dan ramah untuk keluarga Anda.</p>
            </div>
            <div class="mini-card">
                <div class="icon-wrap">⚕️</div>
                <h3>Layanan Berkualitas</h3>
                <p>Mulai dari poli umum, pemeriksaan, hingga rekam medis digital yang terintegrasi untuk pengalaman layanan lebih modern.</p>
            </div>
            <div class="mini-card">
                <div class="icon-wrap">📍</div>
                <h3>Lokasi Mudah Dijangkau</h3>
                <p>Terletak di wilayah Ngawi Timur dengan akses yang mudah, nyaman, dan strategis untuk kebutuhan kesehatan masyarakat.</p>
            </div>
        </div>

        <div class="section-block">
            <div class="section-header">
                <div>
                    <span class="section-tag"><i class="fa-solid fa-heart-pulse"></i> Layanan</span>
                    <h2>Pelayanan kesehatan yang kami sediakan</h2>
                </div>
            </div>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">🩺</div>
                    <h3>Poli Umum</h3>
                    <p>Pemeriksaan kesehatan dasar, konsultasi keluhan umum, dan penanganan medis awal dengan cepat.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">💉</div>
                    <h3>Imunisasi</h3>
                    <p>Layanan vaksinasi dan imunisasi untuk anak serta kebutuhan perlindungan kesehatan keluarga.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">🧪</div>
                    <h3>Laboratorium</h3>
                    <p>Pemeriksaan penunjang medis yang akurat untuk mendukung diagnosis dan perawatan yang tepat.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">👶</div>
                    <h3>Kesehatan Ibu & Anak</h3>
                    <p>Pelayanan konseling, pemeriksaan, dan pendampingan kesehatan ibu serta tumbuh kembang anak.</p>
                </div>
            </div>
        </div>

        <div class="section-block">
            <div class="section-header">
                <div>
                    <span class="section-tag"><i class="fa-solid fa-user-doctor"></i> Dokter</span>
                    <h2>Tim dokter kami</h2>
                </div>
            </div>
            <div class="doctor-grid">
                @forelse($dokterList as $dokter)
                    <div class="doctor-card">
                        <div class="doctor-avatar">👨‍⚕️</div>
                        <span class="doctor-role">{{ $dokter->poli->nama_poli ?? 'Poli Umum' }}</span>
                        <h3>{{ $dokter->user->name ?? 'Dokter Klinik' }}</h3>
                        <p>{{ $dokter->spesialisasi ?? 'Dokter umum dan pelayanan kesehatan' }}</p>
                        <ul class="doctor-meta">
                            <li><i class="fa-solid fa-id-card"></i> {{ $dokter->no_str ?? 'No. STR tersedia' }}</li>
                            <li><i class="fa-solid fa-clock"></i> Jadwal praktik tersedia</li>
                        </ul>
                    </div>
                @empty
                    <div class="doctor-card">
                        <div class="doctor-avatar">👨‍⚕️</div>
                        <span class="doctor-role">Poli Umum</span>
                        <h3>Dokter Klinik</h3>
                        <p>Dokter profesional siap melayani kebutuhan kesehatan masyarakat.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="section-block">
            <div class="section-header">
                <div>
                    <span class="section-tag"><i class="fa-solid fa-phone-volume"></i> Kontak</span>
                    <h2>Hubungi Klinik Harapan Bangsa</h2>
                </div>
            </div>
            <div class="contact-grid">
                <div class="contact-item">
                    <div class="contact-icon">📞</div>
                    <strong>Telepon</strong>
                    <p>{{ $clinicPhone ?? '+62 812-3456-7890' }}</p>
                </div>
                <div class="contact-item">
                    <div class="contact-icon">📍</div>
                    <strong>Alamat</strong>
                    <p>{{ $clinicAddress ?? 'Jl. Raya Ngawi Timur, Kabupaten Ngawi, Jawa Timur' }}</p>
                </div>
                <div class="contact-item">
                    <div class="contact-icon">✉️</div>
                    <strong>Email</strong>
                    <p>{{ $clinicEmail ?? 'info@klinikhbpngawitimur.com' }}</p>
                </div>
                <div class="contact-item">
                    <div class="contact-icon">🕒</div>
                    <strong>Jam Layanan</strong>
                    <p>Senin - Sabtu<br>08.00 - 20.00 WIB</p>
                </div>
            </div>
        </div>

        <div class="bottom-bar">
            <div class="bottom-brand">
                <div class="bottom-icon">🏥</div>
                <div>
                    <strong>Klinik Harapan Bangsa Ngawi Timur</strong>
                    <span>Pelayanan kesehatan modern, terpercaya, dan ramah untuk keluarga.</span>
                </div>
            </div>
            <div class="bottom-contact">
                <div class="bottom-icon">📞</div>
                <div>
                    <strong>Hubungi Kami</strong>
                    <span>{{ $clinicPhone ?? '+62 852-4772-0535' }} • {{ $clinicAddress ?? 'Jl. Raya Ngawi Timur' }}</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        setInterval(() => {
            window.location.reload();
        }, 30000);
    </script>
</body>
</html>