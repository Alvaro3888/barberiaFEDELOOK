<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Fede looK Barbería - Panel Admin</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CDN Bootstrap Icons & FontAwesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --black: #111111;
            --black-soft: #1c1c1c;
            --dark: #252525;
            --gray: #777777;
            --gray-dark: #555555;
            --gray-light: #f5f5f5;
            --gray-soft: #fafafa;
            --white: #ffffff;
            --petrol: #168F87;
            --petrol-light: #1FA69C;
            --petrol-dark: #0F746E;
            --petrol-soft: #E8F5F3;
            --accent: #168F87;
            --border: #dddddd;
            --border-soft: #eeeeee;
        }

        *, *::before, *::after {
            font-style: normal !important;
        }

        html, body {
            height: 100%;
            overflow: hidden;
            font-family: Arial, Helvetica, sans-serif;
            color: var(--dark);
            background-color: #f7f7f7;
        }

        #navbar {
            height: 68px;
            background: rgba(255, 255, 255, .92);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(220, 220, 220, .75);
            z-index: 1050;
        }

        .logo-header {
            width: 38px !important;
            height: 38px !important;
            object-fit: contain;
            display: block;
        }

        .header-nav-link {
            color: #686868 !important;
            text-decoration: none !important;
            transition: color .2s ease;
        }

        .header-nav-link:hover,
        .header-nav-link:focus {
            color: var(--petrol) !important;
        }

        .btn-petrol {
            background: #3BB5AC !important;
            border: 1px solid #3BB5AC !important;
            color: var(--white) !important;
            border-radius: 6px;
            font-weight: 600;
            transition: all .2s ease;
            box-shadow: 0 4px 10px rgba(22, 143, 135, .16);
        }

        .btn-petrol:hover {
            background: #3BB5AC !important;
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(22, 143, 135, .22);
        }

        .btn-outline-petrol {
            background: transparent !important;
            border: 1px solid var(--petrol) !important;
            color: var(--petrol) !important;
            border-radius: 6px;
            font-weight: 600;
            transition: all .2s ease;
        }

        .btn-outline-petrol:hover {
            background: var(--petrol-soft) !important;
            color: var(--petrol-dark) !important;
        }

        .sidebar-fixed {
            width: 240px;
            position: fixed;
            top: 68px;
            bottom: 0;
            left: 0;
            z-index: 1030;
            overflow-y: auto;
            background-color: var(--black-soft);
            padding: 1.25rem 0.75rem;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        .sidebar-nav .nav-link {
            color: #cccccc !important;
            font-weight: 500;
            font-size: 0.85rem;
            padding: 0.6rem 0.8rem;
            border-radius: 8px;
            transition: all 0.2s ease-in-out;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 4px;
        }

        .sidebar-nav .nav-link:hover {
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.08);
            transform: translateX(4px);
        }

        .sidebar-nav .nav-link.active {
            color: #ffffff !important;
            background-color: var(--petrol);
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(22, 143, 135, 0.35);
        }

        .sidebar-nav .nav-link i {
            font-size: 1.05rem;
            width: 20px;
            text-align: center;
        }

        .main-wrapper {
            margin-top: 68px;
            margin-left: 240px;
            height: calc(100vh - 68px);
            overflow-y: auto;
            overflow-x: hidden;
            width: calc(100% - 240px);
        }

        @media (max-width: 767.98px) {
            .sidebar-fixed {
                display: none;
            }
            .main-wrapper {
                margin-left: 0;
                width: 100%;
            }
        }

        .dropdown-menu {
            border: 1px solid var(--border);
            box-shadow: 0 15px 35px rgba(0, 0, 0, .10);
            border-radius: 12px;
            padding: 8px;
            background: rgba(255,255,255,.97);
            backdrop-filter: blur(12px);
        }

        .dropdown-item {
            border-radius: 8px;
            color: #555555;
            padding: .5rem .8rem;
            font-size: 0.9rem;
            transition: all .2s ease;
        }

        .dropdown-item:hover {
            background: var(--petrol-soft);
            color: var(--petrol-dark);
        }

        #mobileOverlay {
            background: rgba(0, 0, 0, .64);
            backdrop-filter: blur(5px);
            opacity: 0;
            visibility: hidden;
            transition: opacity .3s ease, visibility .3s ease;
        }

        #mobileOverlay.open {
            opacity: 1;
            visibility: visible;
        }

        #mobileMenu {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height .35s ease, opacity .25s ease;
        }

        #mobileMenu.open {
            max-height: 900px;
            opacity: 1;
        }

        .mobile-menu-container {
            background: rgba(255,255,255,.97);
            backdrop-filter: blur(20px);
            border-top: 1px solid var(--border-soft);
            box-shadow: 0 15px 35px rgba(0,0,0,.08);
        }

        .hamburger-line {
            transition: transform .3s ease, opacity .2s ease, background-color .2s ease;
        }

        .menu-open .line-1 { transform: translateY(7px) rotate(45deg); }
        .menu-open .line-2 { opacity: 0; }
        .menu-open .line-3 { transform: translateY(-7px) rotate(-45deg); }
    </style>
