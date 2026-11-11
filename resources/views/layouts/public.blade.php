<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Configuración esencial -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Título optimizado para SEO -->
    <title>Fede looK | Barbería y Estética en San Pedro de Jujuy</title>
    
    <!-- Meta Descripción SEO (Crucial para aparecer en Google) -->
    <meta name="description" content="Reserva tu turno online en Fede looK, la mejor barbería y centro de estética en San Pedro de Jujuy. Cortes modernos, barba, perfilado y atención profesional.">
    
    <!-- Palabras clave y metadatos del sitio -->
    <meta name="keywords" content="barberia, peluqueria, san pedro de jujuy, turnos online, cortes de cabello, barba, estetica, fede look">
    <meta name="author" content="Fede looK Barbería">
    <meta name="robots" content="index, follow">

    <!-- Open Graph para Redes Sociales y WhatsApp -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://fedelook.codexmf.com">
    <meta property="og:title" content="Fede looK | Barbería y Estética en San Pedro de Jujuy">
    <meta property="og:description" content="Reserva tu turno online de manera rápida y sencilla. Cortes, barba y el mejor estilo.">
    <meta property="og:image" content="{{ asset('images/sillas-barberia.webp') }}">

    <!-- Geolocalización para SEO Local en Argentina -->
    <meta name="geo.region" content="AR-Y">
    <meta name="geo.placename" content="San Pedro de Jujuy">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Preconexión de CDNs para acelerar la carga -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">

    <!-- Hojas de Estilo Externas -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Estilos CSS Personalizados -->
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
            --cream: #fafafa;
            --beige: #7A5638;
            --beige-light: #916A47;
            --beige-dark: #62432C;
            --beige-border: #5E402A;
            --petrol: #168F87;
            --petrol-light: #1FA69C;
            --petrol-dark: #0F746E;
            --petrol-soft: #E8F5F3;
            --accent: #168F87;
            --accent-light: #1FA69C;
            --accent-dark: #0F746E;
            --red: #168F87;
            --border: #dddddd;
            --border-soft: #eeeeee;
        }

        html {
            scroll-behavior: smooth;
            scroll-padding-top: 90px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            color: var(--dark);
            background: var(--white);
        }

        a {
            transition: color .2s ease;
        }

        /* Sección hero optimizada para pantallas verticales y horizontales (landscape) */
        .hero-section {
            padding-top: 95px !important;
            padding-bottom: 45px !important;
            min-height: 100vh;
            background-image: url('{{ asset("images/sillas-barberia.webp") }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        /* Regla estricta para celulares en modo horizontal */
        @media (max-height: 500px) {
            .hero-section {
                min-height: auto !important;
                padding-top: 80px !important;
                padding-bottom: 30px !important;
            }
            .hero-logo-wrapper {
                display: none !important;
            }
            .hero-title {
                font-size: 1.8rem !important;
                margin-bottom: 0.2rem !important;
            }
            .hero-badge {
                margin-bottom: 0.2rem !important;
                padding: 0.2rem 0.6rem !important;
                font-size: 0.65rem !important;
            }
            .beige-line {
                margin-bottom: 0.5rem !important;
            }
            .hero-subtitle-text {
                font-size: 0.85rem !important;
                margin-bottom: 0.5rem !important;
            }
        }

        .bg-dots {
            position: relative;
            background-image: linear-gradient(rgba(250, 250, 250, .55), rgba(250, 250, 250, .55)), url("/images/hero-madera.jpg");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .bg-dots::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(255, 255, 255, .08), rgba(245, 245, 245, .14));
            pointer-events: none;
        }

        .card-hover {
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 35px rgba(0, 0, 0, .10);
            border-color: var(--petrol);
        }

        #navbar {
            background: #ffffff !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            border-bottom: 1px solid var(--border) !important;
            transition: box-shadow .3s ease;
        }

        .navbar-scrolled {
            background: #ffffff !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
            border-bottom-color: var(--border) !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
        }

        .navbar-container {
            max-width: 1140px;
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

        .logo-header {
            width: 38px !important;
            height: 38px !important;
            max-width: 38px !important;
            max-height: 38px !important;
            object-fit: contain;
            display: block;
            flex: 0 0 38px;
            transition: none;
        }

        .navbar-container .brand-name {
            color: var(--dark) !important;
            letter-spacing: -.015em;
            transition: none !important;
        }

        .btn-black,
        .btn-outline-beige {
            background: #3BB5AC !important;
            border: 1px solid #3BB5AC !important;
            color: var(--white) !important;
            border-radius: 6px;
            font-weight: 600;
            transition: background-color 0.25s ease, border-color 0.25s ease, opacity 0.25s ease, box-shadow 0.25s ease;
            box-shadow: 0 2px 6px rgba(22, 143, 135, 0.12);
        }

        .btn-black:hover,
        .btn-outline-beige:hover {
            transform: none;
            opacity: 0.92;
            box-shadow: 0 4px 12px rgba(22, 143, 135, 0.18);
        }

        .beige-line {
            width: 55px;
            height: 3px;
            background: linear-gradient(90deg, var(--beige-dark), var(--petrol), var(--beige-dark));
            border-radius: 50px;
            margin: 0 auto;
        }

        .dropdown-menu {
            min-width: 220px;
            border: 1px solid var(--border);
            box-shadow: 0 15px 35px rgba(0, 0, 0, .10);
            border-radius: 14px;
            padding: 8px;
            background: #ffffff;
        }

        .dropdown-item {
            border-radius: 9px;
            color: #555555;
            padding: .65rem .8rem;
            transition: all .2s ease;
        }

        .dropdown-item:hover,
        .dropdown-item:focus {
            background: var(--petrol-soft);
            color: var(--petrol-dark);
            padding-left: 1rem;
        }

        footer {
            background: #242424 !important;
        }

        footer h3 {
            color: #eeeeee;
            transition: color .2s ease;
        }

        footer h3:hover {
            color: var(--petrol-light);
        }

        #mobileOverlay {
            background: rgba(0, 0, 0, .64);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
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
            max-height: 85vh;
            opacity: 1;
        }

        .mobile-menu-container {
            background: #ffffff;
            border-top: 1px solid var(--border-soft);
            box-shadow: 0 15px 35px rgba(0, 0, 0, .08);
            max-height: calc(100vh - 80px);
            overflow-y: auto;
            padding-bottom: 2.5rem !important;
        }

        .mobile-nav-link {
            color: #4a5568 !important;
            font-weight: 500;
            transition: all .2s ease;
        }

        .mobile-nav-link:hover,
        .mobile-nav-link:focus {
            background: rgba(22, 143, 135, 0.06);
            color: var(--petrol) !important;
        }

        .hamburger-line {
            transition: transform .3s ease, opacity .2s ease, background-color .2s ease;
        }

        .menu-open .line-1 { transform: translateY(7px) rotate(45deg); }
        .menu-open .line-2 { opacity: 0; }
        .menu-open .line-3 { transform: translateY(-7px) rotate(-45deg); }

        #menuButton {
            background: #ffffff !important;
            border: 1px solid var(--border) !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .04);
        }

        #menuButton:hover .hamburger-line {
            background: var(--petrol) !important;
        }

        .dropdown-toggle.hide-arrow::after {
            display: none !important;
        }

        .user-avatar-btn {
            transition: opacity 0.2s ease !important;
        }
        
        .user-avatar-btn:hover {
            transform: none !important;
            opacity: 0.85;
        }

        .text-teal { color: #168F87 !important; }
        .border-teal-light { border-color: rgba(22, 143, 135, 0.3) !important; }
        .transition-transform { transition: transform 0.25s ease; }
        
        [aria-expanded="true"] #serviciosChevron {
            transform: rotate(180deg);
        }

        .hover-dark:hover {
            color: #111111 !important;
            font-weight: 500;
            background-color: rgba(0, 0, 0, 0.02);
            border-radius: 6px;
        }

        .whatsapp-float {
            width: 56px;
            height: 56px;
            background-color: #25d366;
            transition: all 0.3s ease;
        }
        .whatsapp-float:hover {
            transform: scale(1.1);
            background-color: #20ba5a !important;
            box-shadow: 0 8px 25px rgba(37, 211, 102, 0.4) !important;
        }
        .social-btn {
            transition: all 0.2s ease;
        }
        .social-btn:hover {
            transform: translateY(-2px);
            background-color: rgba(255,255,255,0.1) !important;
            color: #168F87 !important;
            border-color: #168F87 !important;
        }
    </style>
