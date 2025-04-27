<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0d6efd">

    <title>{{ config('app.name', 'Bengkel POS') }}</title>

    <!-- PWA Meta Tags -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192x192.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 56px;
            /* Height of navbar */
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, .1);
        }

        .sidebar {
            background-color: #343a40;
            min-height: calc(100vh - 56px);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, .8);
            padding: 1rem;
            border-left: 3px solid transparent;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, .1);
        }

        .sidebar .nav-link.active {
            color: #fff;
            background-color: #0d6efd;
            border-left-color: #fff;
        }

        .sidebar .nav-link i {
            width: 24px;
            text-align: center;
        }

        /* Mobile specific styles */
        @media (max-width: 991.98px) {
            .sidebar {
                position: fixed;
                top: 56px;
                bottom: 0;
                left: -250px;
                width: 250px;
                transition: all 0.3s;
                z-index: 1040;
                overflow-y: auto;
            }

            .sidebar.show {
                left: 0;
            }

            .sidebar-backdrop {
                position: fixed;
                top: 56px;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1030;
                display: none;
            }

            .sidebar-backdrop.show {
                display: block;
            }

            .main-content {
                margin-left: 0 !important;
            }
        }

        @media (min-width: 992px) {
            .sidebar {
                position: fixed;
                top: 56px;
                bottom: 0;
                left: 0;
                width: 250px;
            }

            .main-content {
                margin-left: 250px;
            }
        }

        /* Card responsive */
        .card {
            margin-bottom: 1rem;
        }

        /* Table responsive */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Button spacing on mobile */
        @media (max-width: 576px) {
            .btn-group-mobile {
                display: flex;
                flex-direction: column;
            }

            .btn-group-mobile .btn {
                margin-bottom: 0.5rem;
                width: 100%;
            }

            .btn-group-mobile .btn:last-child {
                margin-bottom: 0;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            @auth
                <button class="btn btn-dark d-lg-none me-2" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            @endauth
            <a class="navbar-brand" href="{{ url('/') }}">Bengkel POS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    @auth
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="position-sticky">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                            href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('pos.index') ? 'active' : '' }}"
                            href="{{ route('pos.index') }}">
                            <i class="fas fa-cash-register"></i>
                            <span>POS</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}"
                            href="{{ route('products.index') }}">
                            <i class="fas fa-box"></i>
                            <span>Produk</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}"
                            href="{{ route('categories.index') }}">
                            <i class="fas fa-tags"></i>
                            <span>Kategori</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}"
                            href="{{ route('transactions.index') }}">
                            <i class="fas fa-history"></i>
                            <span>Transaksi</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
    @endauth

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid py-3">
            @yield('content')
        </div>
    </main>

    <!-- PWA Script -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/service-worker.js');
            });
        }
    </script>

    <script>
        // Sidebar toggle for mobile
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
                sidebarBackdrop.classList.toggle('show');
            });

            sidebarBackdrop?.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarBackdrop.classList.remove('show');
            });
        }
    </script>

    @stack('scripts')
</body>

</html>
