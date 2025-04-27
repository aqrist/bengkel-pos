<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- PWA Meta Tags -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192x192.png') }}">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }

        .sidebar .nav-link {
            color: #fff;
        }

        .sidebar .nav-link:hover {
            background-color: #495057;
        }

        .sidebar .nav-link.active {
            background-color: #0d6efd;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 56px;
                bottom: 0;
                left: -250px;
                width: 250px;
                transition: 0.3s;
                z-index: 999;
            }

            .sidebar.show {
                left: 0;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <button class="btn btn-dark d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
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

    <div class="container-fluid mt-5 pt-3">
        <div class="row">
            @auth
                <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                    <div class="position-sticky pt-3">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                                    href="{{ route('dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('pos.index') ? 'active' : '' }}"
                                    href="{{ route('pos.index') }}">
                                    <i class="fas fa-cash-register me-2"></i> POS
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}"
                                    href="{{ route('products.index') }}">
                                    <i class="fas fa-box me-2"></i> Produk
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}"
                                    href="{{ route('categories.index') }}">
                                    <i class="fas fa-tags me-2"></i> Kategori
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}"
                                    href="{{ route('transactions.index') }}">
                                    <i class="fas fa-history me-2"></i> Transaksi
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>

                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                    @yield('content')
                </main>
            @else
                <main class="col-12">
                    @yield('content')
                </main>
            @endauth
        </div>
    </div>

    <!-- PWA Script -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/service-worker.js');
            });
        }
    </script>

    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
        });
    </script>

    @stack('scripts')
</body>

</html>