</head>

<body class="text-dark">

    <!-- Overlay móvil[cite: 5] -->
    <div id="mobileOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-md-none" style="z-index: 1040;"></div>

    <!-- Header / Navbar[cite: 5] -->
    <header id="navbar" class="fixed-top" style="z-index: 1050;">
        <nav class="navbar-container mx-auto px-3 px-md-4 py-2 py-md-3">
            <div class="d-flex align-items-center justify-content-between position-relative" style="min-height: 52px;">

                <!-- 1. Izquierda: Brand / Logo[cite: 5] -->
                <a href="{{ url('/') }}" class="d-flex align-items-center gap-2 gap-md-3 text-decoration-none flex-shrink-0" style="z-index: 1060;">
                    <img src="{{ asset('logos/cortebarba2.png') }}" alt="Fede looK Barbería" class="logo-header">
                    <span class="fs-5 brand-name fw-bold text-truncate" style="line-height: 1; letter-spacing: -0.02em;">Fede looK</span>
                </a>

                <!-- 2. Centro Absoluto: Navegación Escritorio[cite: 5] -->
                <div class="d-none d-lg-flex align-items-center gap-4 position-absolute start-50 translate-middle-x">
                    <a href="{{ url('/') }}" class="header-nav-link fw-medium">Inicio</a>

                    <div class="dropdown d-flex align-items-center">
                        <a href="{{ route('usuario.catalogo') }}" class="header-nav-link dropdown-toggle fw-medium d-flex align-items-center gap-1" id="tiposDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Servicios
                        </a>
                        <ul class="dropdown-menu shadow-lg border-0 py-2 mt-2" aria-labelledby="tiposDropdown">
                            @forelse($tipos ?? [] as $tipo)
                                <li><a class="dropdown-item py-2" href="{{ route('usuario.catalogo', ['tipo' => $tipo->id]) }}">{{ $tipo->nombre }}</a></li>
                            @empty
                                <li><span class="dropdown-item text-muted py-2">No hay servicios disponibles</span></li>
                            @endforelse
                        </ul>
                    </div>

                    <a href="{{ url('/') }}#como-funciona" class="header-nav-link fw-medium">Cómo funciona</a>
                    <a href="{{ route('sucursales.public') }}" class="header-nav-link fw-medium">Sucursales</a>
                </div>

                <!-- 3. Derecha: Botones y Avatar Escritorio[cite: 5] -->
                <div class="d-none d-md-flex align-items-center gap-3 ms-auto" style="z-index: 1060;">
                    @auth
                        <div class="dropdown d-flex align-items-center">
                            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle hide-arrow user-avatar-btn" id="userMenuDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                @php
                                    $avatar = Auth::user()->avatar ?? null;
                                    $isGoogleAvatar = $avatar && (\Illuminate\Support\Str::startsWith($avatar, ['http://', 'https://']));
                                @endphp

                                @if($avatar)
                                    <img src="{{ $isGoogleAvatar ? $avatar : asset('storage/' . $avatar) }}" alt="Avatar" class="rounded-circle border border-2 border-white shadow-sm global-user-avatar" id="navbar-avatar-preview" style="width: 44px; height: 44px; object-fit: cover;">
                                @else
                                    <img src="#" alt="Avatar" class="rounded-circle border border-2 border-white shadow-sm global-user-avatar d-none" id="navbar-avatar-preview" style="width: 44px; height: 44px; object-fit: cover;">
                                    <div id="navbar-avatar-placeholder" class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center shadow-sm border border-2 border-white" style="width: 44px; height: 44px; font-weight: bold; font-size: 15px;">
                                        {{ strtoupper(substr(Auth::user()->nombre ?? (Auth::user()->name ?? 'U'), 0, 1)) }}
                                    </div>
                                @endif
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 py-2 mt-2" aria-labelledby="userMenuDropdown" style="border-radius: 12px; min-width: 210px;">
                                <li class="px-3 py-2 mb-1 text-muted small border-bottom text-truncate" style="max-width: 220px;">
                                    Hola, <strong class="text-dark">{{ Auth::user()->nombre ?? (Auth::user()->name ?? 'Usuario') }}</strong>
                                </li>

                                @if (auth()->user()->rol_id == 1)
                                    <li><a class="dropdown-item py-2" href="{{ route('admin.dashboard_admin') }}"><i class="bi bi-speedometer2 me-2 text-muted"></i> Panel</a></li>
                                @elseif(auth()->user()->rol_id == 2)
                                    <li><a class="dropdown-item py-2" href="{{ route('barbero.dashboard_barbero') }}"><i class="bi bi-calendar-check me-2 text-muted"></i> Agenda</a></li>
                                @else
                                    <li><a class="dropdown-item py-2" href="{{ route('usuario.turnos') }}"><i class="bi bi-clock-history me-2 text-muted"></i> Mis Turnos</a></li>
                                @endif

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
                    @else
                        <a href="{{ route('login') }}" class="btn btn-black px-4 py-2 fw-semibold shadow-sm d-flex align-items-center text-decoration-none">Iniciar sesión</a>
                    @endauth
                </div>

                <!-- Control Móvil (Hamburguesa)[cite: 5] -->
                <div class="d-flex align-items-center d-md-none ms-auto">
                    <button id="menuButton" type="button" aria-label="Abrir menú" aria-expanded="false" class="position-relative btn p-0 rounded-3 d-flex flex-column align-items-center justify-content-center border-0 bg-transparent" style="z-index: 1060; width: 42px; height: 42px;">
                        <span class="hamburger-line line-1 d-block rounded-pill" style="width: 22px; height: 2px; margin: 3px 0; background: #111111;"></span>
                        <span class="hamburger-line line-2 d-block rounded-pill" style="width: 22px; height: 2px; margin: 3px 0; background: #111111;"></span>
                        <span class="hamburger-line line-3 d-block rounded-pill" style="width: 22px; height: 2px; margin: 3px 0; background: #111111;"></span>
                    </button>
                </div>

            </div>

            <!-- Desplegable Menú Móvil[cite: 5] -->
            <div id="mobileMenu" class="d-md-none">
                <div class="mobile-menu-container rounded-bottom-4 pt-3 pb-4 mt-2 px-3 shadow-lg" style="max-height: calc(100vh - 85px); overflow-y: auto;">
                    
                    @auth
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
                    @endauth

                    <nav class="d-flex flex-column gap-2">
                        <a href="{{ url('/') }}" class="mobile-link mobile-nav-link d-flex align-items-center px-3 py-3 rounded-3 text-decoration-none fw-semibold text-dark">
                            <i class="bi bi-house me-3 text-secondary fs-5"></i> Inicio
                        </a>

                        <div class="mobile-dropdown-container">
                            <div class="d-flex align-items-center justify-content-between px-3 py-3 rounded-3 text-dark fw-semibold cursor-pointer" 
                                 data-bs-toggle="collapse" 
                                 data-bs-target="#mobileServiciosSubmenu" 
                                 aria-expanded="false" 
                                 aria-controls="mobileServiciosSubmenu"
                                 style="cursor: pointer;">
                                <span class="d-flex align-items-center">
                                    <i class="bi bi-scissors me-3 text-secondary fs-5"></i> Servicios
                                </span>
                                <i class="bi bi-chevron-down small transition-transform text-muted" id="serviciosChevron"></i>
                            </div>

                            <div class="collapse mt-1 ms-4 ps-3 border-start border-2 border-teal-light" id="mobileServiciosSubmenu">
                                <a href="{{ route('usuario.catalogo') }}" class="mobile-link d-block py-2.5 px-2 text-decoration-none small fw-bold text-teal">
                                    Ver catálogo completo <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                                @forelse($tipos ?? [] as $tipo)
                                    <a href="{{ route('usuario.catalogo', ['tipo' => $tipo->id]) }}" class="mobile-link d-block py-2 px-2 text-decoration-none small text-secondary hover-dark">
                                        {{ $tipo->nombre }}
                                    </a>
                                @empty
                                    <span class="d-block py-2 px-2 text-muted small">No hay servicios disponibles</span>
                                @endforelse
                            </div>
                        </div>

                        <a href="{{ url('/') }}#como-funciona" class="mobile-link mobile-nav-link d-flex align-items-center px-3 py-3 rounded-3 text-decoration-none fw-semibold text-dark">
                            <i class="bi bi-info-circle me-3 text-secondary fs-5"></i> Cómo funciona
                        </a>

                        <a href="{{ route('sucursales.public') }}" class="mobile-link mobile-nav-link d-flex align-items-center px-3 py-3 rounded-3 text-decoration-none fw-semibold text-dark">
                            <i class="bi bi-geo-alt me-3 text-secondary fs-5"></i> Sucursales
                        </a>
                    </nav>

                    <div class="border-top mt-3 pt-3 px-1 mb-2" style="border-color: var(--border-soft) !important;">
                        @auth
                            @if (auth()->user()->rol_id == 1)
                                <a href="{{ route('admin.dashboard_admin') }}" class="mobile-link btn btn-black w-100 py-3 fw-semibold mb-2 shadow-sm rounded-3">
                                    <i class="bi bi-speedometer2 me-2"></i> Panel de Control
                                </a>
                            @elseif(auth()->user()->rol_id == 2)
                                <a href="{{ route('barbero.dashboard_barbero') }}" class="mobile-link btn btn-black w-100 py-3 fw-semibold mb-2 shadow-sm rounded-3">
                                    <i class="bi bi-calendar-check me-2"></i> Mi Agenda
                                </a>
                            @else
                                <a href="{{ route('usuario.turnos') }}" class="mobile-link btn btn-black w-100 py-3 fw-semibold mb-2 shadow-sm rounded-3">
                                    <i class="bi bi-clock-history me-2"></i> Mis Turnos
                                </a>
                            @endif

                            <form action="{{ route('logout') }}" method="POST" class="mt-2">
                                @csrf
                                <button type="submit" class="btn btn-light w-100 py-3 fw-semibold text-danger border-0 rounded-3 text-start px-3 d-flex align-items-center">
                                    <i class="bi bi-box-arrow-right me-3 fs-5"></i> Cerrar sesión
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="mobile-link btn btn-black w-100 py-3 fw-semibold shadow-sm rounded-3">
                                <i class="bi bi-person me-2"></i> Iniciar sesión
                            </a>
                        @endauth
                    </div>

                </div>
            </div>

        </nav>
    </header>

    <main>
       @if (request()->routeIs('inicio') || request()->is('/'))
    <!-- Sección hero adaptada de forma inteligente a modo horizontal y vertical -->
    <section id="inicio" class="hero-section position-relative d-flex align-items-center justify-content-center overflow-hidden">
        
        <!-- Degradado cinematográfico profesional -->
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(17,17,17,0.3) 0%, rgba(17,17,17,0.75) 100%); z-index: 1;"></div>

        <!-- Contenido principal -->
        <div class="container position-relative text-center text-white py-3 py-md-5" style="z-index: 2;">
            
            <!-- Logo con contenedor circular limpio (se oculta automáticamente en landscape móvil) -->
            <div class="d-flex justify-content-center align-items-center mb-3 mb-md-4 hero-logo-wrapper">
                <div class="bg-white p-3 rounded-circle shadow-lg d-flex align-items-center justify-content-center" style="width: 110px; height: 110px; box-shadow: 0 15px 35px rgba(0,0,0,0.4)!important;">
                    <img src="{{ asset('logos/cortebarba.png') }}" alt="Logo Fede looK Barbería" style="width: 80px; height: 80px; object-fit: contain;">
                </div>
            </div>

            <div class="beige-line mb-3 mb-md-4"></div>

            <!-- Bloque de textos -->
            <div class="mx-auto mb-3 mb-md-4" style="max-width: 750px;">
                <span class="badge px-3 py-2 rounded-pill fw-bold mb-2 mb-md-3 hero-badge" style="background: #168F87; color: #ffffff; letter-spacing: 0.15em; font-size: 0.75rem; box-shadow: 0 5px 15px rgba(22,143,135,0.4);">CLUB DE CABALLEROS</span>
                <h1 class="display-4 fw-bold mb-2 text-white hero-title" style="letter-spacing: -0.02em; text-shadow: 0 4px 15px rgba(0,0,0,0.6);">Fede looK</h1>
                <p class="fs-5 text-light mb-2 hero-subtitle-text" style="font-weight: 300; text-shadow: 0 2px 10px rgba(0,0,0,0.6);">Experiencia de primera clase para el caballero moderno.</p>
                <p class="small text-uppercase text-white-50 d-none d-sm-block" style="letter-spacing: 0.2em; text-shadow: 0 2px 8px rgba(0,0,0,0.6);">BARBERÍA PROFESIONAL</p>
            </div>

            <!-- Botón de acción -->
            <div class="d-flex justify-content-center">
                <a href="{{ route('usuario.catalogo') }}" class="btn bg-white text-dark border-0 px-4 px-md-5 py-2 py-md-3 fw-bold shadow-lg text-uppercase text-decoration-none" style="letter-spacing: 0.05em; font-size: 0.95rem; border-radius: 6px; transition: all 0.25s ease;" onmouseover="this.style.backgroundColor='#f1f1f1'; this.style.transform='translateY(-2px)';" onmouseout="this.style.backgroundColor='#ffffff'; this.style.transform='translateY(0)';">Reservar turno</a>
            </div>

        </div>
    </section>
    