</head>

<body>

    <div id="mobileOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-md-none" style="z-index: 1040;"></div>

    <!-- Header Navbar Superior -->
    <header id="navbar" class="fixed-top">
        <nav class="container-fluid px-4 h-100">
            <div class="d-flex align-items-center justify-content-between h-100">

                <a href="{{ route('admin.dashboard_admin') }}" class="d-flex align-items-center gap-3 text-decoration-none" style="z-index: 1060;">
                    <img src="{{ asset('logos/cortebarba2.png') }}" alt="Fede looK Barbería" class="logo-header">
                    <span class="fs-5 fw-bold text-dark" style="letter-spacing: -.015em;">
                        Fede looK <span class="badge text-white fs-6 ms-1" style="background-color: var(--petrol) !important;">Admin</span>
                    </span>
                </a>

                <!-- Desktop: abre en pestaña nueva -->
                <div class="d-none d-md-flex align-items-center gap-4">
                    <a href="{{ url('/') }}" class="header-nav-link fw-medium" target="_blank">Ver Sitio Público <i class="bi bi-box-arrow-up-right small"></i></a>
                </div>

                <div class="d-none d-md-flex align-items-center gap-2">
                    @auth
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-petrol px-3 py-1-5 fw-semibold">
                                Cerrar sesión
                            </button>
                        </form>

                        <a href="{{ route('perfil.index') }}" class="d-inline-flex align-items-center text-decoration-none ms-2" title="Mi Perfil">
                            @php
                                $avatar = Auth::user()->avatar ?? null;
                                $isGoogleAvatar = $avatar && (\Illuminate\Support\Str::startsWith($avatar, ['http://', 'https://']));
                            @endphp

                            <img src="{{ $avatar ? ($isGoogleAvatar ? $avatar : asset('storage/' . $avatar)) : '#' }}" 
                                 alt="Avatar" 
                                 class="rounded-circle border border-2 border-white shadow-sm global-user-avatar {{ !$avatar ? 'd-none' : '' }}" 
                                 id="navbar-avatar-preview" 
                                 style="width: 38px; height: 38px; object-fit: cover;">

                            <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center shadow-sm border border-2 border-white {{ $avatar ? 'd-none' : '' }}" 
                                 id="navbar-avatar-placeholder" 
                                 style="width: 38px; height: 38px; font-weight: bold; font-size: 14px;">
                                {{ strtoupper(substr(Auth::user()->nombre ?? (Auth::user()->name ?? 'U'), 0, 1)) }}
                            </div>
                        </a>
                    @endauth
                </div>

                <div class="d-flex align-items-center gap-2 d-md-none">
                    @auth
                        <a href="{{ route('perfil.index') }}" class="d-inline-flex align-items-center text-decoration-none me-1" title="Mi Perfil">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="rounded-circle border border-2 border-white shadow-sm" style="width: 36px; height: 36px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center shadow-sm border border-2 border-white" style="width: 36px; height: 36px; font-weight: bold; font-size: 13px;">
                                    {{ strtoupper(substr(Auth::user()->nombre ?? Auth::user()->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                        </a>
                    @endauth

                    <button id="menuButton" type="button" aria-label="Abrir menú" aria-expanded="false" class="btn p-0 rounded-3 d-flex flex-column align-items-center justify-content-center" style="z-index: 1060; width: 44px; height: 44px; background: rgba(255,255,255,.88); border: 1px solid #222222;">
                        <span class="hamburger-line line-1 d-block rounded" style="width: 24px; height: 2px; margin: 2.5px 0; background: #111111;"></span>
                        <span class="hamburger-line line-2 d-block rounded" style="width: 24px; height: 2px; margin: 2.5px 0; background: #111111;"></span>
                        <span class="hamburger-line line-3 d-block rounded" style="width: 24px; height: 2px; margin: 2.5px 0; background: #111111;"></span>
                    </button>
                </div>

            </div>

            <!-- Desplegable Menú Móvil -->
            <div id="mobileMenu" class="d-md-none">
                <div class="mobile-menu-container rounded-bottom-4 pb-4 pt-3 mt-3 px-3">
                    @auth
                        @if(auth()->user()->rol_id == 1)
                            <a href="{{ url('/') }}" class="mobile-link nav-link p-2 rounded text-petrol fw-bold mb-2 border border-petrol" style="border-color: var(--petrol) !important;">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Ver Sitio Público
                            </a>

                            <div class="fw-bold small text-uppercase text-secondary mb-2 mt-3">Panel Admin</div>
                            <a href="{{ route('admin.dashboard_admin') }}" class="mobile-link nav-link p-2 rounded text-dark fw-semibold">
                                <i class="bi bi-speedometer2 me-2 text-petrol"></i> Dashboard
                            </a>
                            <a class="mobile-link nav-link p-2 rounded text-dark fw-semibold {{ request()->routeIs('admin.personal.*') ? 'active text-white' : '' }}" href="{{ route('admin.personal.index') }}">
                                <i class="fas fa-user-tie me-2 text-petrol"></i> <span>Personal / Barberos</span>
                            </a>
                            <a href="{{ route('admin.usuarios.index') }}" class="mobile-link nav-link p-2 rounded text-dark fw-semibold {{ request()->routeIs('admin.usuarios*') ? 'active text-white' : '' }}">
                                <i class="bi bi-people me-2 text-petrol"></i> Clientes
                            </a>
                            <a href="{{ route('admin.sucursales.index') }}" class="mobile-link nav-link p-2 rounded text-dark fw-semibold">
                                <i class="bi bi-shop me-2 text-petrol"></i> Sucursales
                            </a>
                            <a href="{{ route('admin.TipoServicios.index') }}" class="mobile-link nav-link p-2 rounded text-dark fw-semibold">
                                <i class="bi bi-grid me-2 text-petrol"></i> Tipo de Servicios
                            </a>
                            <a href="{{ route('admin.servicios.index') }}" class="mobile-link nav-link p-2 rounded text-dark fw-semibold">
                                <i class="bi bi-scissors me-2 text-petrol"></i> Servicios
                            </a>
                            <a href="{{ route('admin.turnos.index') }}" class="mobile-link nav-link p-2 rounded text-dark fw-semibold">
                                <i class="bi bi-calendar-check me-2 text-petrol"></i> Turnos
                            </a>
                            <a href="{{ route('admin.pagos.index') }}" class="mobile-link nav-link p-2 rounded text-dark fw-semibold">
                                <i class="bi bi-cash-stack me-2 text-petrol"></i> Movimientos de Pagos
                            </a>
                            <a href="{{ route('admin.calificaciones.index') }}" class="mobile-link nav-link p-2 rounded text-dark fw-semibold {{ request()->routeIs('admin.calificaciones*') ? 'active text-white' : '' }}">
                                <i class="bi bi-star me-2 text-petrol"></i> Calificaciones
                            </a>
                            <a href="{{ route('admin.configuracion.index') }}" class="mobile-link nav-link p-2 rounded text-dark fw-semibold {{ request()->routeIs('admin.configuracion*') ? 'active text-white' : '' }}">
                                <i class="bi bi-gear me-2 text-petrol"></i> Configuración
                            </a>
                            <a href="{{ route('perfil.index') }}" class="mobile-link nav-link p-2 rounded text-dark fw-semibold">
                                <i class="bi bi-person-gear me-2 text-petrol"></i> Mi Perfil
                            </a>

                            <form action="{{ route('logout') }}" method="POST" class="mt-3">
                                @csrf
                                <button type="submit" class="btn btn-outline-petrol w-100 py-2 fw-semibold">Cerrar sesión</button>
                            </form>
                        @endif
                    @endauth
                </div>
            </div>

        </nav>
    </header>

    <!-- Estructura Panel Admin -->
    <div class="d-flex">
        
        <!-- Sidebar Fijo Lateral Admin (Desktop) -->
        <nav class="sidebar-fixed shadow-sm">
            <h6 class="text-uppercase fw-bold px-3 mb-3" style="letter-spacing: .12em; font-size: 0.68rem; color: var(--petrol-light);">
                Gestión Admin
            </h6>
            <ul class="nav flex-column sidebar-nav">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard_admin') }}" class="nav-link {{ request()->routeIs('admin.dashboard_admin*') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.personal.*') ? 'active' : '' }}" href="{{ route('admin.personal.index') }}">
                        <i class="fas fa-user-tie"></i> <span>Personal / Barberos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.usuarios.index') }}" class="nav-link {{ request()->routeIs('admin.usuarios*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> <span>Clientes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.sucursales.index') }}" class="nav-link {{ request()->routeIs('admin.sucursales*') ? 'active' : '' }}">
                        <i class="bi bi-shop"></i> <span>Sucursales</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.TipoServicios.index') }}" class="nav-link {{ request()->routeIs('admin.TipoServicios*') ? 'active' : '' }}">
                        <i class="bi bi-grid"></i> <span>Tipo de Servicios</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.servicios.index') }}" class="nav-link {{ request()->routeIs('admin.servicios*') ? 'active' : '' }}">
                        <i class="bi bi-scissors"></i> <span>Servicios</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.turnos.index') }}" class="nav-link {{ request()->routeIs('admin.turnos*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-check"></i> <span>Turnos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.pagos.index') }}" class="nav-link {{ request()->routeIs('admin.pagos*') ? 'active' : '' }}">
                        <i class="bi bi-cash-stack"></i> <span>Movimientos de Pagos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.calificaciones.index') }}" class="nav-link {{ request()->routeIs('admin.calificaciones*') ? 'active' : '' }}">
                        <i class="bi bi-star"></i> <span>Calificaciones</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.configuracion.index') }}" class="nav-link {{ request()->routeIs('admin.configuracion*') ? 'active' : '' }}">
                        <i class="bi bi-gear"></i> <span>Configuración</span>
                    </a>
                </li>
            </ul>

            <h6 class="text-uppercase fw-bold px-3 mt-4 mb-3" style="letter-spacing: .12em; font-size: 0.68rem; color: var(--petrol-light);">
                Cuenta
            </h6>
            <ul class="nav flex-column sidebar-nav">
                <li class="nav-item">
                    <a href="{{ route('perfil.index') }}" class="nav-link {{ request()->routeIs('perfil*') ? 'active' : '' }}">
                        <i class="bi bi-person-gear"></i> <span>Mi Perfil</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Área de Contenido Principal -->
        <div class="main-wrapper">
            <main class="p-3 p-md-4">
                @yield('content')
            </main>
        </div>

    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const menuButton = document.getElementById('menuButton');
        const mobileMenu = document.getElementById('mobileMenu');
        const mobileOverlay = document.getElementById('mobileOverlay');

        function openMobileMenu() {
            mobileMenu.classList.add('open');
            mobileOverlay.classList.add('open');
            menuButton.classList.add('menu-open');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileMenu() {
            mobileMenu.classList.remove('open');
            mobileOverlay.classList.remove('open');
            menuButton.classList.remove('menu-open');
            document.body.style.overflow = '';
        }

        if (menuButton) {
            menuButton.addEventListener('click', function () {
                if (mobileMenu.classList.contains('open')) {
                    closeMobileMenu();
                } else {
                    openMobileMenu();
                }
            });
        }

        if (mobileOverlay) {
            mobileOverlay.addEventListener('click', closeMobileMenu);
        }

        document.querySelectorAll('.mobile-link').forEach(function (link) {
            link.addEventListener('click', closeMobileMenu);
        });
    </script>

</body>
</html>