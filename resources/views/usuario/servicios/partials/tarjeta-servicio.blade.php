<div class="flex-shrink-0" style="width: 270px;">
    @auth
        <a href="{{ route('usuario.turnos.create', ['servicio_id' => $servicio->id]) }}" class="text-decoration-none d-block h-100">
    @else
        <a href="{{ route('login') }}" class="text-decoration-none d-block h-100">
    @endauth
        <div class="card bg-card-dark border-0 rounded-4 overflow-hidden h-100 modern-card-hover">
            
            {{-- Imagen del servicio --}}
            <div class="position-relative overflow-hidden" style="height: 160px;">
                @if($servicio->imagen)
                    <img src="{{ Str::startsWith($servicio->imagen, ['http://', 'https://']) ? $servicio->imagen : asset('storage/' . $servicio->imagen) }}" class="w-100 h-100 object-fit-cover service-img" alt="{{ $servicio->nombre }}">
                @elseif($servicio->imagen_url)
                    <img src="{{ $servicio->imagen_url }}" class="w-100 h-100 object-fit-cover service-img" alt="{{ $servicio->nombre }}">
                @else
                    <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-secondary bg-opacity-10 text-white-50">
                        <i class="bi bi-scissors fs-1"></i>
                    </div>
                @endif
                <span class="badge position-absolute top-0 end-0 m-3 px-3 py-1 bg-black bg-opacity-75 backdrop-blur rounded-pill text-white small fw-normal border border-white border-opacity-10">
                    <i class="bi bi-clock me-1"></i>{{ $servicio->duracion }} min
                </span>
            </div>

            {{-- Detalle del servicio --}}
            <div class="card-body p-3 d-flex flex-column">
                <h6 class="fw-bold text-white mb-1 text-truncate">{{ $servicio->nombre }}</h6>
                <p class="text-white-50 small mb-3 line-clamp-2" style="font-size: 0.82rem;">
                    {{ $servicio->descripcion ?: 'Servicio profesional adaptado a tu estilo.' }}
                </p>
                
                <div class="d-flex align-items-center justify-content-between mt-auto pt-2 border-top border-white border-opacity-10">
                    @auth
                        <span class="text-teal fw-bold fs-5">${{ number_format($servicio->precio_actual ?? $servicio->precio, 0, ',', '.') }}</span>
                        <span class="btn-action-circle">
                            <i class="bi bi-arrow-right"></i>
                        </span>
                    @else
                        <span class="text-white-50 small fst-italic" style="font-size: 0.75rem;">Iniciá sesión para ver precio</span>
                        <span class="btn-action-circle">
                            <i class="bi bi-lock"></i>
                        </span>
                    @endauth
                </div>
            </div>

        </div>
    </a>
</div>