@endif

        @yield('content')
    </main>

    @php
        $whatsappNumero = \App\Models\Configuracion::get('whatsapp_numero', '5493881234567');
        $whatsappMensaje = urlencode(\App\Models\Configuracion::get('whatsapp_mensaje', 'Hola Fede looK, quiero consultar por un turno.'));
        $instagramUrl = \App\Models\Configuracion::get('instagram_url');
        $facebookUrl = \App\Models\Configuracion::get('facebook_url');
        $tiktokUrl = \App\Models\Configuracion::get('tiktok_url');
    @endphp

    <!-- BOTÓN FLOTANTE DE WHATSAPP DINÁMICO[cite: 4] -->
    @if($whatsappNumero)
    <div class="position-fixed bottom-0 end-0 p-4" style="z-index: 1080;">
        <a href="https://wa.me/{{ $whatsappNumero }}?text={{ $whatsappMensaje }}" 
           target="_blank" 
           rel="noopener noreferrer" 
           class="d-flex align-items-center justify-content-center text-white rounded-circle shadow-lg text-decoration-none whatsapp-float"
           title="¡Escribinos por WhatsApp!">
            <i class="bi bi-whatsapp fs-3"></i>
        </a>
    </div>
    @endif

    <footer class="text-white py-5">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-4">
            <div>
                <h3 class="fw-bold fs-5 mb-1">Fede looK</h3>
                <p class="text-secondary mt-1 mb-0">Barbería & Estilo</p>
            </div>

            <!-- REDES SOCIALES DINÁMICAS[cite: 4] -->
            <div class="d-flex gap-3">
                @if(!empty($instagramUrl))
                    <a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center shadow-sm social-btn text-white" style="width: 42px; height: 42px;">
                        <i class="bi bi-instagram fs-5"></i>
                    </a>
                @endif
                @if(!empty($facebookUrl))
                    <a href="{{ $facebookUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center shadow-sm social-btn text-white" style="width: 42px; height: 42px;">
                        <i class="bi bi-facebook fs-5"></i>
                    </a>
                @endif
                @if(!empty($tiktokUrl))
                    <a href="{{ $tiktokUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center shadow-sm social-btn text-white" style="width: 42px; height: 42px;">
                        <i class="bi bi-tiktok fs-5"></i>
                    </a>
                @endif
            </div>

            <div class="text-secondary small text-center text-md-end">
                © {{ date('Y') }} Fede looK. Todos los derechos reservados.
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle[cite: 4] -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script del Navbar y Menú Móvil[cite: 5] -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navbar = document.getElementById('navbar');

            function updateNavbar() {
                if (window.scrollY > 30) {
                    navbar.classList.add('navbar-scrolled');
                } else {
                    navbar.classList.remove('navbar-scrolled');
                }
            }

            window.addEventListener('scroll', updateNavbar);
            updateNavbar();

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
                menuButton.addEventListener('click', function() {
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

            document.querySelectorAll('.mobile-link').forEach(function(link) {
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
        });
    </script>

    @if (isset($turnoPendienteCalificar) && $turnoPendienteCalificar)
        <!-- MODAL FLOTANTE DE CALIFICACIÓN PENDIENTE[cite: 4] -->
        <div class="modal fade" id="modalCalificacionFlotante" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header bg-dark text-white border-0 py-3">
                        <h5 class="modal-title fs-6 fw-bold text-uppercase mb-0">
                            <i class="bi bi-star-fill text-warning me-2"></i> ¡Queremos conocer tu experiencia!
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('calificaciones.store') }}" method="POST" id="formCalificacionModal">
                        @csrf
                        <input type="hidden" name="turno_id" value="{{ $turnoPendienteCalificar->id }}">

                        <div class="modal-body text-center p-4">
                            <p class="text-secondary small mb-1">Completaste tu servicio de</p>
                            <h5 class="fw-bold text-dark mb-1">{{ $turnoPendienteCalificar->servicio->nombre ?? 'Barbería' }}</h5>
                            <p class="small text-muted mb-3">
                                Atendido por <strong>{{ $turnoPendienteCalificar->barbero->nombre ?? ($turnoPendienteCalificar->barbero->name ?? 'tu barbero') }}</strong> el {{ \Carbon\Carbon::parse($turnoPendienteCalificar->fecha)->format('d/m/Y') }}
                            </p>

                            <hr class="my-3 opacity-25">

                            <label class="form-label fw-semibold text-dark mb-2">¿Cómo evaluarías la atención recibida?</label>

                            <div class="star-rating d-flex justify-content-center gap-2 mb-3">
                                @for ($i = 1; $i <= 5; $i++)
                                    <input type="radio" id="star_flotante_{{ $i }}" name="puntuacion" value="{{ $i }}" class="d-none" required>
                                    <label for="star_flotante_{{ $i }}" class="star-label-flotante text-secondary fs-1" data-value="{{ $i }}" style="cursor: pointer; transition: color 0.2s, transform 0.2s;">
                                        <i class="bi bi-star-fill"></i>
                                    </label>
                                @endfor
                            </div>

                            <div class="mb-2">
                                <textarea name="comentario" id="comentario_flotante" class="form-control rounded-3" rows="3" placeholder="Escribí un breve comentario sobre tu experiencia..." maxlength="500" required></textarea>
                                <small id="error-comentario-flotante" class="text-danger d-none mt-1 small">El comentario debe tener al menos 3 caracteres y empezar con una letra (sin números ni símbolos al inicio).</small>
                            </div>
                        </div>

                        <div class="modal-footer bg-light border-0 justify-content-between">
                            <button type="button" class="btn btn-link text-muted text-decoration-none small" data-bs-dismiss="modal">Más tarde</button>
                            <button type="submit" id="btnEnviarCalificacion" class="btn btn-warning px-4 py-2 fw-bold text-dark shadow-sm" disabled>Enviar Opinión</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <style>
            .star-label-flotante {
                color: #cccccc !important;
                display: inline-block;
            }
            .star-label-flotante:hover,
            .star-label-flotante.activa {
                color: #ffc107 !important;
                transform: scale(1.15);
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var modalElement = document.getElementById('modalCalificacionFlotante');
                if (modalElement) {
                    var modalCalificacion = new bootstrap.Modal(modalElement);
                    modalCalificacion.show();
                }

                const starLabels = document.querySelectorAll('.star-label-flotante');
                const comentarioInput = document.getElementById('comentario_flotante');
                const btnEnviar = document.getElementById('btnEnviarCalificacion');
                const errorMsg = document.getElementById('error-comentario-flotante');

                function validarFormulario() {
                    const estrellaSeleccionada = document.querySelector('input[name="puntuacion"]:checked');
                    const texto = comentarioInput ? comentarioInput.value.trim() : '';
                    const empiezaConLetra = /^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ]/.test(texto);
                    const cumpleMinimo = texto.length >= 3;

                    if (estrellaSeleccionada && empiezaConLetra && cumpleMinimo) {
                        btnEnviar.removeAttribute('disabled');
                        if (errorMsg) errorMsg.classList.add('d-none');
                    } else {
                        btnEnviar.setAttribute('disabled', 'true');
                        if (texto.length > 0 && (!empiezaConLetra || !cumpleMinimo)) {
                            if (errorMsg) errorMsg.classList.remove('d-none');
                        } else {
                            if (errorMsg) errorMsg.classList.add('d-none');
                        }
                    }
                }

                starLabels.forEach((label) => {
                    label.addEventListener('click', function() {
                        const selectedValue = parseInt(this.getAttribute('data-value'));
                        const radioInput = document.getElementById('star_flotante_' + selectedValue);
                        if (radioInput) {
                            radioInput.checked = true;
                            validarFormulario();
                        }

                        starLabels.forEach((l) => {
                            const val = parseInt(l.getAttribute('data-value'));
                            if (val <= selectedValue) {
                                l.classList.add('activa');
                            } else {
                                l.classList.remove('activa');
                            }
                        });
                    });
                });

                if (comentarioInput) {
                    comentarioInput.addEventListener('input', validarFormulario);
                }
            });
        </script>
    @endif
</body>

</html>