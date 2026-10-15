@extends('layout.menu')

@section('konten')
<link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.min.css') }}">

<div class="custom-dashboard animate-fade-in">
    <div class="header-banner shadow-sm">
        <div class="header-content">
            <div class="user-info">
                <p class="greeting">Halo, selamat datang kembali 👋</p>
                <h1 class="user-name">Sapnel Desera Adventus</h1>
                <div class="badges-row">
                    <span class="badge-custom"><i class="fa fa-circle text-success font-size-8 mr-1"></i> ADMINISTRATOR</span>
                    <span class="badge-custom">STMIK BANJARBARU</span>
                </div>
                <p class="system-desc">Sistem Informasi Perpustakaan — Kelola data dan pantau sirkulasi buku.</p>
            </div>
            
            <div class="clock-box shadow-sm">
                <div class="clock-content">
                    <div class="clock-icon-bg">📚</div>
                    <div class="time-wrapper">
                        <h2 id="realtime-clock">00.00.00</h2>
                        <p id="realtime-date">Memuat Tanggal...</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="wave-container">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                <path d="M0,60 C320,110 420,10 720,65 C1020,120 1120,20 1440,60 L1440,120 L0,120 Z" fill="#f4f7fc"/>
            </svg>
        </div>
    </div>

    <div class="row mt-4">
        @php
            $stats = [
                ['label' => 'TOTAL ANGGOTA', 'count' => $total_anggota, 'icon' => 'fa-users', 'color' => 'blue', 'tag' => 'AKTIF', 'route' => 'anggota.index'],
                ['label' => 'TOTAL BUKU', 'count' => $total_buku, 'icon' => 'fa-book', 'color' => 'green', 'tag' => 'KOLEKSI', 'route' => 'buku.index'],
                ['label' => 'TOTAL KATEGORI', 'count' => $total_kategori, 'icon' => 'fa-tags', 'color' => 'orange', 'tag' => 'KATALOG', 'route' => 'kategori.index'],
                ['label' => 'TOTAL PEMINJAMAN', 'count' => $total_peminjaman, 'icon' => 'fa-sync', 'color' => 'indigo', 'tag' => 'AKTIF', 'route' => 'peminjaman.index'],
                ['label' => 'TOTAL PENGEMBALIAN', 'count' => $total_pengembalian, 'icon' => 'fa-check-circle', 'color' => 'teal', 'tag' => 'SELESAI', 'route' => 'pengembalian.index'],
                ['label' => 'TOTAL PENGGUNA', 'count' => $total_pengguna, 'icon' => 'fa-user-shield', 'color' => 'cyan', 'tag' => 'STAF', 'route' => 'pengguna.index'],
            ];
        @endphp

        @foreach($stats as $stat)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="stat-card card-border-{{ $stat['color'] }} shadow-sm h-100">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="mini-icon-box icon-bg-{{ $stat['color'] }}">
                            <i class="fas {{ $stat['icon'] }}"></i>
                        </div>
                        <span class="stat-tag tag-{{ $stat['color'] }}">{{ $stat['tag'] }}</span>
                    </div>
                    
                    <h2 class="stat-number">{{ $stat['count'] ?? 0 }}</h2>
                    <p class="stat-label">{{ $stat['label'] }}</p>
                    
                    <i class="fas {{ $stat['icon'] }} card-watermark"></i>
                    
                    <div class="divider-line"></div>
                    <a href="{{ asset('assets') . '/' . ($stat['route'] ?? '') }}" class="btn-detail">
                        Lihat Detail <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="info-card p-4 shadow-sm h-100">
                <h5 class="info-title"><i class="fas fa-chart-pie mr-2 text-primary"></i> Ringkasan Sirkulasi</h5>
                
                <div class="chart-box-container mt-4">
                    <div class="donut-ring position-relative">
                        <div class="donut-center-absolute">
                            <h3>{{ ($total_peminjaman ?? 0) + ($total_pengembalian ?? 0) }}</h3>
                            <p>TRANSAKSI</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 px-2">
                    <div class="d-flex justify-content-between align-items-center mb-2 font-size-13">
                        <span class="text-muted"><span class="legend-dot bg-indigo"></span> Peminjaman</span>
                        <span class="badge-count-indigo">{{ $total_peminjaman ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center font-size-13">
                        <span class="text-muted"><span class="legend-dot bg-teal"></span> Pengembalian</span>
                        <span class="badge-count-teal">{{ $total_pengembalian ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="info-card p-4 shadow-sm h-100">
                <h5 class="info-title"><i class="fas fa-bolt mr-2 text-warning"></i> Menu Cepat</h5>
                <div class="row mt-3 no-gutters">
                    <div class="col-4 p-1">
                        <a href="{{ asset('assets') . '/peminjaman' }}" class="quick-box-item q-blue">
                            <i class="fas fa-sync"></i><span>Peminjaman</span>
                        </a>
                    </div>
                    <div class="col-4 p-1">
                        <a href="{{ asset('assets') . '/pengembalian' }}" class="quick-box-item q-green">
                            <i class="fas fa-check-circle"></i><span>Pengembalian</span>
                        </a>
                    </div>
                    <div class="col-4 p-1">
                        <a href="{{ asset('assets') . '/anggota' }}" class="quick-box-item q-orange">
                            <i class="fas fa-users"></i><span>Anggota</span>
                        </a>
                    </div>
                    <div class="col-4 p-1">
                        <a href="{{ asset('assets') . '/buku' }}" class="quick-box-item q-purple">
                            <i class="fas fa-book"></i><span>Data Buku</span>
                        </a>
                    </div>
                    <div class="col-4 p-1">
                        <a href="{{ asset('assets') . '/kategori' }}" class="quick-box-item q-cyan">
                            <i class="fas fa-tags"></i><span>Kategori</span>
                        </a>
                    </div>
                    <div class="col-4 p-1">
                        <a href="{{ asset('assets') . '/pengguna' }}" class="quick-box-item q-dark">
                            <i class="fas fa-user-shield"></i><span>Pengguna</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="info-card p-4 shadow-sm h-100">
                <h5 class="info-title"><i class="fas fa-info-circle mr-2 text-info"></i> Info Sistem</h5>
                <div class="list-system-info mt-3">
                    <div class="sys-item">
                        <div class="sys-icon bg-light text-danger"><i class="fas fa-university"></i></div>
                        <div><label>INSTITUSI</label><p>STMIK Banjarbaru</p></div>
                    </div>
                    <div class="sys-item">
                        <div class="sys-icon bg-light text-success"><i class="fas fa-book-open"></i></div>
                        <div><label>UNIT</label><p>Perpustakaan Kampus</p></div>
                    </div>
                    <div class="sys-item">
                        <div class="sys-icon bg-light text-primary"><i class="fas fa-user-circle"></i></div>
                        <div><label>OPERATOR</label><p>Sapnel Desera Adventus</p></div>
                    </div>
                    <div class="sys-item">
                        <div class="sys-icon bg-light text-warning"><i class="fas fa-calendar-alt"></i></div>
                        <div><label>TANGGAL</label><p id="sys-date-text">Memuat...</p></div>
                    </div>
                    <div class="sys-item">
                        <div class="sys-icon bg-light text-secondary"><i class="fas fa-lock"></i></div>
                        <div><label>HAK AKSES</label><p><span class="badge badge-success px-2">Administrator Penuh</span></p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function runClock() {
        const timeNode = document.getElementById('realtime-clock');
        const dateNode = document.getElementById('realtime-date');
        const sysDateNode = document.getElementById('sys-date-text');
        
        if(!timeNode) return;

        const now = new Date();
        
        const hh = String(now.getHours()).padStart(2, '0');
        const mm = String(now.getMinutes()).padStart(2, '0');
        const ss = String(now.getSeconds()).padStart(2, '0');
        timeNode.textContent = `${hh}.${mm}.${ss}`;

        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        const stringDate = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
        
        dateNode.textContent = stringDate;
        if(sysDateNode) sysDateNode.textContent = stringDate;
    }
    setInterval(runClock, 1000);
    runClock();
</script>

<style>
    .custom-dashboard { background-color: #f4f7fc; padding: 20px; font-family: 'Inter', sans-serif; color: #334155; }
    .animate-fade-in { animation: fadeIn 0.5s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* BANNER */
    .header-banner {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        border-radius: 16px; padding: 40px 40px 90px 40px; position: relative; color: white; overflow: hidden;
    }
    .header-content { display: flex; justify-content: space-between; align-items: center; position: relative; z-index: 5; }
    .greeting { font-size: 14px; opacity: 0.9; margin-bottom: 4px; font-weight: 500; }
    .user-name { font-size: 30px; font-weight: 800; margin-bottom: 12px; letter-spacing: -0.5px; }
    .badge-custom { background: rgba(255,255,255,0.15); padding: 5px 14px; border-radius: 50px; font-size: 11px; font-weight: 700; margin-right: 8px; border: 1px solid rgba(255,255,255,0.1); }
    .system-desc { margin-top: 15px; opacity: 0.8; font-size: 13px; font-weight: 300; }

    /* CLOCK BOX MOCKUP */
    .clock-box { background: rgba(255, 255, 255, 0.12); backdrop-filter: blur(8px); padding: 15px 25px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.2); width: 240px; }
    .clock-content { display: flex; align-items: center; gap: 15px; }
    .clock-icon-bg { font-size: 28px; background: rgba(255,255,255,0.2); width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 12px; }
    #realtime-clock { font-size: 26px; font-weight: 800; margin: 0; letter-spacing: 0.5px; }
    #realtime-date { font-size: 11px; opacity: 0.8; margin: 2px 0 0 0; font-weight: 400; }
    .wave-container { position: absolute; bottom: -2px; left: 0; width: 100%; height: 50px; z-index: 1; }
    .wave-container svg { width: 100%; height: 100%; }

    /* STAT CARDS */
    .stat-card { background: white; border-radius: 16px; border: none; position: relative; overflow: hidden; transition: all 0.3s ease; }
    .stat-card:hover { transform: translateY(-4px); box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; }
    .mini-icon-box { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
    .stat-tag { font-size: 10px; font-weight: 800; padding: 2px 10px; border-radius: 6px; }
    .stat-number { font-size: 40px; font-weight: 800; margin: 15px 0 2px 0; color: #1e293b; position: relative; z-index: 3; }
    .stat-label { font-size: 12px; font-weight: 700; color: #94a3b8; margin-bottom: 0; letter-spacing: 0.5px; position: relative; z-index: 3; }
    .divider-line { height: 1px; background: #f1f5f9; margin: 15px 0 10px 0; }
    .btn-detail { font-size: 12px; font-weight: 600; color: #64748b; text-decoration: none !important; transition: color 0.2s; }
    .btn-detail:hover { color: #3b82f6; }
    
    /* WATERMARK SAMAR */
    .card-watermark { position: absolute; bottom: 35px; right: 15px; font-size: 75px; color: rgba(0, 0, 0, 0.02); pointer-events: none; z-index: 1; }

    /* CARD LINES BORDER ATAS */
    .card-border-blue { border-top: 4px solid #3b82f6 !important; }
    .card-border-green { border-top: 4px solid #10b981 !important; }
    .card-border-orange { border-top: 4px solid #f59e0b !important; }
    .card-border-indigo { border-top: 4px solid #6366f1 !important; }
    .card-border-teal { border-top: 4px solid #14b8a6 !important; }
    .card-border-cyan { border-top: 4px solid #06b6d4 !important; }

    /* ICON BACKGROUNDS */
    .icon-bg-blue { background: #eff6ff; color: #3b82f6; } .tag-blue { background: #eff6ff; color: #3b82f6; }
    .icon-bg-green { background: #ecfdf5; color: #10b981; } .tag-green { background: #ecfdf5; color: #10b981; }
    .icon-bg-orange { background: #fffbeb; color: #f59e0b; } .tag-orange { background: #fffbeb; color: #f59e0b; }
    .icon-bg-indigo { background: #eef2ff; color: #6366f1; } .tag-indigo { background: #eef2ff; color: #6366f1; }
    .icon-bg-teal { background: #f0fdfa; color: #14b8a6; } .tag-teal { background: #f0fdfa; color: #14b8a6; }
    .icon-bg-cyan { background: #ecfeff; color: #06b6d4; } .tag-cyan { background: #ecfeff; color: #06b6d4; }

    /* LOWER COMPONENTS */
    .info-card { background: white; border-radius: 16px; border: none; }
    .info-title { font-size: 14px; font-weight: 700; color: #1e293b; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; margin: 0; }
    
    /* DONUT CHART (PERBAIKAN TOTAL POSISI PUSAT) */
    .chart-box-container { display: flex; justify-content: center; padding: 10px 0; }
    .donut-ring { width: 130px; height: 130px; border-radius: 50%; border: 12px solid #f1f5f9; border-top-color: #6366f1; border-right-color: #14b8a6; }
    
    .donut-center-absolute {
        position: absolute;
        top: 35%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .donut-center-absolute h3 { margin: 0; font-size: 24px; font-weight: 800; color: #1e293b; line-height: 1; }
    .donut-center-absolute p { margin: 2px 0 0 0; font-size: 9px; color: #94a3b8; font-weight: 700; letter-spacing: 0.5px; }
    
    .legend-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 6px; }
    .bg-indigo { background-color: #6366f1 !important; }
    .bg-teal { background-color: #14b8a6 !important; }
    .font-size-13 { font-size: 13px; } .font-size-8 { font-size: 8px; }

    /* PERBAIKAN WARNA HITUNGAN SIRKULASI BAWAH */
    .badge-count-indigo { background: #eef2ff; color: #6366f1; font-weight: 800; padding: 2px 10px; border-radius: 50px; font-size: 12px; }
    .badge-count-teal { background: #f0fdfa; color: #14b8a6; font-weight: 800; padding: 2px 10px; border-radius: 50px; font-size: 12px; }

    /* QUICK BOX GRID */
    .quick-box-item { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 15px 5px; border-radius: 12px; text-decoration: none !important; transition: all 0.2s; background: #fafafa; border: 1px solid #f1f5f9; }
    .quick-box-item:hover { transform: translateY(-2px); background: white; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
    .quick-box-item i { font-size: 20px; margin-bottom: 8px; }
    .quick-box-item span { font-size: 11px; font-weight: 600; color: #475569; }
    
    .q-blue i { color: #3b82f6; } .q-green i { color: #10b981; } .q-orange i { color: #f59e0b; }
    .q-purple i { color: #8b5cf6; } .q-cyan i { color: #06b6d4; } .q-dark i { color: #475569; }

    /* SYSTEM LIST */
    .list-system-info { display: flex; flex-direction: column; gap: 11px; }
    .sys-item { display: flex; align-items: center; gap: 12px; background: #f8fafc; padding: 8px 12px; border-radius: 10px; border: 1px solid #f1f5f9; }
    .sys-icon { width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 14px; }
    .sys-item label { font-size: 9px; font-weight: 800; color: #94a3b8; margin: 0; letter-spacing: 0.5px; }
    .sys-item p { font-size: 12px; font-weight: 700; color: #334155; margin: 0; }
</style>
@endsection