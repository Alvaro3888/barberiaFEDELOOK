<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Fedelook Barbería</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS único con paleta Figma -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="bg-paper">

    <!-- Navbar fijo -->
    <nav class="navbar navbar-expand-lg border-ink fixed-top bg-paper">
        <div class="container-fluid">
            <a class="navbar-brand text-ink fw-bold" href="{{ url('/') }}">
                FEDELOOK
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link text-ink" href="{{ url('/') }}">Inicio</a></li>

                    <!-- Dropdown Servicios -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-ink" href="#" id="serviciosDropdown" role="button" data-bs-toggle="dropdown">
                            Servicios
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('servicios.cortes') }}">Cortes</a></li>
                            <li><a class="dropdown-item" href="{{ route('servicios.barba') }}">Barba</a></li>
                            <li><a class="dropdown-item" href="{{ route('servicios.coloracion') }}">Coloración</a></li>
                        </ul>
                    </li>

                    <li class="nav-item"><a class="nav-link text-ink" href="#">Cómo funciona</a></li>
                    <li class="nav-item"><a class="nav-link text-ink" href="#">Sucursales</a></li>
                </ul>

                <!-- Botón dinámico según estado de sesión -->
@auth
    {{-- Botón según el rol con color distinto --}}
    @if(auth()->user()->rol->nombre === 'admin')
        <a href="{{ route('admin.dashboard_admin') }}" class="btn btn-role me-2">Panel</a>
    @elseif(auth()->user()->rol->nombre === 'barbero')
        <a href="{{ route('barbero.dashboard_barbero') }}" class="btn btn-role me-2">Agenda</a>
    @elseif(auth()->user()->rol->nombre === 'cliente')
        <a href="{{ route('usuario.turnos') }}" class="btn btn-role me-2">Mis Turnos</a>
    @endif

    {{-- Botón de cerrar sesión --}}
    <form action="{{ route('logout') }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-oxblood">Cerrar sesión</button>
    </form>
@else
    {{-- Botón de iniciar sesión --}}
    <a href="{{ route('login') }}" class="btn btn-oxblood">Iniciar sesión</a>
@endauth


                    
            </div>
        </div>
    </nav>

    <!-- Contenido principal público -->
    <div style="margin-top: 80px;">
        <main class="p-4">
            @yield('content')
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    window.addEventListener("scroll", function() {
        const navbar = document.querySelector(".navbar");
        if (window.scrollY > 50) {
            navbar.classList.add("scrolled");
        } else {
            navbar.classList.remove("scrolled");
        }
    });
    </script>

</body>
</html>
