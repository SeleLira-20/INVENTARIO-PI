<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Universal Inventory')</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        .sidebar {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 260px;
            padding-top: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            overflow-y: auto;
        }

        .navbar-top {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: fixed;
            top: 0;
            left: 260px;
            right: 0;
            z-index: 999;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .main-content {
            margin-left: 260px;
            margin-top: 70px;
            padding: 30px;
            min-height: calc(100vh - 70px);
        }

        /* Logo */
        .logo-container {
            text-align: center;
            margin-bottom: 30px;
            padding: 0 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .logo-img {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 24px;
        }

        .logo-text {
            font-size: 14px;
            font-weight: 600;
            line-height: 1.2;
            text-align: left;
        }

        /* Sidebar Nav */
        .sidebar-nav {
            list-style: none;
            padding: 20px 0;
        }

        .sidebar-nav li {
            margin: 0;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 3px solid #ffc107;
            padding-left: 17px;
        }

        .sidebar-nav i {
            width: 20px;
            text-align: center;
        }

        /* User Section */
        .user-section {
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #ffc107;
            color: #1e3c72;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
        }

        .user-name {
            font-size: 13px;
            font-weight: 600;
            color: white;
        }

        .user-role {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 2px;
        }

        /* Top Navbar */
        .navbar-brand-text {
            font-size: 18px;
            font-weight: 700;
            color: #1e3c72;
            font-family: 'Poppins', sans-serif;
        }

        .navbar-subtitle {
            font-size: 12px;
            color: #6c757d;
            font-weight: 400;
        }

        .navbar-icons {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .navbar-icons a {
            color: #6c757d;
            font-size: 18px;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .navbar-icons a:hover {
            color: #1e3c72;
        }

        .badge-notification {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: bold;
        }

        /* Cards */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #1e3c72;
            margin: 10px 0;
        }

        .stat-label {
            font-size: 13px;
            color: #6c757d;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-trend {
            font-size: 12px;
            margin-top: 10px;
            font-weight: 600;
        }

        .stat-trend.positive {
            color: #28a745;
        }

        .stat-trend.negative {
            color: #dc3545;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .stat-icon.green {
            background-color: #d4edda;
            color: #28a745;
        }

        .stat-icon.blue {
            background-color: #cfe2ff;
            color: #0d6efd;
        }

        .stat-icon.orange {
            background-color: #fff3cd;
            color: #ff6b35;
        }

        .stat-icon.red {
            background-color: #f8d7da;
            color: #dc3545;
        }

        .stat-icon.teal {
            background-color: #d1ecf1;
            color: #17a2b8;
        }

        /* Alerts */
        .alert-box {
            background: white;
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 12px;
            border-left: 4px solid;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .alert-box.warning {
            border-left-color: #ffc107;
        }

        .alert-box.danger {
            border-left-color: #dc3545;
        }

        .alert-box.info {
            border-left-color: #0d6efd;
        }

        .alert-icon {
            font-size: 20px;
            width: 30px;
            text-align: center;
        }

        .alert-warning .alert-icon {
            color: #ffc107;
        }

        .alert-danger .alert-icon {
            color: #dc3545;
        }

        .alert-info .alert-icon {
            color: #0d6efd;
        }

        .alert-content {
            flex: 1;
        }

        .alert-title {
            font-weight: 600;
            font-size: 13px;
            color: #333;
            margin-bottom: 3px;
        }

        .alert-message {
            font-size: 12px;
            color: #666;
        }

        .alert-close {
            background: none;
            border: none;
            color: #999;
            cursor: pointer;
            font-size: 16px;
            padding: 0;
        }

        /* Buttons */
        .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 10px 20px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            border: none;
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #152d54 0%, #1d3f70 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(30, 60, 114, 0.3);
            color: white;
        }

        .btn-outline-primary {
            border: 1px solid #1e3c72;
            color: #1e3c72;
        }

        .btn-outline-primary:hover {
            background: #1e3c72;
            border-color: #1e3c72;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        /* Tables */
        .table-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background-color: #f8f9fa;
            border: none;
            color: #6c757d;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 15px;
        }

        .table td {
            padding: 15px;
            border-color: #e9ecef;
            vertical-align: middle;
        }

        .table tbody tr {
            border-bottom: 1px solid #e9ecef;
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Modals & Forms */
        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            border-bottom: 1px solid #e9ecef;
            padding: 25px;
        }

        .modal-title {
            font-weight: 700;
            color: #1e3c72;
            font-family: 'Poppins', sans-serif;
        }

        .modal-body {
            padding: 25px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 14px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #2a5298;
            box-shadow: 0 0 0 0.2rem rgba(42, 82, 152, 0.15);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.show {
                width: 260px;
                transform: translateX(0);
            }

            .navbar-top {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }
        }

        /* Scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }
    </style>

    @yield('extra-css')
</head>
<body>
    <!-- Sidebar -->
    @include('components.sidebar')

    <!-- Top Navbar -->
    @include('components.navbar')

    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Chart.js for graphics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

    <script>
        // Sidebar toggle para mobile
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }

            // Set active nav item
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.sidebar-nav a');
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });
        });
    </script>

    @yield('extra-js')
</body>
</html>