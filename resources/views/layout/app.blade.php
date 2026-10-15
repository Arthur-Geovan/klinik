<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>Sistem Informasi Klinik Harapan Bangsa Ngawi Timur</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<style>
		:root {
			--clinic-navy: #17324d;
			--clinic-blue: #236b8e;
			--clinic-teal: #2aa79b;
			--clinic-mist: #f2f7f8;
			--clinic-line: #dbe8eb;
		}
		body { background-color: var(--clinic-mist); color: #203246; }
		.topbar {
			display: flex;
			align-items: center;
			justify-content: space-between;
			min-height: 45px;
			padding: 0 18px;
			background: var(--clinic-navy);
			border-bottom: 3px solid var(--clinic-teal);
			color: #fff;
			font-size: 13px;
		}
		.topbar-date,
		.topbar-user,
		.topbar-action,
		.topbar-clinic {
			display: inline-flex;
			align-items: center;
		}
		.topbar-date { gap: 5px; font-weight: 600; }
		.topbar-date i { font-size: 12px; }
		.topbar-clinic {
			gap: 10px;
			margin-left: 12px;
			padding-left: 12px;
			border-left: 1px solid rgba(255, 255, 255, 0.25);
			font-weight: 700;
			letter-spacing: 0.02em;
			white-space: nowrap;
		}
		.topbar-clinic .clinic-name {
			font-size: 12px;
			line-height: 1.2;
			text-align: left;
			color: #ffffff;
		}
		.topbar-user { gap: 7px; }
		.topbar-avatar {
			width: 28px;
			height: 28px;
			border: 2px solid rgba(255, 255, 255, 0.9);
			border-radius: 50%;
			object-fit: cover;
		}
		.topbar-user i { font-size: 17px; }
		.topbar-action {
			padding: 0 14px;
			min-height: 24px;
			border-right: 1px solid rgba(255, 255, 255, 0.55);
			color: #fff;
		}
		.topbar-action:hover { color: #d9f3ff; }
		.notification-menu {
			width: 320px;
			padding: 0;
			border: 0;
			box-shadow: 0 10px 26px rgba(31, 45, 61, 0.18);
		}
		.notification-header {
			padding: 12px 15px;
			border-bottom: 1px solid #edf0f4;
			color: #17253b;
			font-weight: 700;
		}
		.notification-item {
			display: block;
			padding: 11px 15px;
			border-bottom: 1px solid #f1f3f5;
			color: #344054;
			text-decoration: none;
			white-space: normal;
		}
		.notification-item:hover { background: #eef8f7; color: var(--clinic-teal); }
		.notification-title { display: block; font-size: 12px; font-weight: 700; }
		.notification-message { display: block; margin-top: 2px; font-size: 12px; }
		.notification-date { display: block; margin-top: 4px; color: #8a96a8; font-size: 11px; }
		.notification-empty { padding: 16px 15px; color: #8a96a8; font-size: 12px; }
		.notification-badge {
			position: absolute;
			top: -6px;
			right: 5px;
			min-width: 16px;
			height: 16px;
			padding: 1px 4px;
			border-radius: 8px;
			background: #ef4444;
			color: #fff;
			font-size: 10px;
			line-height: 14px;
			text-align: center;
		}
		.topbar-user-name {
			max-width: 130px;
			overflow: hidden;
			text-overflow: ellipsis;
			white-space: nowrap;
		}
		.sidebar {
			min-height: 100vh;
			background: linear-gradient(180deg, #17324d 0%, #112d43 100%);
			color: #fff;
			width: 260px;
			overflow: hidden;
			position: relative;
			white-space: nowrap;
			border-right: 0;
			box-shadow: 8px 0 25px rgba(14, 30, 42, 0.12);
			padding-top: 16px;
		}
		.sidebar h4 {
			color: #8ee1d6 !important;
			margin: 0;
			white-space: nowrap;
		}
		.sidebar small.text-muted {
			color: #aebdce !important;
			font-size: 10px;
			font-weight: 700;
			letter-spacing: 0.8px;
			white-space: nowrap;
		}
		.sidebar a {
			color: #dfeaf5;
			text-decoration: none;
			padding: 12px 16px;
			display: flex;
			align-items: center;
			justify-content: flex-start;
			gap: 12px;
			width: calc(100% - 16px);
			border-radius: 12px;
			transition: all 0.2s ease;
			white-space: nowrap;
			font-size: 0.95rem;
			font-weight: 600;
			margin: 4px 8px;
			border: 1px solid transparent;
			box-shadow: inset 0 0 0 0 rgba(255,255,255,0);
		}
		.sidebar a:hover {
			background: rgba(255,255,255,0.04);
			color: #ffffff;
			border-color: rgba(255,255,255,0.08);
			transform: translateX(2px);
		}
		.sidebar a.active {
			background: linear-gradient(90deg, rgba(42,167,155,0.22), rgba(35,107,142,0.18));
			color: #ffffff;
			border-color: rgba(142,225,214,0.3);
			box-shadow: inset 0 0 0 1px rgba(142,225,214,0.14), 0 8px 18px rgba(10,20,32,0.15);
		}
		.sidebar a i {
			color: #9ae0d2;
			width: 18px;
			text-align: center;
			flex-shrink: 0;
			font-size: 0.98rem;
			line-height: 1;
			transition: color 0.2s ease, transform 0.2s ease;
			filter: drop-shadow(0 0 4px rgba(14, 30, 42, 0.12)) drop-shadow(0 3px 6px rgba(14, 30, 42, 0.18));
		}
		.sidebar .menu-text {
			display: inline-block;
			flex: 1;
			text-align: left;
		}
		.sidebar a:hover i,
		.sidebar a.active i {
			color: #ffffff;
			transform: scale(1.06);
			filter: drop-shadow(0 0 5px rgba(14, 30, 42, 0.12)) drop-shadow(0 4px 8px rgba(14, 30, 42, 0.22));
		}
		.sidebar .menu-text,
		.sidebar .logout-text,
		.sidebar .menu-group-label {
			display: inline-block;
		}
		.sidebar-header {
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 12px;
			margin-bottom: 12px;
			min-height: 120px;
			padding: 18px 14px 12px;
			height: auto;
		}
		.sidebar-brand {
			display: flex;
			align-items: center;
			justify-content: flex-start;
			flex: 1;
			min-width: 0;
			gap: 16px;
			padding: 0 6px;
			height: 100%;
			margin: 0 auto;
			width: 100%;
		}
		.sidebar-brand .brand-name {
			display: block;
		}
		.brand-mark {
			display: flex;
			align-items: center;
			justify-content: center;
			position: relative;
			width: 78px;
			height: 78px;
			min-width: 78px;
			border-radius: 20px;
			background: linear-gradient(135deg, #69f0d5 0%, #36b9f4 100%);
			color: #fff;
			font-size: 1.7rem;
			box-shadow: 0 0 0 2px rgba(255,255,255,0.12), 0 18px 30px rgba(64, 199, 224, 0.35), 0 0 22px rgba(126, 239, 255, 0.7), 0 0 38px rgba(54, 185, 244, 0.45);
			padding: 0;
			margin: 0 auto;
			text-align: center;
			flex-shrink: 0;
			transform: translateX(0) scale(1.04);
			align-self: center;
			overflow: hidden;
		}
		.brand-mark::before {
			content: "";
			position: absolute;
			inset: 0;
			background: linear-gradient(120deg, rgba(255,255,255,0.52), rgba(255,255,255,0), rgba(255,255,255,0.28));
			transform: translateX(-100%);
			animation: brandShine 3s ease-in-out infinite;
		}
		.brand-logo {
			width: 100%;
			height: 100%;
			object-fit: cover;
			display: block;
			filter: drop-shadow(0 0 10px rgba(135, 240, 255, 0.45)) drop-shadow(0 8px 16px rgba(16, 62, 77, 0.24));
			position: relative;
			z-index: 1;
		}
		.sidebar-brand h4 {
			color: #edfdfb !important;
			display: flex;
			flex-direction: column;
			align-items: flex-start;
			justify-content: center;
			text-align: left;
			margin: 0;
			white-space: normal;
			word-break: normal;
			overflow-wrap: break-word;
			line-height: 1.08;
			font-size: 1.08rem;
			letter-spacing: 0.02em;
			font-weight: 900;
			max-width: 140px;
			text-shadow: 0 0 16px rgba(124, 232, 255, 0.55), 0 5px 14px rgba(19, 68, 104, 0.28);
		}
		.sidebar-brand h4 span {
			display: block;
			white-space: normal;
		}
		@keyframes brandShine {
			0% { transform: translateX(-110%); }
			35%, 100% { transform: translateX(110%); }
		}
	</style>
</head>
<body>
	<div class="d-flex">
		@auth
		<div id="sidebarMenu" class="sidebar p-3 style-sidebar" style="width: 250px;">
			<div class="sidebar-header">
				<div class="sidebar-brand">
					<span class="brand-mark">
						<img src="{{ asset('assets/img/WhatsApp Image 2026-09-23 at 13.22.23.jpeg') }}" alt="Logo Klinik Harapan Bangsa" class="brand-logo">
					</span>
					<h4 class="brand-name mb-0">
						<span>Klinik</span>
						<span>Harapan Bangsa</span>
						<span>Ngawi Timur</span>
					</h4>
				</div>
			</div>
			<hr class="text-secondary">
			<small class="menu-group-label text-muted px-3 text-uppercase font-weight-bold">Menu Utama</small>
			<a href="{{ route('dashboard') }}" class="mt-2"><i class="fa-solid fa-gauge me-2"></i> <span class="menu-text">Dashboard</span></a>
			@if(Auth::user()->role === 'apoteker')
				<a href="{{ route('apoteker.resep.index') }}"><i class="fa-solid fa-prescription-bottle-medical me-2"></i> <span class="menu-text">Resep Masuk</span></a>
				<a href="{{ route('obat.index') }}"><i class="fa-solid fa-pills me-2"></i> <span class="menu-text">Data Obat</span></a>
			@else
			@if(Auth::user()->role === 'admin')
				<small class="menu-group-label text-muted px-3 mt-3 d-block text-uppercase font-weight-bold">Master Data</small>
				<a href="{{ route('poli.index') }}"><i class="fa-solid fa-clinic-medical me-2"></i> <span class="menu-text">Data Poli</span></a>
				<a href="{{ route('dokter.index') }}"><i class="fa-solid fa-user-md me-2"></i> <span class="menu-text">Data Dokter</span></a>
				<a href="{{ route('jadwal.index') }}"><i class="fa-solid fa-calendar-days me-2"></i> <span class="menu-text">Jadwal Praktik</span></a>
				<a href="{{ route('obat.index') }}"><i class="fa-solid fa-pills me-2"></i> <span class="menu-text">Data Obat</span></a>
				<a href="{{ route('pembayaran.index') }}"><i class="fa-solid fa-file-invoice-dollar me-2"></i> <span class="menu-text">Pembayaran</span></a>
			@endif
			<small class="menu-group-label text-muted px-3 mt-3 d-block text-uppercase font-weight-bold">Pelayanan</small>
			<a href="{{ route('reservasi.index') }}"><i class="fa-solid fa-calendar-check me-2"></i> <span class="menu-text">Reservasi</span></a>
			@if(Auth::user()->role === 'pasien')
			<a href="{{ route('chatbot.index') }}"><i class="fa-solid fa-robot me-2"></i> <span class="menu-text">Tanya AI Klinik</span></a>
			@endif
			@endif
			<div class="mt-5 px-3">
				<form action="{{ route('logout') }}" method="POST" class="logout-form">
					@csrf
					<button type="submit" class="btn btn-danger w-100 logout-button" style="display:flex; align-items:center; justify-content:center; gap:8px; background: linear-gradient(180deg, #f5695d 0%, #ea4646 100%); border:none; box-shadow: inset 0 0 0 1px rgba(255,255,255,0.1);">
						<i class="fa-solid fa-right-from-bracket"></i>
						<span class="logout-btn-text">Logout</span>
					</button>
				</form>
			</div>
		</div>
		@endauth
		<div class="flex-grow-1">
			@auth
			<nav class="topbar" aria-label="Informasi akun">
				<div class="topbar-date">
					<i class="fa-solid fa-calendar-days" aria-hidden="true"></i>
					<span id="topbarDate">Memuat tanggal...</span>
					<span>|</span>
					<i class="fa-solid fa-clock" aria-hidden="true"></i>
					<span id="topbarTime">00:00:00</span>
				</div>
				<div class="topbar-user">
					<div class="dropdown">
						<a href="#" class="topbar-action position-relative notification-toggle" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifikasi">
							<i class="fa-solid fa-bell" aria-hidden="true"></i>
							@if($notifications->isNotEmpty())
								<span class="notification-badge">{{ $notifications->count() }}</span>
							@endif
						</a>
						<div class="dropdown-menu dropdown-menu-end notification-menu">
							<div class="notification-header">Notifikasi</div>
							@forelse($notifications as $notification)
								<a href="{{ $notification['url'] }}" class="notification-item">
									<span class="notification-title">{{ $notification['title'] }}</span>
									<span class="notification-message">{{ $notification['message'] }}</span>
									<span class="notification-date">{{ $notification['date'] }}</span>
								</a>
							@empty
								<div class="notification-empty">Belum ada notifikasi.</div>
							@endforelse
						</div>
					</div>
					<span class="topbar-user-name" title="{{ Auth::user()->name }}">
						<img src="{{ asset('assets/img/predator.jpg') }}" class="topbar-avatar" alt="Foto {{ Auth::user()->name }}">
						{{ Auth::user()->name }}
					</span>
				</div>
			</nav>
			@endauth
			<div class="p-4">
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
				title: 'Terjadi kesalahan',
				text: @json($errors->first()),
				icon: 'error'
			});
		</script>
	@endif

	<script>
		function confirmDelete(form) {
			Swal.fire({
				title: 'Yakin hapus data?',
				text: 'Data yang dihapus tidak dapat dikembalikan!',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Ya, hapus!',
				cancelButtonText: 'Batal'
			}).then((result) => {
				if (result.isConfirmed) {
					form.submit();
				}
			});

			return false;
		}
	</script>
				@yield('content')
			</div>
		</div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<script>
		document.querySelector('.notification-toggle')?.addEventListener('click', function () {
			const badge = this.querySelector('.notification-badge');
			if (badge) badge.remove();

			fetch('{{ route('notifications.read') }}', {
				method: 'POST',
				headers: {
					'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
					'Accept': 'application/json'
				}
			});
		});

		document.addEventListener('DOMContentLoaded', function () {
			const dateElement = document.getElementById('topbarDate');
			const timeElement = document.getElementById('topbarTime');
			const updateTopbarClock = () => {
				const now = new Date();
				if (dateElement) {
					dateElement.textContent = new Intl.DateTimeFormat('id-ID', {
						weekday: 'long', day: '2-digit', month: 'short', year: 'numeric'
					}).format(now);
				}
				if (timeElement) {
					timeElement.textContent = now.toLocaleTimeString('id-ID', { hour12: false });
				}
			};
			updateTopbarClock();
			setInterval(updateTopbarClock, 1000);

			});
	</script>
</body>
</html>
