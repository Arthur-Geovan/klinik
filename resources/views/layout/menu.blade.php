<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="icon" href="{{ asset('assets/img/logo.png') }}" type="image/png" />
    <title>{{ isset($judul) ? $judul : 'STMIK Banjarbaru' }}</title>

    <link href="{{ asset('assets/lib/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/lib/Ionicons/css/ionicons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/lib/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/lib/jquery-toggles/toggles-full.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/lib/rickshaw/rickshaw.min.css') }}" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="{{ asset('assets/css/amanda.css') }}">
</head>

<body>

    <div class="am-header">
        <div class="am-header-left">
            <a id="naviconLeft" href="" class="am-navicon d-none d-lg-flex"><i class="icon ion-navicon-round"></i></a>
            <a id="naviconLeftMobile" href="" class="am-navicon d-lg-none"><i class="icon ion-navicon-round"></i></a>
            <a href="{{ route('home') }}" class="am-logo">PERPUSTAKAAN STMIK</a>
        </div>

        <div class="am-header-right">
            <div class="dropdown dropdown-profile">
                <a href="" class="nav-link nav-link-profile" data-toggle="dropdown">
                    <img src="{{ asset('assets/img/predator.jpg') }}" class="wd-32 rounded-circle" alt="">
                    <span class="logged-name">
                        <span class="hidden-xs-down">
                            @auth
                                {{ Auth::user()->username }}
                            @else
                                Guest
                            @endauth
                        </span>
                        <i class="fa fa-angle-down mg-l-3"></i>
                    </span>
                </a>
            </div>
            <div class="dropdown dropdown-profile">
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link nav-link text-danger p-0 border-0">
                        <i class="icon ion-power"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="am-sideleft">
        <ul class="nav am-sideleft-tab">
            <li class="nav-item">
                <a href="#" class="nav-link active"><i class="icon ion-ios-home-outline tx-24"></i></a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link non"></a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link non"></a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link non"></a>
            </li>
        </ul>
        <style>
            .non {
                pointer-events: none;
            }
        </style>

        <div class="tab-content">
            <div id="mainMenu" class="tab-pane active">
                <ul class="nav am-sideleft-menu">
                    
                    <li class="nav-item">
                        <a href="{{ route('home') }}" class="nav-link {{ Request::routeIs('home') ? 'active' : '' }}">
                            <i class="icon ion-ios-home-outline"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('anggota.index') }}" class="nav-link {{ Request::routeIs('anggota*') ? 'active' : '' }}">
                            <i class="icon ion-ios-people-outline"></i>
                            <span>Data Anggota</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('buku.index') }}" class="nav-link {{ Request::routeIs('buku*') ? 'active' : '' }}">
                            <i class="icon ion-ios-book-outline"></i>
                            <span>Data Buku</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('kategori.index') }}" class="nav-link {{ Request::routeIs('kategori*') ? 'active' : '' }}">
                            <i class="icon ion-ios-bookmarks-outline"></i>
                            <span>Data Kategori Buku</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('pengguna.index') }}" class="nav-link {{ Request::routeIs('pengguna*') ? 'active' : '' }}">
                            <i class="icon ion-android-person"></i>
                            <span>Data Pengguna</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('peminjaman.index') }}" class="nav-link {{ Request::routeIs('peminjaman*') ? 'active' : '' }}">
                            <i class="icon ion-ios-loop"></i>
                            <span>Data Peminjaman</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('pengembalian.index') }}" class="nav-link {{ Request::routeIs('pengembalian*') ? 'active' : '' }}">
                            <i class="icon ion-ios-checkmark-outline"></i>
                            <span>Data Pengembalian</span>
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </div>

    <div class="am-mainpanel">
        <div class="am-pagetitle">
            <h5 class="am-title">{{ isset($judul) ? $judul : 'Dashboard' }}</h5>
        </div>
        <div class="am-pagebody">
            @yield('konten')
        </div>
    </div>

    <script src="{{ asset('assets/lib/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/lib/popper.js/popper.js') }}"></script>
    <script src="{{ asset('assets/lib/bootstrap/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/lib/perfect-scrollbar/js/perfect-scrollbar.jquery.js') }}"></script>
    <script src="{{ asset('assets/lib/jquery-toggles/toggles.min.js') }}"></script>
    <script src="{{ asset('assets/lib/d3/d3.js') }}"></script>
    <script src="{{ asset('assets/lib/rickshaw/rickshaw.min.js') }}"></script>
    <script src="{{ asset('assets/lib/Flot/jquery.flot.js') }}"></script>
    <script src="{{ asset('assets/lib/Flot/jquery.flot.pie.js') }}"></script>
    <script src="{{ asset('assets/lib/Flot/jquery.flot.resize.js') }}"></script>
    <script src="{{ asset('assets/lib/flot-spline/jquery.flot.spline.js') }}"></script>

    <script src="{{ asset('assets/js/amanda.js') }}"></script>
    <script src="{{ asset('assets/js/ResizeSensor.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>

</body>

</html>