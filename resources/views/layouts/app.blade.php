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

               <div class="d-flex align-items-center">
    @auth
        @if(Auth::user()->rol && Auth::user()->rol->nombre === 'admin')
            <a href="{{ route('admin.dashboard_admin') }}" class="btn-role me-2">Panel</a>
        @elseif(Auth::user()->rol && Auth::user()->rol->nombre === 'barbero')
            <a href="{{ route('barbero.dashboard_barbero') }}" class="btn-role me-2">Agenda</a>
        @endif

        <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn-oxblood">Cerrar sesión</button>
        </form>
    @else
        <a href="{{ route('login') }}" class="btn-oxblood">Iniciar sesión</a>
    @endauth
</div>




            </div>
        </div>
    </nav>

    <!-- Panel Admin debajo -->
    <div class="d-flex" style="margin-top: 80px;">
        <nav class="bg-ink text-white p-3" style="width: 220px; min-height: 100vh;">
            <h4 class="mb-4">Panel Admin</h4>
            <ul class="nav flex-column">
                <li class="nav-item"><a href="{{ route('admin.usuarios.index') }}" class="nav-link text-white"><i class="bi bi-people"></i> Usuarios</a></li>
                <li class="nav-item"><a href="{{ route('admin.sucursales.index') }}" class="nav-link text-white"><i class="bi bi-shop"></i> Sucursales</a></li>
                <li class="nav-item"><a href="{{ route('admin.servicios.index') }}" class="nav-link text-white"><i class="bi bi-scissors"></i> Servicios</a></li>
                <li class="nav-item"><a href="{{ route('admin.turnos.index') }}" class="nav-link text-white"><i class="bi bi-calendar-check"></i> Turnos</a></li>
                <li class="nav-item"><a href="{{ route('admin.estadisticas.dashboard') }}" class="nav-link text-white"><i class="bi bi-bar-chart"></i> Estadísticas</a></li>
            </ul>
        </nav>

        <div class="flex-grow-1">
            <main class="p-4">
                @yield('content')
            </main>
        </div>
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
