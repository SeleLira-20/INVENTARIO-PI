<div class="sidebar d-flex flex-column">
    <div class="logo-container d-flex align-items-center p-3 mb-3">
        <div class="bg-white rounded d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px; flex-shrink: 0; overflow: hidden;">
            <img src="{{ asset('logo.jpeg') }}" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        <div class="ms-3 hide-on-collapse">
            <strong class="d-block" style="line-height: 1;">Universal</strong>
            <small style="opacity: 0.7;">Inventory</small>
        </div>
    </div>

    <ul class="nav flex-column flex-grow-1">
        @php
            $menus = [
                ['route' => 'dashboard',   'icon' => 'fa-chart-line',      'label' => 'Dashboard'],
                ['route' => 'inventario',  'icon' => 'fa-box',             'label' => 'Inventario'],
                ['route' => 'picking',     'icon' => 'fa-dolly',           'label' => 'Picking'],
                ['route' => 'ubicaciones', 'icon' => 'fa-map-marker-alt',  'label' => 'Ubicaciones'],
                ['route' => 'reportes',    'icon' => 'fa-file-alt',        'label' => 'Reportes'],
                ['route' => 'usuarios',    'icon' => 'fa-users',           'label' => 'Usuarios'],
            ];
            $iniciales = strtoupper(
                substr(Auth::user()->name, 0, 1) .
                substr(strstr(Auth::user()->name, ' '), 1, 1)
            );
        @endphp
        @foreach($menus as $menu)
        <li class="nav-item">
            <a href="{{ route($menu['route']) }}" class="nav-link text-white py-3 px-4 d-flex align-items-center {{ request()->routeIs($menu['route']) ? 'bg-white bg-opacity-10 border-start border-warning' : 'opacity-75' }}">
                <i class="fas {{ $menu['icon'] }} fa-fw me-3"></i>
                <span class="hide-on-collapse">{{ $menu['label'] }}</span>
            </a>
        </li>
        @endforeach
    </ul>

    <div class="mt-auto p-3 border-top border-white border-opacity-10">
        <a href="{{ route('perfil') }}" class="d-flex align-items-center text-decoration-none text-white mb-3 p-2 rounded hover-effect" style="background: rgba(255,255,255,0.05);">
            <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 40px; height: 40px; flex-shrink: 0;">
                {{ $iniciales }}
            </div>
            <div class="ms-3 hide-on-collapse">
                <div class="small fw-bold">{{ Auth::user()->name }}</div>
                <div class="smaller opacity-50" style="font-size: 0.7rem;">Administrador</div>
            </div>
        </a>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-light btn-sm w-100 d-flex align-items-center justify-content-center">
                <i class="fas fa-sign-out-alt me-2"></i>
                <span class="hide-on-collapse">Cerrar Sesión</span>
            </button>
        </form>
    </div>
</div>

<style>
    .hover-effect:hover { background: rgba(255,255,255,0.1) !important; }
</style>