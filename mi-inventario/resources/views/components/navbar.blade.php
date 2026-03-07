<nav class="navbar-top justify-content-between">
    <div class="d-flex align-items-center">
        <button class="btn btn-link p-0 me-3" id="sidebarToggle" style="color: #1e3c72;">
            <i class="fas fa-bars fs-4"></i>
        </button>
        <div class="hide-on-collapse">
            <h5 class="mb-0 fw-bold" style="color: #1e3c72;">Universal Inventory</h5>
            <small class="text-muted">Academic Assets & Resources</small>
        </div>
    </div>

    <div class="d-flex align-items-center">
        <div class="me-3 position-relative" style="cursor: pointer;">
            <i class="fas fa-bell text-secondary fs-5"></i>
            <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle" style="font-size: 0.6rem;">3</span>
        </div>
        
        <a href="{{ route('configuracion') }}" class="ms-2 text-secondary hover-config" title="Configuración">
            <i class="fas fa-cog fs-5"></i>
        </a>
    </div>
</nav>

<style>
    .navbar-top {
        display: flex;
        background: white;
        padding: 0.75rem 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .hover-config {
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-block;
    }

    .hover-config:hover {
        color: #1e3c72 !important;
        transform: rotate(30deg);
    }
</style>