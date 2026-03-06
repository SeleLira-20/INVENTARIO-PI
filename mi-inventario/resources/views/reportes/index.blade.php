@extends('layouts.app')

@section('title', 'Reportes y Analítica - Universal Inventory')

@section('extra-css')
<style>
    .filter-section {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        margin-bottom: 25px;
        display: flex;
        gap: 15px;
        align-items: center;
        flex-wrap: wrap;
    }

    .filter-section input,
    .filter-section select {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 13px;
    }

    .filter-section button {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
        cursor: pointer;
        font-size: 13px;
    }

    .report-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }

    .report-title {
        font-size: 16px;
        font-weight: 700;
        color: #1e3c72;
        margin-bottom: 20px;
    }

    .chart-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 30px;
    }

    .template-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }

    .template-card {
        background: white;
        border-radius: 12px;
        padding: 15px;
        border: 1px solid #e9ecef;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
    }

    .template-card:hover {
        border-color: #2a5298;
        box-shadow: 0 4px 12px rgba(42, 82, 152, 0.2);
        transform: translateY(-4px);
    }

    .template-icon {
        font-size: 32px;
        color: #2a5298;
        margin-bottom: 10px;
    }

    .template-name {
        font-weight: 600;
        color: #333;
        font-size: 13px;
        margin-bottom: 8px;
    }

    .template-desc {
        font-size: 11px;
        color: #999;
    }

    .export-buttons {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    .export-btn {
        background: #f0f4f8;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 6px 12px;
        font-size: 11px;
        font-weight: 600;
        color: #2a5298;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .export-btn:hover {
        background: #e0e7f1;
    }

    .table-compact {
        font-size: 12px;
    }

    .table-compact th {
        background: #f8f9fa;
        font-weight: 600;
        padding: 12px;
    }

    .table-compact td {
        padding: 12px;
    }

    @media (max-width: 768px) {
        .chart-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<h2 style="font-size: 24px; font-weight: 700; color: #1e3c72; margin-bottom: 8px;">Reportes y Analítica</h2>
<p style="color: #6c757d; font-size: 13px; margin-bottom: 25px;">Análisis de datos y generación de reportes</p>

<!-- Filtros -->
<div class="filter-section">
    <label style="font-weight: 600; color: #333; font-size: 13px;">Período:</label>
    <input type="date" value="2026-02-01">
    <span style="color: #999;">hasta</span>
    <input type="date" value="2026-02-17">
    <button onclick="alert('Filtro aplicado')">Aplicar</button>
</div>

<!-- Tendencia de Stock -->
<div class="chart-row">
    <div class="report-card">
        <h5 class="report-title">Tendencia de Stock</h5>
        <canvas id="trendChart" height="250"></canvas>
    </div>
</div>

<!-- Performance Charts -->
<div class="chart-row">
    <div class="report-card">
        <h5 class="report-title">Performance por Categoría</h5>
        <canvas id="performanceChart" height="250"></canvas>
    </div>
    <div class="report-card">
        <h5 class="report-title">Eficiencia de Picking</h5>
        <canvas id="pickingChart" height="250"></canvas>
    </div>
</div>

<!-- Plantillas de Reportes -->
<div class="report-card">
    <h5 class="report-title">Plantillas de Reportes</h5>
    
    <div class="template-grid">
        <div class="template-card" onclick="alert('Descargando reporte...')">
            <div class="template-icon"><i class="fas fa-file-pdf"></i></div>
            <div class="template-name">Reporte de Inventario Completo</div>
            <div class="template-desc">Estado actual de todos los inventarios y valores</div>
            <div class="export-buttons">
                <button class="export-btn">PDF</button>
                <button class="export-btn">Excel</button>
                <button class="export-btn">CSV</button>
            </div>
        </div>

        <div class="template-card" onclick="alert('Descargando reporte...')">
            <div class="template-icon"><i class="fas fa-chart-line"></i></div>
            <div class="template-name">Análisis de Movimientos</div>
            <div class="template-desc">Entradas, salidas y transmisiones del período seleccionado</div>
            <div class="export-buttons">
                <button class="export-btn">PDF</button>
                <button class="export-btn">Excel</button>
            </div>
        </div>

        <div class="template-card" onclick="alert('Descargando reporte...')">
            <div class="template-icon"><i class="fas fa-hourglass-half"></i></div>
            <div class="template-name">Performance de Picking</div>
            <div class="template-desc">Métricas de eficiencia y precisión en operaciones de picking</div>
            <div class="export-buttons">
                <button class="export-btn">PDF</button>
                <button class="export-btn">Excel</button>
            </div>
        </div>

        <div class="template-card" onclick="alert('Descargando reporte...')">
            <div class="template-icon"><i class="fas fa-sync"></i></div>
            <div class="template-name">Rotación de Inventario</div>
            <div class="template-desc">Análisis de rotación por categoría y producto</div>
            <div class="export-buttons">
                <button class="export-btn">PDF</button>
                <button class="export-btn">Excel</button>
                <button class="export-btn">CSV</button>
            </div>
        </div>
    </div>
</div>

<!-- Análisis Detallado -->
<div class="report-card">
    <h5 class="report-title">Análisis Detallado por Categoría</h5>
    <div style="overflow-x: auto;">
        <table class="table table-hover table-compact">
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th>Movimientos</th>
                    <th>Valor Total</th>
                    <th>Rotación</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Electrónicos</strong></td>
                    <td>245</td>
                    <td>$58,500</td>
                    <td><span style="color: #28a745; font-weight: 600;">↑ 3.2%</span></td>
                    <td><a href="#" style="color: #2a5298; font-weight: 600; text-decoration: none;">Ver detalles</a></td>
                </tr>
                <tr>
                    <td><strong>Mobiliario</strong></td>
                    <td>123</td>
                    <td>$67,800</td>
                    <td><span style="color: #dc3545; font-weight: 600;">↓ 2.1%</span></td>
                    <td><a href="#" style="color: #2a5298; font-weight: 600; text-decoration: none;">Ver detalles</a></td>
                </tr>
                <tr>
                    <td><strong>Papelería</strong></td>
                    <td>456</td>
                    <td>$12,400</td>
                    <td><span style="color: #28a745; font-weight: 600;">↑ 5.8%</span></td>
                    <td><a href="#" style="color: #2a5298; font-weight: 600; text-decoration: none;">Ver detalles</a></td>
                </tr>
                <tr>
                    <td><strong>Equipamiento</strong></td>
                    <td>189</td>
                    <td>$45,600</td>
                    <td><span style="color: #28a745; font-weight: 600;">↑ 1.5%</span></td>
                    <td><a href="#" style="color: #2a5298; font-weight: 600; text-decoration: none;">Ver detalles</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('extra-js')
<script>
    // Tendencia de Stock
    const ctx1 = document.getElementById('trendChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: ['Feb 2', 'Feb 4', 'Feb 6', 'Feb 8', 'Feb 10', 'Feb 12', 'Feb 14', 'Feb 16'],
            datasets: [{
                label: 'Stock Total',
                data: [9000, 8800, 8950, 8700, 8900, 8600, 8450, 8432],
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#0d6efd',
                pointBorderColor: 'white',
                pointBorderWidth: 2,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: false, ticks: { font: { size: 11 } } }
            }
        }
    });

    // Performance por Categoría
    const ctx2 = document.getElementById('performanceChart').getContext('2d');
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: ['Electrónicos', 'Mobiliario', 'Papelería', 'Equipamiento'],
            datasets: [{
                label: 'Movimientos',
                data: [245, 123, 456, 189],
                backgroundColor: '#28a745',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Eficiencia de Picking
    const ctx3 = document.getElementById('pickingChart').getContext('2d');
    new Chart(ctx3, {
        type: 'line',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May'],
            datasets: [
                {
                    label: 'Precisión %',
                    data: [95, 97, 96, 98, 99],
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    yAxisID: 'y'
                },
                {
                    label: 'Tiempo Promedio (min)',
                    data: [22, 20, 18, 16, 15],
                    borderColor: '#ff9800',
                    backgroundColor: 'rgba(255, 152, 0, 0.1)',
                    tension: 0.4,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'top' } },
            scales: {
                y: { type: 'linear', display: true, position: 'left' },
                y1: { type: 'linear', display: true, position: 'right', grid: { drawOnChartArea: false } }
            }
        }
    });
</script>
@endsection