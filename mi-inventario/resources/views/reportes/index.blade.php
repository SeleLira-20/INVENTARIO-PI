@extends('layouts.app')

@section('title', 'Reportes y Analítica - Universal Inventory')

@section('extra-css')
<style>
    :root {
        --primary-blue: #0d6efd;
        --dark-text: #2d3748;
        --light-text: #718096;
        --bg-body: #f8fafc;
        --border-color: #e2e8f0;
    }

    body { background-color: var(--bg-body); font-family: 'Inter', sans-serif; }

    /* Filtros */
    .filter-card {
        background: white;
        border-radius: 12px;
        padding: 20px 25px;
        border: 1px solid var(--border-color);
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .filter-card input {
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 6px 12px;
        color: var(--dark-text);
        font-size: 14px;
    }

    .btn-aplicar {
        background-color: var(--primary-blue);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 8px 24px;
        font-weight: 600;
        cursor: pointer;
    }

    /* Cards */
    .report-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        border: 1px solid var(--border-color);
        margin-bottom: 24px;
    }

    .report-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .report-title {
        font-size: 16px;
        font-weight: 700;
        color: #1a202c;
    }

    .btn-action {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        padding: 4px 12px;
        font-size: 13px;
        color: var(--light-text);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Plantillas de Reportes (Estilo exacto a la imagen) */
    .template-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    .template-card {
        background: #fdfdfd;
        border: 1px solid #edf2f7;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: flex-start;
        position: relative;
        transition: all 0.2s;
        text-decoration: none;
        color: inherit;
    }

    .template-card:hover { border-color: var(--primary-blue); background: white; }

    .icon-box {
        width: 40px;
        height: 40px;
        background: #ebf4ff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 16px;
        color: var(--primary-blue);
        flex-shrink: 0;
    }

    .template-content h6 { font-size: 14px; font-weight: 700; margin-bottom: 4px; color: #2d3748; }
    .template-content p { font-size: 12px; color: #718096; margin-bottom: 12px; line-height: 1.4; }
    
    .format-info { font-size: 11px; color: #a0aec0; }
    .badge-format {
        background: #edf2f7;
        color: #4a5568;
        padding: 2px 6px;
        border-radius: 4px;
        margin-left: 4px;
        font-weight: 600;
    }

    .btn-download-mini {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        width: 28px;
        height: 28px;
        background: var(--primary-blue);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
    }

    /* Tabla */
    .table thead th {
        background: #f8fafc;
        border-bottom: 1px solid var(--border-color);
        color: #718096;
        font-size: 12px;
        text-transform: none;
        padding: 15px;
    }

    .table tbody td { padding: 15px; font-size: 14px; color: #2d3748; border-bottom: 1px solid #edf2f7; }

    .badge-rot {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }

    @media (max-width: 992px) { .template-grid { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
<div class="container-fluid py-4 px-4">
    <h4 class="fw-bold mb-1" style="color: #1a202c;">Reportes y Analítica</h4>
    <p class="text-muted small mb-4">Análisis de datos y generación de reportes</p>

    <div class="filter-card">
        <i class="far fa-calendar text-muted"></i>
        <span class="fw-bold" style="font-size: 14px; color: #4a5568;">Periodo:</span>
        <input type="date" id="fecha-inicio" value="2026-02-01">
        <span class="text-muted small">hasta</span>
        <input type="date" id="fecha-fin" value="2026-02-17">
        <button class="btn-aplicar" id="btn-aplicar">Aplicar</button>
    </div>

    <div class="report-card">
        <div class="report-header">
            <h6 class="report-title">Tendencia de Stock</h6>
            <button class="btn-action" onclick="descargar('trendChart')"><i class="fas fa-download"></i> Descargar</button>
        </div>
        <canvas id="trendChart" height="80"></canvas>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="report-card">
                <div class="report-header">
                    <h6 class="report-title">Performance por Categoría</h6>
                    <button class="btn-action" onclick="descargar('performanceChart')"><i class="fas fa-upload"></i> Exportar</button>
                </div>
                <canvas id="performanceChart" height="180"></canvas>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="report-card">
                <div class="report-header">
                    <h6 class="report-title">Eficiencia de Picking</h6>
                    <button class="btn-action" onclick="descargar('pickingChart')"><i class="fas fa-upload"></i> Exportar</button>
                </div>
                <canvas id="pickingChart" height="180"></canvas>
            </div>
        </div>
    </div>

    <div class="report-card">
        <h6 class="report-title mb-4">Plantillas de Reportes</h6>
        <div class="template-grid">
            <a href="#" class="template-card" onclick="alert('Descargando PDF...')">
                <div class="icon-box"><i class="far fa-file-alt"></i></div>
                <div class="template-content">
                    <h6>Reporte de Inventario Completo</h6>
                    <p>Estado actual de todo el inventario con ubicaciones y valores</p>
                    <div class="format-info">Formatos: <span class="badge-format">PDF</span><span class="badge-format">Excel</span><span class="badge-format">CSV</span></div>
                </div>
                <div class="btn-download-mini"><i class="fas fa-download"></i></div>
            </a>
            <a href="#" class="template-card">
                <div class="icon-box"><i class="fas fa-chart-line"></i></div>
                <div class="template-content">
                    <h6>Análisis de Movimientos</h6>
                    <p>Entradas, salidas y transferencias del periodo seleccionado</p>
                    <div class="format-info">Formatos: <span class="badge-format">PDF</span><span class="badge-format">Excel</span></div>
                </div>
                <div class="btn-download-mini"><i class="fas fa-download"></i></div>
            </a>
            <a href="#" class="template-card">
                <div class="icon-box"><i class="fas fa-chart-bar"></i></div>
                <div class="template-content">
                    <h6>Performance de Picking</h6>
                    <p>Métricas de eficiencia y precisión en operaciones de picking</p>
                    <div class="format-info">Formatos: <span class="badge-format">PDF</span><span class="badge-format">Excel</span></div>
                </div>
                <div class="btn-download-mini"><i class="fas fa-download"></i></div>
            </a>
            <a href="#" class="template-card">
                <div class="icon-box"><i class="fas fa-sync-alt"></i></div>
                <div class="template-content">
                    <h6>Rotación de Inventario</h6>
                    <p>Análisis de rotación por categoría y producto</p>
                    <div class="format-info">Formatos: <span class="badge-format">PDF</span><span class="badge-format">Excel</span><span class="badge-format">CSV</span></div>
                </div>
                <div class="btn-download-mini"><i class="fas fa-download"></i></div>
            </a>
        </div>
    </div>

    <div class="report-card">
        <h6 class="report-title mb-4">Análisis Detallado por Categoría</h6>
        <div class="table-responsive">
            <table class="table table-borderless align-middle" id="tabla-datos">
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
                        <td class="text-center mov">245</td>
                        <td class="text-center total">$98,500</td>
                        <td class="text-center"><span class="badge-rot" style="background: #fffaf0; color: #ed8936;">4.2x</span></td>
                        <td class="text-end"><a href="#" class="text-primary text-decoration-none small fw-bold">Ver detalles</a></td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Mobiliario</td>
                        <td class="text-center mov">123</td>
                        <td class="text-center total">$67,800</td>
                        <td class="text-center"><span class="badge-rot" style="background: #fff5f5; color: #f56565;">2.8x</span></td>
                        <td class="text-end"><a href="#" class="text-primary text-decoration-none small fw-bold">Ver detalles</a></td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Papelería</td>
                        <td class="text-center mov">456</td>
                        <td class="text-center total">$12,400</td>
                        <td class="text-center"><span class="badge-rot" style="background: #f0fff4; color: #48bb78;">8.5x</span></td>
                        <td class="text-end"><a href="#" class="text-primary text-decoration-none small fw-bold">Ver detalles</a></td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Equipamiento</td>
                        <td class="text-center mov">189</td>
                        <td class="text-center total">$45,600</td>
                        <td class="text-center"><span class="badge-rot" style="background: #ebf8ff; color: #4299e1;">3.5x</span></td>
                        <td class="text-end"><a href="#" class="text-primary text-decoration-none small fw-bold">Ver detalles</a></td>
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
    // Configuración visual de las gráficas (Colores de la imagen)
    const grayColor = '#a0aec0';

    const trendChart = new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: ['Feb 1', 'Feb 5', 'Feb 10', 'Feb 15'],
            datasets: [{
                data: [8200, 8400, 8100, 8300],
                borderColor: '#4299e1',
                backgroundColor: 'rgba(66, 153, 225, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 0
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                y: { min: 5000, max: 10000, grid: { color: '#f7fafc' }, ticks: { color: grayColor } },
                x: { grid: { display: false }, ticks: { color: grayColor } }
            }
        }
    });

    const perfChart = new Chart(document.getElementById('performanceChart'), {
        type: 'bar',
        data: {
            labels: ['Electrónicos', 'Mobiliario', 'Papelería', 'Equipamiento'],
            datasets: [{
                label: 'Movimientos',
                data: [245, 123, 456, 189],
                backgroundColor: '#38a169',
                borderRadius: 4,
                barThickness: 40
            }]
        },
        options: {
            plugins: { legend: { display: true, position: 'bottom' } },
            scales: {
                y: { grid: { color: '#f7fafc' }, ticks: { color: grayColor } },
                x: { grid: { display: false }, ticks: { color: grayColor } }
            }
        }
    });

    const pickingChart = new Chart(document.getElementById('pickingChart'), {
        type: 'line',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May'],
            datasets: [{
                label: 'Precisión %',
                data: [95, 98, 97, 99, 98],
                borderColor: '#48bb78',
                tension: 0.4,
                yAxisID: 'y'
            }, {
                label: 'Tiempo Promedio (min)',
                data: [18, 16, 15, 17, 14],
                borderColor: '#ed8936',
                tension: 0.4,
                yAxisID: 'y1'
            }]
        },
        options: {
            plugins: { legend: { display: true, position: 'bottom' } },
            scales: {
                y: { position: 'left', max: 100, ticks: { color: grayColor } },
                y1: { position: 'right', max: 20, grid: { display: false }, ticks: { color: grayColor } }
            }
        }
    });

    // Simulación del botón Aplicar
    document.getElementById('btn-aplicar').addEventListener('click', function() {
        this.innerText = 'Cargando...';
        setTimeout(() => {
            // Aleatorizar valores
            trendChart.data.datasets[0].data = trendChart.data.datasets[0].data.map(() => Math.floor(Math.random() * 2000 + 7000));
            trendChart.update();
            
            perfChart.data.datasets[0].data = perfChart.data.datasets[0].data.map(() => Math.floor(Math.random() * 400 + 100));
            perfChart.update();

            document.querySelectorAll('.mov').forEach(el => el.innerText = Math.floor(Math.random() * 300 + 100));
            
            this.innerText = 'Aplicar';
            alert("Datos actualizados correctamente.");
        }, 800);
    });

    function descargar(id) {
        const link = document.createElement('a');
        link.download = `${id}.png`;
        link.href = document.getElementById(id).toDataURL();
        link.click();
    }
</script>
@endsection