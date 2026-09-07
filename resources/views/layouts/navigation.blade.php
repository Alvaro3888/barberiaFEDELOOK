<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <!-- Logo circular + Fedelook -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('index') }}">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Fedelook" style="width:40px;height:40px;object-fit:cover;border-radius:50%;margin-right:10px;">
            <span class="fw-bold">FEDELOOK</span>
        </a>

        <!-- Botón responsive -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menú centrado -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('index') }}">Inicio</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="serviciosDropdown" role="button" data-bs-toggle="dropdown">
                        Servicios
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('servicios.cortes') }}">Cortes</a></li>
                        <li><a class="dropdown-item" href="{{ route('servicios.barba') }}">Barba</a></li>
                        <li><a class="dropdown-item" href="{{ route('servicios.coloracion') }}">Coloración</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="#">Ubicación</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Sobre nosotros</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Contáctanos</a></li>
            </ul>

            <!-- Botones dinámicos según rol -->
            <ul class="navbar-nav ms-auto">
                @guest
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="btn btn-outline-light">Iniciar Sesión</a>
                    </li>
                @else
                    @if(Auth::user()->rol === 'admin')
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard_admin') }}" class="btn btn-outline-light me-2">Panel</a>
                        </li>
                    @elseif(Auth::user()->rol === 'barbero')
                        <li class="nav-item">
                            <a href="{{ route('barbero.dashboard_barbero') }}" class="btn btn-outline-light me-2">Agenda</a>
                        </li>
                    @elseif(Auth::user()->rol === 'cliente')
                        <li class="nav-item">
                            <a href="{{ route('usuario.turnos') }}" class="btn btn-outline-light me-2">Mis turnos</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger">Cerrar Sesión</button>
                        </form>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
