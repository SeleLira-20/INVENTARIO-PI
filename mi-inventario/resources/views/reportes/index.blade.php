@extends('layouts.app')

@section('title', 'Reportes y Analítica - Universal Inventory')

@section('extra-css')
<style>
    :root {
        --primary-blue: #0061f2;
        --dark-blue: #1e3c72;
        --light-gray: #f8f9fa;
        --border-color: #e3e6f0;
    }

    body { background-color: #f4f6f9; }

    /* Filtros */
    .filter-section {
        background: white;
        border-radius: 15px;
        padding: 15px 25px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.05);
        margin-bottom: 25px;
        display: flex;
        gap: 15px;
        align-items: center;
    }

    .filter-section input {
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 8px 15px;
        color: #6e707e;
    }

    .btn-aplicar {
        background-color: var(--primary-blue);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 8px 25px;
        font-weight: 600;
        cursor: pointer;
    }

    /* Cards */
    .report-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.05);
        margin-bottom: 25px;
        border: 1px solid transparent;
    }

    .report-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--dark-blue);
        margin-bottom: 0;
    }

    .btn-export {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 5px 15px;
        font-size: 13px;
        color: #5a5c69;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* Plantillas de Reportes (Estilo exacto a la imagen) */
    .template-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .template-item {
        border: 1px solid #edf0f5;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        position: relative;
        transition: all 0.2s;
    }

    .template-item:hover { border-color: var(--primary-blue); background: #fafdff; }

    .template-icon-wrapper {
        width: 45px;
        height: 45px;
        background: #eef2f8;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        color: var(--primary-blue);
        font-size: 1.2rem;
    }

    .template-info h6 { margin: 0; font-weight: 700; color: #3a3b45; font-size: 14px; }
    .template-info p { margin: 3px 0 10px 0; color: #858796; font-size: 12px; }
    
    .format-labels { font-size: 11px; color: #b7b9cc; }
    .format-badge {
        background: #f1f3f7;
        color: #6e707e;
        padding: 2px 8px;
        border-radius: 4px;
        margin-left: 5px;
        font-weight: 600;
    }

    .download-circle {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        width: 32px;
        height: 32px;
        background: var(--primary-blue);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
    }

    /* Tabla */
    .table thead th {
        background-color: #f8f9fc;
        border-bottom: 1px solid var(--border-color);
        color: #858796;
        text-transform: capitalize;
        font-size: 13px;
        font-weight: 600;
    }

    .badge-rotation {
        padding: 5px 10px;
        border-radius: 5px;
        font-weight: 700;
        font-size: 11px;
    }

    @media (max-width: 992px) { .template-grid { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <h2 class="fw-bold" style="color: #1e3c72; margin-bottom: 5px;">Reportes y Analítica</h2>
    <p class="text-muted mb-4">Análisis de datos y generación de reportes</p>

    <div class="filter-section">
        <i class="far fa-calendar-alt text-muted"></i>
        <span class="fw-bold" style="font-size: 14px;">Periodo:</span>
        <input type="text" value="02/01/2026">
        <span class="text-muted">hasta</span>
        <input type="text" value="02/17/2026">
        <button class="btn-aplicar">Aplicar</button>
    </div>

    <div class="report-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="report-title">Tendencia de Stock</h5>
            <button class="btn-export"><i class="fas fa-download"></i> Descargar</button>
        </div>
        <canvas id="trendChart" height="100"></canvas>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="report-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="report-title">Performance por Categoría</h5>
                    <button class="btn-export"><i class="fas fa-download"></i> Exportar</button>
                </div>
                <canvas id="performanceChart" height="200"></canvas>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="report-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="report-title">Eficiencia de Picking</h5>
                    <button class="btn-export"><i class="fas fa-download"></i> Exportar</button>
                </div>
                <canvas id="pickingChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="report-card">
        <h5 class="report-title mb-4">Plantillas de Reportes</h5>
        <div class="template-grid">
            <div class="template-item">
                <div class="template-icon-wrapper"><i class="far fa-file-alt"></i></div>
                <div class="template-info">
                    <h6>Reporte de Inventario Completo</h6>
                    <p>Estado actual de todo el inventario con ubicaciones y valores</p>
                    <span class="format-labels">Formatos: <span class="format-badge">PDF</span><span class="format-badge">Excel</span><span class="format-badge">CSV</span></span>
                </div>
                <div class="download-circle"><i class="fas fa-download"></i></div>
            </div>
            <div class="template-item">
                <div class="template-icon-wrapper"><i class="fas fa-chart-line"></i></div>
                <div class="template-info">
                    <h6>Análisis de Movimientos</h6>
                    <p>Entradas, salidas y transferencias del periodo seleccionado</p>
                    <span class="format-labels">Formatos: <span class="format-badge">PDF</span><span class="format-badge">Excel</span></span>
                </div>
                <div class="download-circle"><i class="fas fa-download"></i></div>
            </div>
            <div class="template-item">
                <div class="template-icon-wrapper"><i class="fas fa-chart-bar"></i></div>
                <div class="template-info">
                    <h6>Performance de Picking</h6>
                    <p>Métricas de eficiencia y precisión en operaciones de picking</p>
                    <span class="format-labels">Formatos: <span class="format-badge">PDF</span><span class="format-badge">Excel</span></span>
                </div>
                <div class="download-circle"><i class="fas fa-download"></i></div>
            </div>
            <div class="template-item">
                <div class="template-icon-wrapper"><i class="fas fa-sync"></i></div>
                <div class="template-info">
                    <h6>Rotación de Inventario</h6>
                    <p>Análisis de rotación por categoría y producto</p>
                    <span class="format-labels">Formatos: <span class="format-badge">PDF</span><span class="format-badge">Excel</span><span class="format-badge">CSV</span></span>
                </div>
                <div class="download-circle"><i class="fas fa-download"></i></div>
            </div>
        </div>
    </div>

    <div class="report-card">
        <h5 class="report-title mb-4">Análisis Detallado por Categoría</h5>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Categoría</th>
                        <th class="text-center">Movimientos</th>
                        <th class="text-center">Valor Total</th>
                        <th class="text-center">Rotación</th>
                        <th class="text-end">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold">Electrónicos</td>
                        <td class="text-center">245</td>
                        <td class="text-center text-muted">$98,500</td>
                        <td class="text-center"><span class="badge-rotation" style="background: #fff8e1; color: #ffb300;">4.2x</span></td>
                        <td class="text-end"><a href="#" class="text-primary text-decoration-none fw-bold small">Ver detalles</a></td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Mobiliario</td>
                        <td class="text-center">123</td>
                        <td class="text-center text-muted">$67,800</td>
                        <td class="text-center"><span class="badge-rotation" style="background: #fbe9e7; color: #f44336;">2.8x</span></td>
                        <td class="text-end"><a href="#" class="text-primary text-decoration-none fw-bold small">Ver detalles</a></td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Papelería</td>
                        <td class="text-center">456</td>
                        <td class="text-center text-muted">$12,400</td>
                        <td class="text-center"><span class="badge-rotation" style="background: #e8f5e9; color: #4caf50;">8.5x</span></td>
                        <td class="text-end"><a href="#" class="text-primary text-decoration-none fw-bold small">Ver detalles</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('extra-js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Configuración compartida
    Chart.defaults.color = '#858796';
    Chart.defaults.font.family = 'Nunito, sans-serif';

    // Gráfico de Tendencia
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: ['Feb 1', 'Feb 5', 'Feb 10', 'Feb 15'],
            datasets: [{
                data: [8200, 8400, 8100, 8300],
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { min: 5000, max: 10000, ticks: { stepSize: 2500 } } }
        }
    });

    // Performance Categoría
    new Chart(document.getElementById('performanceChart'), {
        type: 'bar',
        data: {
            labels: ['Electrónicos', 'Mobiliario', 'Papelería', 'Equipamiento'],
            datasets: [{
                data: [245, 123, 456, 189],
                backgroundColor: '#1cc88a',
                borderRadius: 5
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { max: 600, ticks: { stepSize: 150 } } }
        }
    });

    // Picking Chart (Dual Axis)
    new Chart(document.getElementById('pickingChart'), {
        type: 'line',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May'],
            datasets: [{
                label: 'Precisión %',
                data: [95, 98, 97, 99, 98],
                borderColor: '#1cc88a',
                yAxisID: 'y',
                tension: 0.3
            }, {
                label: 'Tiempo Promedio',
                data: [18, 15, 14, 16, 12],
                borderColor: '#f6c23e',
                yAxisID: 'y1',
                tension: 0.3
            }]
        },
        options: {
            scales: {
                y: { position: 'left', max: 100 },
                y1: { position: 'right', max: 20, grid: { drawOnChartArea: false } }
            }
        }
    });
</script>
@endsection