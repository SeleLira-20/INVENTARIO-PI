<div class="sidebar">
    <!-- Logo -->
    <div class="logo-container">
        <div class="logo-img">
    <img src="{{ asset('images/logo.jpeg') }}" alt="Universal Inventory" style="width: 100%; height: 100%; object-fit: contain;">
</div>
        <div class="logo-text">
            <strong>Universal</strong><br>
            <span style="font-size: 11px; opacity: 0.8;">Inventory</span>
        </div>
    </div>

    <!-- Navigation -->
    <ul class="sidebar-nav">
        <li>
            <a href="{{ route('dashboard') }}" class="@if(request()->routeIs('dashboard')) active @endif">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="{{ route('inventario') }}" class="@if(request()->routeIs('inventario')) active @endif">
                <i class="fas fa-box"></i>
                <span>Inventario</span>
            </a>
        </li>
        <li>
            <a href="{{ route('picking') }}" class="@if(request()->routeIs('picking')) active @endif">
                <i class="fas fa-dolly"></i>
                <span>Picking</span>
            </a>
        </li>
        <li>
            <a href="{{ route('reportes') }}" class="@if(request()->routeIs('reportes')) active @endif">
                <i class="fas fa-file-chart-line"></i>
                <span>Reportes</span>
            </a>
        </li>
        <li>
            <a href="{{ route('ubicaciones') }}" class="@if(request()->routeIs('ubicaciones')) active @endif">
                <i class="fas fa-map-marker-alt"></i>
                <span>Ubicaciones</span>
            </a>
        </li>
        <li>
            <a href="{{ route('usuarios') }}" class="@if(request()->routeIs('usuarios')) active @endif">
                <i class="fas fa-users"></i>
                <span>Usuarios</span>
            </a>
        </li>
        <li>
            <a href="{{ route('configuracion') }}" class="@if(request()->routeIs('configuracion')) active @endif">
                <i class="fas fa-cog"></i>
                <span>Configuración</span>
            </a>
        </li>
    </ul>

    <!-- User Section -->
    <div class="user-section">
        <div class="user-info">
            <div class="user-avatar">JD</div>
            <div>
                <div class="user-name">John Doe</div>
                <div class="user-role">Administrador</div>
            </div>
        </div>
        <a href="{{ route('perfil') }}" class="btn btn-outline-light btn-sm w-100 mb-2" style="border-color: rgba(255,255,255,0.3); color: rgba(255,255,255,0.9);">
            <i class="fas fa-user-circle"></i> Mi Perfil
        </a>
        <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm w-100" style="border-color: rgba(255,255,255,0.3); color: rgba(255,255,255,0.9);" onclick="return confirm('¿Deseas cerrar sesión?')">
            <i class="fas fa-sign-out-alt"></i> Salir
        </a>
    </div>
</div>