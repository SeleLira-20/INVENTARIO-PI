@extends('layouts.app')

@section('title', 'Dashboard - Universal Inventory')

@section('extra-css')
<style>
    .metric-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
        text-align: center;
    }

    .metric-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 28px;
    }

    .metric-value {
        font-size: 32px;
        font-weight: 700;
        color: #1e3c72;
        margin: 10px 0;
    }

    .metric-label {
        font-size: 12px;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .metric-trend {
        font-size: 12px;
        margin-top: 10px;
        font-weight: 600;
    }

    .metric-trend.up {
        color: #28a745;
    }

    .metric-trend.down {
        color: #dc3545;
    }

    .chart-container {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }

    .chart-title {
        font-size: 16px;
        font-weight: 700;
        color: #1e3c72;
        margin-bottom: 20px;
    }

    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #1e3c72;
        margin-bottom: 20px;
        margin-top: 30px;
    }
</style>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-md-3">
        <div class="metric-card">
            <div class="metric-icon" style="background-color: #d4edda; color: #28a745;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="metric-label">Precisión de Inventario</div>
            <div class="metric-value">98.5%</div>
            <div class="metric-trend up">
                <i class="fas fa-arrow-up"></i> +2.3%
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="metric-card">
            <div class="metric-icon" style="background-color: #cfe2ff; color: #0d6efd;">
                <i class="fas fa-cube"></i>
            </div>
            <div class="metric-label">Movimientos Hoy</div>
            <div class="metric-value">1,247</div>
            <div class="metric-trend up">
                <i class="fas fa-arrow-up"></i> +12%
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="metric-card">
            <div class="metric-icon" style="background-color: #fff3cd; color: #ff6b35;">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="metric-label">Items en Stock</div>
            <div class="metric-value">8,432</div>
            <div class="metric-trend down">
                <i class="fas fa-arrow-down"></i> -3.2%
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="metric-card">
            <div class="metric-icon" style="background-color: #d1ecf1; color: #17a2b8;">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="metric-label">Valor Total</div>
            <div class="metric-value">$2.4M</div>
            <div class="metric-trend up">
                <i class="fas fa-arrow-up"></i> +5.1%
            </div>
        </div>
    </div>
</div>

<!-- Alertas y Notificaciones -->
<h3 class="section-title">
    <i class="fas fa-bell"></i> Alertas y Notificaciones
</h3>

<div class="alert-box warning">
    <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
    <div class="alert-content">
        <div class="alert-title">Stock bajo: Papel A4 (Quedan 45 unidades)</div>
        <div class="alert-message">Considere realizar un pedido pronto para evitar desabastecimiento</div>
    </div>
    <button class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
</div>

<div class="alert-box danger">
    <div class="alert-icon"><i class="fas fa-times-circle"></i></div>
    <div class="alert-content">
        <div class="alert-title">Discrepancia detectada en Zona A-12</div>
        <div class="alert-message">Diferencia en inventario físico vs sistema</div>
    </div>
    <button class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
</div>

<div class="alert-box info">
    <div class="alert-icon"><i class="fas fa-info-circle"></i></div>
    <div class="alert-content">
        <div class="alert-title">3 órdenes de picking pendientes de asignación</div>
        <div class="alert-message">Revisar y asignar órdenes para optimizar el proceso</div>
    </div>
    <button class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
</div>

<!-- Gráficos -->
<div class="row">
    <div class="col-lg-6">
        <div class="chart-container">
            <h5 class="chart-title">Movimientos Mensuales</h5>
            <canvas id="movementsChart" height="300"></canvas>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="chart-container">
            <h5 class="chart-title">Stock por Categoría</h5>
            <canvas id="categoryChart" height="300"></canvas>
        </div>
    </div>
</div>

<!-- Actividad Reciente -->
<h3 class="section-title">
    <i class="fas fa-history"></i> Actividad Reciente
</h3>

<div class="chart-container">
    <div style="padding: 20px;">
        <div style="margin-bottom: 20px; display: flex; align-items: center; gap: 15px;">
            <div style="width: 40px; height: 40px; background: #d4edda; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #28a745;">
                <i class="fas fa-inbox"></i>
            </div>
            <div>
                <div style="font-weight: 600; color: #333; font-size: 14px;">Entrada de inventario</div>
                <div style="font-size: 12px; color: #6c757d;">Laptop Dell XPS 15 - Maria García • Hace 3 min</div>
            </div>
        </div>

        <div style="margin-bottom: 20px; display: flex; align-items: center; gap: 15px;">
            <div style="width: 40px; height: 40px; background: #cfe2ff; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #0d6efd;">
                <i class="fas fa-dolly"></i>
            </div>
            <div>
                <div style="font-weight: 600; color: #333; font-size: 14px;">Picking completado</div>
                <div style="font-size: 12px; color: #6c757d;">Sillas de oficina (x10) - Carlos López • Hace 12 min</div>
            </div>
        </div>

        <div style="margin-bottom: 20px; display: flex; align-items: center; gap: 15px;">
            <div style="width: 40px; height: 40px; background: #fff3cd; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #ff6b35;">
                <i class="fas fa-wrench"></i>
            </div>
            <div>
                <div style="font-weight: 600; color: #333; font-size: 14px;">Ajuste de inventario</div>
                <div style="font-size: 12px; color: #6c757d;">Ajuste en Pasillo 2 - Admin • Hace 45 min</div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('extra-js')
<script>
    // Gráfico de Movimientos Mensuales
    const ctx1 = document.getElementById('movementsChart').getContext('2d');
    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
            datasets: [
                {
                    label: 'Entradas',
                    data: [400, 120, 500, 280, 350, 200],
                    backgroundColor: '#28a745',
                    borderRadius: 8,
                    borderSkipped: false
                },
                {
                    label: 'Salidas',
                    data: [200, 150, 350, 180, 250, 150],
                    backgroundColor: '#0d6efd',
                    borderRadius: 8,
                    borderSkipped: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: { size: 12 }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { font: { size: 11 } }
                },
                x: {
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });

    // Gráfico de Stock por Categoría
    const ctx2 = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Electrónicos: 38%', 'Mobiliario: 25%', 'Papelería: 21%', 'Equipamiento: 16%'],
            datasets: [{
                data: [38, 25, 21, 16],
                backgroundColor: [
                    '#0d6efd',
                    '#28a745',
                    '#ff9800',
                    '#dc3545'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: { size: 11 }
                    }
                }
            }
        }
    });
</script>
@endsection