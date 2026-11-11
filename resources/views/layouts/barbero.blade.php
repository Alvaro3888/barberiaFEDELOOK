<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Fede looK Barbería - Panel Barbero</title>

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
            background: #ffffff !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            border-bottom: 1px solid var(--border) !important;
            z-index: 1050;
        }

        .logo-header {
            width: 38px !important;
            height: 38px !important;
            object-fit: contain;
            display: block;
        }

        .header-nav-link {
            color: #4a5568 !important;
            font-weight: 500;
            text-decoration: none !important;
            transition: color .25s ease;
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
            width: 230px;
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
            font-size: 0.88rem;
            padding: 0.65rem 0.9rem;
            border-radius: 8px;
            transition: all 0.2s ease-in-out;
            display: flex;
            align-items: center;
            gap: 12px;
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
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        .main-wrapper {
            margin-top: 68px;
            margin-left: 230px;
            height: calc(100vh - 68px);
            overflow-y: auto;
            overflow-x: hidden;
            width: calc(100% - 230px);
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
            background: #ffffff;
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

        .dropdown-toggle.hide-arrow::after {
            display: none !important;
        }

        .user-avatar-btn {
            transition: opacity 0.2s ease !important;
        }
        
        .user-avatar-btn:hover {
            opacity: 0.85;
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
            background: #ffffff;
            border-top: 1px solid var(--border-soft);
            box-shadow: 0 15px 35px rgba(0,0,0,.08);
            max-height: calc(100vh - 80px);
            overflow-y: auto;
            padding-bottom: 2.5rem !important;
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

                <!-- 1. Logo / Identidad -->
                <a href="{{ route('barbero.turnos') }}" class="d-flex align-items-center gap-3 text-decoration-none" style="z-index: 1060;">
                    <img src="{{ asset('logos/cortebarba2.png') }}" alt="Fede looK Barbería" class="logo-header">
                    <span class="fs-5 fw-bold text-dark" style="letter-spacing: -.015em;">
                        Fede looK <span class="badge text-white fs-6 ms-1" style="background-color: var(--petrol) !important;">Barbero</span>
                    </span>
                </a>

                <!-- 2. Enlace a sitio público (Escritorio) -->
                <div class="d-none d-md-flex align-items-center gap-4">
                    <a href="{{ url('/') }}" class="header-nav-link fw-medium" target="_blank">Ver Sitio Público <i class="bi bi-box-arrow-up-right small"></i></a>
                </div>

                <!-- 3. Perfil de Usuario / Avatar con menú desplegable (Escritorio) -->
                <div class="d-none d-md-flex align-items-center gap-2">
                    @auth
                        <div class="dropdown d-flex align-items-center">
                            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle hide-arrow user-avatar-btn" id="userMenuDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                @php
                                    $avatar = Auth::user()->avatar ?? null;
                                    $isGoogleAvatar = $avatar && (\Illuminate\Support\Str::startsWith($avatar, ['http://', 'https://']));
                                @endphp

                                @if($avatar)
                                    <img src="{{ $isGoogleAvatar ? $avatar : asset('storage/' . $avatar) }}" alt="Avatar" class="rounded-circle border border-2 border-white shadow-sm global-user-avatar" id="navbar-avatar-preview" style="width: 42px; height: 42px; object-fit: cover;">
                                @else
                                    <img src="#" alt="Avatar" class="rounded-circle border border-2 border-white shadow-sm global-user-avatar d-none" id="navbar-avatar-preview" style="width: 42px; height: 42px; object-fit: cover;">
                                    <div id="navbar-avatar-placeholder" class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center shadow-sm border border-2 border-white" style="width: 42px; height: 42px; font-weight: bold; font-size: 14px;">
                                        {{ strtoupper(substr(Auth::user()->nombre ?? (Auth::user()->name ?? 'U'), 0, 1)) }}
                                    </div>
                                @endif
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 py-2 mt-2" aria-labelledby="userMenuDropdown" style="border-radius: 12px; min-width: 210px;">
                                <li class="px-3 py-2 mb-1 text-muted small border-bottom text-truncate" style="max-width: 220px;">
                                    Hola, <strong class="text-dark">{{ Auth::user()->nombre ?? (Auth::user()->name ?? 'Usuario') }}</strong>
                                </li>
                                <li><a class="dropdown-item py-2" href="{{ route('perfil.index') }}"><i class="bi bi-person-gear me-2 text-muted"></i> Mi Perfil</a></li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2 text-danger w-100 text-start"><i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endauth
                </div>

                <!-- Control Móvil (Solo Botón Hamburguesa, sin avatar duplicado afuera) -->
                <div class="d-flex align-items-center gap-2 d-md-none">
                    <button id="menuButton" type="button" aria-label="Abrir menú" aria-expanded="false" class="btn p-0 rounded-3 d-flex flex-column align-items-center justify-content-center border-0 bg-transparent" style="z-index: 1060; width: 42px; height: 42px; border: 1px solid var(--border) !important;">
                        <span class="hamburger-line line-1 d-block rounded-pill" style="width: 22px; height: 2px; margin: 3px 0; background: #111111;"></span>
                        <span class="hamburger-line line-2 d-block rounded-pill" style="width: 22px; height: 2px; margin: 3px 0; background: #111111;"></span>
                        <span class="hamburger-line line-3 d-block rounded-pill" style="width: 22px; height: 2px; margin: 3px 0; background: #111111;"></span>
                    </button>
                </div>

            </div>

            <!-- Desplegable Menú Móvil -->
            <div id="mobileMenu" class="d-md-none">
                <div class="mobile-menu-container rounded-bottom-4 pb-4 pt-3 mt-2 px-3 shadow-lg">
                    @auth
                        <!-- Tarjeta de perfil dentro del menú móvil -->
                        <div class="d-flex align-items-center gap-3 px-3 py-3 mb-3 bg-light rounded-4 border-0">
                            @php
                                $avatar = Auth::user()->avatar ?? null;
                                $isGoogleAvatar = $avatar && (\Illuminate\Support\Str::startsWith($avatar, ['http://', 'https://']));
                            @endphp

                            @if($avatar)
                                <img src="{{ $isGoogleAvatar ? $avatar : asset('storage/' . $avatar) }}" alt="Avatar" class="rounded-circle border border-2 border-white shadow-sm" style="width: 42px; height: 42px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center shadow-sm border border-2 border-white" style="width: 42px; height: 42px; font-weight: bold; font-size: 15px;">
                                    {{ strtoupper(substr(Auth::user()->nombre ?? (Auth::user()->name ?? 'U'), 0, 1)) }}
                                </div>
                            @endif

                            <div class="text-truncate">
                                <div class="fw-bold text-dark text-truncate small">{{ Auth::user()->nombre ?? (Auth::user()->name ?? 'Usuario') }}</div>
                                <a href="{{ route('perfil.index') }}" class="text-decoration-none text-muted" style="font-size: 12px;">Editar perfil</a>
                            </div>
                        </div>

                        @if(auth()->user()->rol_id == 2)
                            <a href="{{ url('/') }}" class="mobile-link nav-link p-3 rounded-3 text-petrol fw-bold mb-2 border border-petrol d-flex align-items-center" style="border-color: var(--petrol) !important;">
                                <i class="bi bi-box-arrow-in-right me-3 fs-5"></i> Ver Sitio Público
                            </a>

                            <div class="fw-bold small text-uppercase text-secondary mb-2 mt-3 px-1">Mi Panel (Barbero)</div>
                            
                            <nav class="d-flex flex-column gap-1">
                                <a href="{{ route('barbero.turnos') }}" class="mobile-link nav-link px-3 py-2.5 rounded-3 text-dark fw-semibold {{ request()->routeIs('barbero.turnos*') ? 'bg-light text-petrol' : '' }}">
                                    <i class="bi bi-calendar-check-fill me-3 text-petrol fs-5"></i> Mis Turnos
                                </a>
                                <a href="{{ route('barbero.progreso') }}" class="mobile-link nav-link px-3 py-2.5 rounded-3 text-dark fw-semibold {{ request()->routeIs('barbero.progreso*') ? 'bg-light text-petrol' : '' }}">
                                    <i class="bi bi-graph-up-arrow me-3 text-petrol fs-5"></i> Mi Progreso
                                </a>
                            </nav>

                            <div class="border-top mt-3 pt-3 px-1">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-light w-100 py-3 fw-semibold text-danger border-0 rounded-3 text-start px-3 d-flex align-items-center">
                                        <i class="bi bi-box-arrow-right me-3 fs-5"></i> Cerrar sesión
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>

        </nav>
    </header>

    <!-- Estructura Panel Barbero -->
    <div class="d-flex">
        
        <!-- Sidebar Fijo Lateral Barbero (Desktop) -->
        <nav class="sidebar-fixed shadow-sm">
            <h6 class="text-uppercase fw-bold px-3 mb-3" style="letter-spacing: .12em; font-size: 0.68rem; color: var(--petrol-light);">
                Mi Panel
            </h6>
            <ul class="nav flex-column sidebar-nav">
                <li class="nav-item">
                    <a href="{{ route('barbero.turnos') }}" class="nav-link {{ request()->routeIs('barbero.turnos*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-check-fill"></i> <span>Mis Turnos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('barbero.progreso') }}" class="nav-link {{ request()->routeIs('barbero.progreso*') ? 'active' : '' }}">
                        <i class="bi bi-graph-up-arrow"></i> <span>Mi Progreso</span>
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
            if (!mobileMenu || !mobileOverlay || !menuButton) return;
            mobileMenu.classList.add('open');
            mobileOverlay.classList.add('open');
            menuButton.classList.add('menu-open');
            menuButton.setAttribute('aria-expanded', 'true');
            menuButton.setAttribute('aria-label', 'Cerrar menú');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileMenu() {
            if (!mobileMenu || !mobileOverlay || !menuButton) return;
            mobileMenu.classList.remove('open');
            mobileOverlay.classList.remove('open');
            menuButton.classList.remove('menu-open');
            menuButton.setAttribute('aria-expanded', 'false');
            menuButton.setAttribute('aria-label', 'Abrir menú');
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

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && mobileMenu && mobileMenu.classList.contains('open')) {
                closeMobileMenu();
            }
        });

        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                closeMobileMenu();
            }
        });
    </script>

</body>
</html>