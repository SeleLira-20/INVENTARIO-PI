<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Universal Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root { --sidebar-w: 260px; --sidebar-c: 80px; }
        body { background-color: #f7f9fc; margin: 0; display: flex; font-family: 'Plus Jakarta Sans', sans-serif; }
        .sidebar { width: var(--sidebar-w); background: #1e3c72; position: fixed; height: 100vh; transition: 0.3s; z-index: 1100; color: white; }
        .sidebar.collapsed { width: var(--sidebar-c); }
        .main-wrapper { flex-grow: 1; margin-left: var(--sidebar-w); transition: 0.3s; min-width: 0; width: 100%; }
        .sidebar.collapsed ~ .main-wrapper { margin-left: var(--sidebar-c); }
        .navbar-top { height: 70px; background: white; display: flex; align-items: center; padding: 0 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .sidebar.collapsed .hide-on-collapse { display: none; }
    </style>

    @yield('extra-css') 
</head>
<body>
    @include('components.sidebar')
    <div class="main-wrapper">
        @include('components.navbar')
        <div class="p-4">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', () => {
            document.querySelector('.sidebar').classList.toggle('collapsed');
            setTimeout(() => { window.dispatchEvent(new Event('resize')); }, 300);
        });
    </script>
    @yield('extra-js')
</body>
</html>