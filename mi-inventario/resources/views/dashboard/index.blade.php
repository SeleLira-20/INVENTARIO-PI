@extends('layouts.app')

@section('content')
<div class="container-fluid p-4" style="background-color: #f8fafc;">
    <div class="mb-4">
        <h2 class="fw-bold mb-0">Dashboard</h2>
        <p class="text-muted">Resumen general del sistema de inventario</p>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 12px;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="rounded-3 p-2" style="background: #ecfdf5; color: #10b981;">
                        <i class="fas fa-check-circle fa-lg"></i>
                    </div>
                    <span class="text-success small fw-bold"><i class="fas fa-arrow-up"></i> 2.3%</span>
                </div>
                <div class="mt-3">
                    <div class="text-muted small">Precisión de Inventario</div>
                    <div class="h3 fw-bold mb-0">98.5%</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 12px;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="rounded-3 p-2" style="background: #eff6ff; color: #3b82f6;">
                        <i class="fas fa-exchange-alt fa-lg"></i>
                    </div>
                    <span class="text-success small fw-bold"><i class="fas fa-arrow-up"></i> 12%</span>
                </div>
                <div class="mt-3">
                    <div class="text-muted small">Movimientos Hoy</div>
                    <div class="h3 fw-bold mb-0">1,247</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 12px;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="rounded-3 p-2" style="background: #fffbeb; color: #f59e0b;">
                        <i class="fas fa-box fa-lg"></i>
                    </div>
                    <span class="text-danger small fw-bold"><i class="fas fa-arrow-down"></i> 3.2%</span>
                </div>
                <div class="mt-3">
                    <div class="text-muted small">Items en Stock</div>
                    <div class="h3 fw-bold mb-0">8,432</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 12px;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="rounded-3 p-2" style="background: #f0fdf4; color: #16a34a;">
                        <i class="fas fa-dollar-sign fa-lg"></i>
                    </div>
                    <span class="text-success small fw-bold"><i class="fas fa-arrow-up"></i> 5.1%</span>
                </div>
                <div class="mt-3">
                    <div class="text-muted small">Valor Total</div>
                    <div class="h3 fw-bold mb-0">$2.4M</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body">
            <h6 class="fw-bold mb-3"><i class="fas fa-exclamation-triangle text-warning me-2"></i> Alertas y Notificaciones</h6>
            <div class="alert border-0 mb-2 d-flex justify-content-between align-items-center" style="background-color: #fffbeb;">
                <span><i class="fas fa-exclamation-circle text-warning me-2"></i> Stock bajo: <strong>Papel A4</strong> (Quedan 45 unidades)</span>
                <span class="badge bg-warning text-dark">Alto</span>
            </div>
            <div class="alert border-0 mb-2 d-flex justify-content-between align-items-center" style="background-color: #fef2f2;">
                <span><i class="fas fa-times-circle text-danger me-2"></i> Discrepancia detectada en <strong>Zona A-12</strong></span>
                <span class="badge bg-danger">Crítico</span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 15px;">
                <h6 class="fw-bold mb-4">Movimientos Mensuales</h6>
                <div style="height: 300px;">
                    <canvas id="movementsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 15px;">
                <h6 class="fw-bold mb-4">Stock por Categoría</h6>
                <div style="height: 300px;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra-js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gráfico de Barras (Entradas y Salidas)
        new Chart(document.getElementById('movementsChart'), {
            type: 'bar',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
                datasets: [
                    {
                        label: 'entradas',
                        data: [400, 200, 480, 278, 420, 380],
                        backgroundColor: '#10b981', // Verde
                        borderRadius: 4,
                        barThickness: 20,
                    },
                    {
                        label: 'salidas',
                        data: [250, 450, 300, 390, 450, 480],
                        backgroundColor: '#3b82f6', // Azul
                        borderRadius: 4,
                        barThickness: 20,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true } }
                },
                scales: {
                    y: { beginAtZero: true, grid: { drawBorder: false, color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Gráfico de Dona
        new Chart(document.getElementById('categoryChart'), {
            type: 'doughnut',
            data: {
                labels: ['Electrónicos', 'Mobiliario', 'Papelería', 'Equipamiento'],
                datasets: [{
                    data: [38, 25, 21, 16],
                    backgroundColor: ['#2563eb', '#10b981', '#f59e0b', '#8b5cf6'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true } }
                }
            }
        });
    });
</script>
@endsection