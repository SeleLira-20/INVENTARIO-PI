<div class="navbar-top">
    <div>
        <button class="btn btn-link" id="sidebarToggle" style="display:none; color: #1e3c72;">
            <i class="fas fa-bars"></i>
        </button>
        <h5 class="navbar-brand-text mb-0" style="display:inline; margin-left: 10px;">
            Universal Inventory
            <br>
            <span class="navbar-subtitle">Academic Assets & Resources</span>
        </h5>
    </div>

    <div class="navbar-icons">
        <div style="position: relative; cursor: pointer;" title="Notificaciones">
            <i class="fas fa-bell"></i>
            <span class="badge-notification">3</span>
        </div>

        <div style="position: relative; cursor: pointer;" title="Configuración rápida">
            <i class="fas fa-sliders-h"></i>
        </div>

        <div style="border-left: 1px solid #e9ecef; padding-left: 20px; margin-left: 10px;">
            <a href="{{ route('perfil') }}" style="text-decoration: none; color: #1e3c72; font-weight: 600; font-size: 12px;">
                JD <i class="fas fa-chevron-down"></i>
            </a>
        </div>
    </div>
</div>

<style>
    @media (max-width: 768px) {
        #sidebarToggle {
            display: block !important;
        }
    }
</style>