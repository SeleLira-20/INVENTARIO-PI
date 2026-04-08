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

    .filter-card {
        background: white; border-radius: 12px; padding: 20px 25px;
        border: 1px solid var(--border-color); margin-bottom: 25px;
        display: flex; align-items: center; gap: 15px; flex-wrap: wrap;
    }
    .filter-card input {
        border: 1px solid var(--border-color); border-radius: 8px;
        padding: 8px 12px; font-size: 13px; outline: none;
    }
    .btn-aplicar {
        background: #0d6efd; color: white; border: none;
        border-radius: 8px; padding: 8px 20px; font-weight: 600;
        font-size: 13px; cursor: pointer; transition: .2s;
        display: flex; align-items: center; gap: 6px;
    }
    .btn-aplicar:hover { background: #0b5ed7; }
    .btn-aplicar:disabled { opacity: .6; cursor: not-allowed; }

    .report-card {
        background: white; border-radius: 12px; padding: 25px;
        border: 1px solid var(--border-color); margin-bottom: 25px;
    }
    .report-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .report-title  { font-size: 15px; font-weight: 700; color: var(--dark-text); margin: 0; }
    .btn-action {
        background: #f7fafc; border: 1px solid var(--border-color);
        border-radius: 8px; padding: 6px 14px; font-size: 12px;
        font-weight: 600; color: #4a5568; cursor: pointer; transition: .2s;
    }
    .btn-action:hover { background: #edf2f7; }

    /* Stats rápidas */
    .quick-stats { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 25px; }
    .qs-card { background: white; border-radius: 12px; padding: 18px 20px; border: 1px solid var(--border-color); }
    .qs-label { font-size: 11px; color: var(--light-text); font-weight: 600; text-transform: uppercase; }
    .qs-value { font-size: 26px; font-weight: 800; color: var(--dark-text); margin: 4px 0 0; }
    .qs-sub   { font-size: 12px; color: var(--light-text); margin-top: 2px; }

    /* Tabla */
    .table thead th {
        background: #f8fafc; color: #718096; font-size: 12px;
        text-transform: none; padding: 15px; font-weight: 700;
        border-bottom: 1px solid var(--border-color);
    }
    .table tbody td { padding: 15px; font-size: 14px; color: #2d3748; border-bottom: 1px solid #edf2f7; }
    .badge-rot { padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 600; }

    /* Template grid */
    .template-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .template-card {
        display: flex; align-items: center; gap: 15px; padding: 18px;
        border: 1px solid var(--border-color); border-radius: 10px;
        text-decoration: none; transition: .2s; color: inherit;
    }
    .template-card:hover { border-color: #0d6efd; background: #f0f7ff; }
    .icon-box { width: 42px; height: 42px; background: #eff6ff; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #0d6efd; font-size: 18px; flex-shrink: 0; }
    .template-content h6 { margin: 0 0 4px; font-size: 14px; font-weight: 700; color: var(--dark-text); }
    .template-content p  { margin: 0 0 8px; font-size: 12px; color: var(--light-text); }
    .badge-format { background: #edf2f7; color: #4a5568; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; margin-right: 4px; }
    .btn-download-mini { margin-left: auto; color: #0d6efd; font-size: 16px; }


    /* ── Incidentes ── */
    .inc-card { background: white; border-radius: 12px; border: 1px solid var(--border-color); margin-bottom: 25px; overflow: hidden; }
    .inc-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 25px; border-bottom: 1px solid var(--border-color); }
    .inc-title { font-size: 15px; font-weight: 700; color: var(--dark-text); margin: 0; }
    .inc-badge { padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .urgencia-ALTA  { background: #fee2e2; color: #dc2626; }
    .urgencia-MEDIA { background: #fef9c3; color: #854d0e; }
    .urgencia-BAJA  { background: #dcfce7; color: #166534; }
    .estado-Reportado { background: #eff6ff; color: #2563eb; }
    .estado-Resuelto  { background: #dcfce7; color: #166534; }
    .btn-resolver {
        background: #16a34a; color: white; border: none; border-radius: 6px;
        padding: 5px 12px; font-size: 12px; font-weight: 600; cursor: pointer; transition: .2s;
    }
    .btn-resolver:hover { background: #15803d; }
    .btn-resolver:disabled { opacity: .5; cursor: not-allowed; }
    .inc-filtro { padding:6px 14px; border-radius:20px; border:1px solid var(--border-color); background:white; font-size:12px; font-weight:600; color:var(--light-text); cursor:pointer; transition:.2s; }
    .inc-filtro.active { background:#1e3c72; color:white; border-color:#1e3c72; }
    .inc-filtro:hover:not(.active) { background:#f1f5f9; }
    .inc-row { display:grid; grid-template-columns:60px 1fr 120px 100px 110px 100px; gap:12px; align-items:center; padding:14px 0; border-bottom:1px solid #f1f5f9; font-size:13px; }
    .inc-row:last-child { border-bottom:none; }
    .inc-thead { font-size:11px; font-weight:700; color:var(--light-text); text-transform:uppercase; letter-spacing:.5px; }
    .inc-empty { text-align: center; padding: 40px; color: var(--light-text); }


    /* ── Modal incidente ── */
    .inc-modal-overlay { display:none; position:fixed; inset:0; background:rgba(15,23,42,.5); z-index:3000; align-items:center; justify-content:center; }
    .inc-modal-overlay.open { display:flex; }
    .inc-modal-box { background:white; border-radius:16px; padding:32px; width:100%; max-width:540px; box-shadow:0 24px 64px rgba(0,0,0,.2); position:relative; max-height:90vh; overflow-y:auto; animation:popIn .2s ease; }
    @keyframes popIn { from{transform:scale(.95);opacity:0} to{transform:scale(1);opacity:1} }
    .inc-modal-close { position:absolute; top:16px; right:16px; background:#f1f5f9; border:none; width:30px; height:30px; border-radius:8px; cursor:pointer; color:#64748b; font-size:14px; display:flex; align-items:center; justify-content:center; }
    .inc-modal-close:hover { background:#e2e8f0; }
    .inc-detail-row { display:flex; justify-content:space-between; align-items:flex-start; padding:12px 0; border-bottom:1px solid #f1f5f9; font-size:13px; }
    .inc-detail-row:last-child { border-bottom:none; }
    .inc-detail-label { color:#64748b; font-weight:600; font-size:12px; text-transform:uppercase; letter-spacing:.3px; flex-shrink:0; width:140px; }
    .inc-detail-value { color:#1e293b; font-weight:500; text-align:right; flex:1; }
    .btn-resolver-modal { background:#16a34a; color:white; border:none; border-radius:10px; padding:12px 24px; font-weight:700; font-size:14px; cursor:pointer; display:flex; align-items:center; gap:8px; transition:.2s; }
    .btn-resolver-modal:hover { background:#15803d; }
    .btn-resolver-modal:disabled { opacity:.6; cursor:not-allowed; }
    .btn-ya-resuelto { background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; border-radius:10px; padding:12px 24px; font-weight:700; font-size:14px; cursor:default; display:flex; align-items:center; gap:8px; }

    /* Loading overlay */
    .chart-loading { display: flex; align-items: center; justify-content: center; height: 120px; color: var(--light-text); gap: 10px; font-size: 13px; }
    .spin { animation: spin .7s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Toast */
    .toast-container { position: fixed; bottom: 24px; right: 24px; z-index: 5000; display: flex; flex-direction: column; gap: 8px; }
    .toast { background: #1e293b; color: white; padding: 12px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; animation: slideIn .3s ease; box-shadow: 0 4px 12px rgba(0,0,0,.2); }
    .toast.success { border-left: 4px solid #22c55e; }
    .toast.error   { border-left: 4px solid #ef4444; }
    @keyframes slideIn { from { transform: translateX(120%); opacity:0; } to { transform: translateX(0); opacity:1; } }

    @media (max-width: 992px) { .template-grid { grid-template-columns: 1fr; } .quick-stats { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 600px)  { .quick-stats { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
<div class="container-fluid py-4 px-4">
    <h4 class="fw-bold mb-1" style="color: #1a202c;">Reportes y Analítica</h4>
    <p class="text-muted small mb-4">Análisis de datos en tiempo real desde la API</p>

    {{-- Filtro de fechas --}}
    <div class="filter-card">
        <i class="far fa-calendar text-muted"></i>
        <span class="fw-bold" style="font-size:14px;color:#4a5568;">Periodo:</span>
        <input type="date" id="fecha-inicio">
        <span class="text-muted small">hasta</span>
        <input type="date" id="fecha-fin">
        <button class="btn-aplicar" id="btn-aplicar" onclick="cargarTodo()">
            <i class="fas fa-sync-alt" id="icon-aplicar"></i> Aplicar
        </button>
    </div>

    {{-- Quick stats --}}
    <div class="quick-stats">
        <div class="qs-card">
            <div class="qs-label">Total Productos</div>
            <div class="qs-value" id="qs-total">—</div>
            <div class="qs-sub">en inventario</div>
        </div>
        <div class="qs-card">
            <div class="qs-label">Valor Total</div>
            <div class="qs-value" id="qs-valor">—</div>
            <div class="qs-sub">stock × precio</div>
        </div>
        <div class="qs-card">
            <div class="qs-label">Stock Bajo</div>
            <div class="qs-value" id="qs-bajo" style="color:#ca8a04;">—</div>
            <div class="qs-sub">requieren reposición</div>
        </div>
        <div class="qs-card">
            <div class="qs-label">Total Movimientos</div>
            <div class="qs-value" id="qs-mov">—</div>
            <div class="qs-sub">entradas y salidas</div>
        </div>
    </div>

    {{-- Tendencia de Stock --}}
    <div class="report-card">
        <div class="report-header">
            <h6 class="report-title">Tendencia de Stock por Categoría</h6>
            <button class="btn-action" onclick="descargar('trendChart')"><i class="fas fa-download"></i> Descargar</button>
        </div>
        <div id="trend-loading" class="chart-loading"><i class="fas fa-spinner spin"></i> Cargando datos...</div>
        <canvas id="trendChart" height="80" style="display:none;"></canvas>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="report-card">
                <div class="report-header">
                    <h6 class="report-title">Performance por Categoría</h6>
                    <button class="btn-action" onclick="descargar('performanceChart')"><i class="fas fa-upload"></i> Exportar</button>
                </div>
                <div id="perf-loading" class="chart-loading"><i class="fas fa-spinner spin"></i> Cargando...</div>
                <canvas id="performanceChart" height="180" style="display:none;"></canvas>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="report-card">
                <div class="report-header">
                    <h6 class="report-title">Movimientos por Tipo</h6>
                    <button class="btn-action" onclick="descargar('movChart')"><i class="fas fa-upload"></i> Exportar</button>
                </div>
                <div id="mov-loading" class="chart-loading"><i class="fas fa-spinner spin"></i> Cargando...</div>
                <canvas id="movChart" height="180" style="display:none;"></canvas>
            </div>
        </div>
    </div>

    {{-- Plantillas --}}
    <div class="report-card">
        <h6 class="report-title mb-4">Plantillas de Reportes</h6>
        <div class="template-grid">
            <a href="#" class="template-card" onclick="generarPDF(); return false;" style="border-color:#dc2626;">
                <div class="icon-box" style="background:#fef2f2;color:#dc2626;"><i class="fas fa-file-pdf"></i></div>
                <div class="template-content">
                    <h6>Reporte General PDF</h6>
                    <p>Resumen ejecutivo + productos + movimientos en un solo documento</p>
                    <div class="format-info"><span class="badge-format" style="background:#fef2f2;color:#dc2626;">PDF</span></div>
                </div>
                <div class="btn-download-mini" style="color:#dc2626;"><i class="fas fa-download"></i></div>
            </a>
            <a href="#" class="template-card" onclick="exportarMovimientosCSV(); return false;">
                <div class="icon-box"><i class="fas fa-chart-line"></i></div>
                <div class="template-content">
                    <h6>Análisis de Movimientos</h6>
                    <p>Entradas y salidas del periodo seleccionado</p>
                    <div class="format-info"><span class="badge-format">CSV</span></div>
                </div>
                <div class="btn-download-mini"><i class="fas fa-download"></i></div>
            </a>
            <a href="#" class="template-card" onclick="exportarStockBajoCSV(); return false;">
                <div class="icon-box"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="template-content">
                    <h6>Productos con Stock Bajo</h6>
                    <p>Listado de productos que requieren reposición urgente</p>
                    <div class="format-info"><span class="badge-format">CSV</span></div>
                </div>
                <div class="btn-download-mini"><i class="fas fa-download"></i></div>
            </a>
            <a href="#" class="template-card" onclick="exportarCategoriaCSV(); return false;">
                <div class="icon-box"><i class="fas fa-sync-alt"></i></div>
                <div class="template-content">
                    <h6>Análisis por Categoría</h6>
                    <p>Valor total y cantidad de productos por categoría</p>
                    <div class="format-info"><span class="badge-format">CSV</span></div>
                </div>
                <div class="btn-download-mini"><i class="fas fa-download"></i></div>
            </a>
        </div>
    </div>

    {{-- Tabla análisis por categoría --}}
    <div class="report-card">
        <h6 class="report-title mb-4">Análisis Detallado por Categoría</h6>
        <div class="table-responsive">
            <table class="table table-borderless align-middle" id="tabla-datos">
                <thead>
                    <tr>
                        <th>Categoría</th>
                        <th class="text-center">Productos</th>
                        <th class="text-center">Stock Total</th>
                        <th class="text-center">Valor Total</th>
                        <th class="text-center">Stock Bajo</th>
                        <th class="text-end">Estado</th>
                    </tr>
                </thead>
                <tbody id="tabla-body">
                    <tr><td colspan="6" class="text-center text-muted py-4">
                        <i class="fas fa-spinner spin me-2"></i>Cargando datos...
                    </td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>


{{-- Modal Detalle Incidente --}}
<div class="inc-modal-overlay" id="incModalOverlay">
    <div class="inc-modal-box">
        <button class="inc-modal-close" onclick="cerrarModalInc()"><i class="fas fa-times"></i></button>
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
            <div style="width:44px;height:44px;background:#fef9c3;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;color:#ca8a04;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <div style="font-size:18px;font-weight:700;color:#1e293b;" id="inc-modal-titulo">Incidente</div>
                <div style="font-size:13px;color:#64748b;" id="inc-modal-folio">—</div>
            </div>
        </div>
        <div id="inc-modal-body"></div>
        <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px;">
            <button style="background:#f1f5f9;border:1px solid #e2e8f0;border-radius:8px;padding:10px 20px;font-weight:600;cursor:pointer;" onclick="cerrarModalInc()">Cerrar</button>
            <div id="inc-modal-accion"></div>
        </div>
    </div>
</div>

<div class="toast-container" id="toastContainer"></div>

{{-- Sección Incidentes --}}
<div class="inc-card">
        <div class="inc-header">
            <h6 class="inc-title">
                <i class="fas fa-exclamation-triangle" style="color:#f59e0b;margin-right:8px;"></i>
                Incidentes Reportados desde la App
            </h6>
            <div style="display:flex;align-items:center;gap:12px;">
                <span style="font-size:12px;color:var(--light-text);">
                    Total: <strong id="inc-total">—</strong>
                </span>
                <button class="btn-action" onclick="cargarIncidentes()">
                    <i class="fas fa-sync-alt" id="inc-refresh"></i> Actualizar
                </button>
            </div>
        </div>

        {{-- Filtros de incidentes --}}
        <div style="padding:15px 25px;border-bottom:1px solid var(--border-color);display:flex;gap:10px;flex-wrap:wrap;">
            <button class="inc-filtro active" data-estado="" onclick="filtrarIncidentes(this)">Todos</button>
            <button class="inc-filtro" data-estado="Reportado" onclick="filtrarIncidentes(this)">Reportados</button>
            <button class="inc-filtro" data-estado="Resuelto" onclick="filtrarIncidentes(this)">Resueltos</button>
        </div>

        <div style="padding:20px 25px;">
            <div id="inc-loading" style="text-align:center;padding:30px;color:var(--light-text);">
                <i class="fas fa-spinner spin" style="margin-right:8px;"></i> Cargando incidentes...
            </div>
            <div id="inc-lista" style="display:none;"></div>
        </div>
    </div>

@endsection

@section('extra-js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script>
// ── Config ────────────────────────────────────────────────────────────────
const API_PRODUCTOS  = '/inventario/api/productos';
const API_ALERTAS    = '/inventario/api/alertas';
const API_MOV        = '/inventario/api/movimientos';

// Cache global
let cacheProductos   = [];
let cacheMovimientos = [];

// Instancias de charts
let chartTrend = null, chartPerf = null, chartMov = null;

// ── Toast ─────────────────────────────────────────────────────────────────
function toast(msg, tipo = 'success') {
    const c = document.getElementById('toastContainer');
    const t = document.createElement('div');
    t.className = `toast ${tipo}`;
    t.innerHTML = `<i class="fas fa-${tipo==='success'?'check-circle':'times-circle'}"></i> ${msg}`;
    c.appendChild(t);
    setTimeout(() => t.remove(), 3500);
}

// ── Fecha por defecto (mes actual) ────────────────────────────────────────
function initFechas() {
    const hoy   = new Date();
    const inicio = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
    document.getElementById('fecha-fin').value    = hoy.toISOString().slice(0,10);
    document.getElementById('fecha-inicio').value = inicio.toISOString().slice(0,10);
}

// ── Cargar todo ───────────────────────────────────────────────────────────
async function cargarTodo() {
    const btn  = document.getElementById('btn-aplicar');
    const icon = document.getElementById('icon-aplicar');
    btn.disabled = true;
    icon.classList.add('fa-spin');

    try {
        const [productos, movimientos, alertas] = await Promise.all([
            fetchProductos(),
            fetchMovimientos(),
            fetchAlertas(),
        ]);

        cacheProductos   = productos;
        cacheMovimientos = movimientos;

        actualizarQuickStats(productos, movimientos, alertas);
        renderTrendChart(productos);
        renderPerfChart(productos);
        renderMovChart(movimientos);
        renderTabla(productos);

    } catch(err) {
        toast('Error al cargar datos: ' + err.message, 'error');
    } finally {
        btn.disabled = false;
        icon.classList.remove('fa-spin');
    }
}

// ── Fetchers ──────────────────────────────────────────────────────────────
async function fetchProductos() {
    const r = await fetch(API_PRODUCTOS);
    if (!r.ok) throw new Error('Productos: HTTP ' + r.status);
    const d = await r.json();
    return d.productos ?? [];
}

async function fetchAlertas() {
    const r = await fetch(API_ALERTAS);
    if (!r.ok) return [];
    const d = await r.json();
    return d.productos ?? [];
}

async function fetchMovimientos() {
    try {
        const r = await fetch(API_MOV);
        if (!r.ok) return [];
        const d = await r.json();
        return d.movimientos ?? [];
    } catch { return []; }
}

// ── Quick Stats ───────────────────────────────────────────────────────────
function actualizarQuickStats(productos, movimientos, alertas) {
    const valor = productos.reduce((a,p) => a + parseFloat(p.precio_unitario) * p.stock_actual, 0);
    document.getElementById('qs-total').textContent = productos.length;
    document.getElementById('qs-valor').textContent = '$' + valor.toLocaleString('es-MX', {maximumFractionDigits:0});
    document.getElementById('qs-bajo').textContent  = alertas.length;
    document.getElementById('qs-mov').textContent   = movimientos.length || '—';
}

// ── Agrupar por categoría ─────────────────────────────────────────────────
function agruparPorCategoria(productos) {
    const mapa = {};
    productos.forEach(p => {
        const cat = p.categoria ?? 'Otros';
        if (!mapa[cat]) mapa[cat] = { productos: 0, stock: 0, valor: 0, bajo: 0 };
        mapa[cat].productos++;
        mapa[cat].stock += p.stock_actual;
        mapa[cat].valor += parseFloat(p.precio_unitario) * p.stock_actual;
        if (p.stock_actual <= p.stock_minimo) mapa[cat].bajo++;
    });
    return mapa;
}

// ── Chart: Tendencia de Stock por Categoría (barras apiladas) ─────────────
function renderTrendChart(productos) {
    const grupos = agruparPorCategoria(productos);
    const cats   = Object.keys(grupos);
    const valores = cats.map(c => grupos[c].stock);
    const colores = ['#4299e1','#48bb78','#ed8936','#9f7aea','#f56565','#38b2ac'];

    document.getElementById('trend-loading').style.display = 'none';
    const canvas = document.getElementById('trendChart');
    canvas.style.display = 'block';

    if (chartTrend) chartTrend.destroy();
    chartTrend = new Chart(canvas, {
        type: 'bar',
        data: {
            labels: cats,
            datasets: [{
                label: 'Unidades en Stock',
                data: valores,
                backgroundColor: cats.map((_,i) => colores[i % colores.length]),
                borderRadius: 6,
                barThickness: 50,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f7fafc' }, ticks: { color: '#a0aec0' } },
                x: { grid: { display: false }, ticks: { color: '#a0aec0' } }
            }
        }
    });
}

// ── Chart: Performance por Categoría (valor total) ────────────────────────
function renderPerfChart(productos) {
    const grupos = agruparPorCategoria(productos);
    const cats   = Object.keys(grupos);
    const valores = cats.map(c => parseFloat(grupos[c].valor.toFixed(2)));

    document.getElementById('perf-loading').style.display = 'none';
    const canvas = document.getElementById('performanceChart');
    canvas.style.display = 'block';

    if (chartPerf) chartPerf.destroy();
    chartPerf = new Chart(canvas, {
        type: 'bar',
        data: {
            labels: cats,
            datasets: [{
                label: 'Valor en inventario ($)',
                data: valores,
                backgroundColor: '#38a169',
                borderRadius: 4,
                barThickness: 40,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: true, position: 'bottom' } },
            scales: {
                y: { grid: { color: '#f7fafc' }, ticks: { color: '#a0aec0', callback: v => '$' + v.toLocaleString() } },
                x: { grid: { display: false }, ticks: { color: '#a0aec0' } }
            }
        }
    });
}

// ── Chart: Movimientos por Tipo ───────────────────────────────────────────
function renderMovChart(movimientos) {
    document.getElementById('mov-loading').style.display = 'none';
    const canvas = document.getElementById('movChart');
    canvas.style.display = 'block';

    if (chartMov) { chartMov.destroy(); chartMov = null; }

    // Si no hay movimientos mostrar mensaje
    if (!movimientos.length) {
        document.getElementById('mov-loading').style.display = 'flex';
        document.getElementById('mov-loading').innerHTML = '<i class="fas fa-inbox" style="font-size:24px;margin-right:8px;"></i> Sin movimientos registrados';
        canvas.style.display = 'none';
        return;
    }

    const entradas = movimientos.filter(m => m.tipo_movimiento === 'ENTRADA').length;
    const salidas  = movimientos.filter(m => m.tipo_movimiento === 'SALIDA').length;

    // Construir datos solo con valores > 0
    const labels = [];
    const datos  = [];
    const colores = [];
    if (entradas > 0) { labels.push('Entradas'); datos.push(entradas); colores.push('#48bb78'); }
    if (salidas  > 0) { labels.push('Salidas');  datos.push(salidas);  colores.push('#f56565'); }

    chartMov = new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data: datos,
                backgroundColor: colores,
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 15 } },
                tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.raw} movimiento${ctx.raw !== 1 ? 's' : ''}` } }
            }
        }
    });
}

// ── Tabla por categoría ───────────────────────────────────────────────────
function renderTabla(productos) {
    const grupos = agruparPorCategoria(productos);
    const tbody  = document.getElementById('tabla-body');

    if (!Object.keys(grupos).length) {
        tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">Sin datos</td></tr>`;
        return;
    }

    const coloresBadge = {
        ok:   { bg: '#f0fff4', color: '#38a169', texto: 'OK' },
        warn: { bg: '#fffaf0', color: '#ed8936', texto: 'Atención' },
        crit: { bg: '#fff5f5', color: '#f56565', texto: 'Crítico' },
    };

    tbody.innerHTML = Object.entries(grupos).map(([cat, g]) => {
        const pct   = g.total > 0 ? Math.round((g.bajo / g.productos) * 100) : 0;
        const nivel = g.bajo === 0 ? 'ok' : (pct < 50 ? 'warn' : 'crit');
        const b     = coloresBadge[nivel];
        return `
        <tr>
            <td class="fw-bold">${cat}</td>
            <td class="text-center">${g.productos}</td>
            <td class="text-center">${g.stock.toLocaleString()}</td>
            <td class="text-center fw-bold">$${g.valor.toLocaleString('es-MX',{maximumFractionDigits:0})}</td>
            <td class="text-center">
                <span class="badge-rot" style="background:${g.bajo>0?'#fff5f5':'#f0fff4'};color:${g.bajo>0?'#f56565':'#38a169'};">
                    ${g.bajo} producto${g.bajo!==1?'s':''}
                </span>
            </td>
            <td class="text-end">
                <span class="badge-rot" style="background:${b.bg};color:${b.color};">${b.texto}</span>
            </td>
        </tr>`;
    }).join('');
}

// ── Exportar CSV helpers ──────────────────────────────────────────────────
function descargarCSV(nombre, cabecera, filas) {
    const csv = [cabecera.join(','), ...filas].join('\n');
    const a   = document.createElement('a');
    a.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
    a.download = `${nombre}_${new Date().toISOString().slice(0,10)}.csv`;
    a.click();
    toast(`${nombre}.csv descargado`);
}

function exportarInventarioCSV() {
    if (!cacheProductos.length) { toast('Carga los datos primero', 'error'); return; }
    descargarCSV('inventario_completo',
        ['ID','SKU','Nombre','Categoria','Stock Actual','Stock Minimo','Precio','Estado'],
        cacheProductos.map(p => [p.id_producto,p.sku,`"${p.nombre}"`,p.categoria??'Otros',p.stock_actual,p.stock_minimo,p.precio_unitario,p.estado??'Activo'])
    );
}

function exportarMovimientosCSV() {
    if (!cacheMovimientos.length) { toast('Sin movimientos registrados', 'error'); return; }
    descargarCSV('movimientos',
        ['ID','ID Producto','Tipo','Cantidad','Fecha','Usuario','Observaciones'],
        cacheMovimientos.map(m => [m.id_movimiento,m.id_producto,m.tipo_movimiento,m.cantidad,m.fecha_movimiento,m.id_usuario,`"${m.observaciones??''}"`])
    );
}

function exportarStockBajoCSV() {
    const bajo = cacheProductos.filter(p => p.stock_actual <= p.stock_minimo);
    if (!bajo.length) { toast('No hay productos con stock bajo', 'error'); return; }
    descargarCSV('stock_bajo',
        ['ID','SKU','Nombre','Categoria','Stock Actual','Stock Minimo'],
        bajo.map(p => [p.id_producto,p.sku,`"${p.nombre}"`,p.categoria??'Otros',p.stock_actual,p.stock_minimo])
    );
}

function exportarCategoriaCSV() {
    if (!cacheProductos.length) { toast('Carga los datos primero', 'error'); return; }
    const grupos = agruparPorCategoria(cacheProductos);
    descargarCSV('analisis_categoria',
        ['Categoria','Productos','Stock Total','Valor Total','Stock Bajo'],
        Object.entries(grupos).map(([cat,g]) => [cat, g.productos, g.stock, g.valor.toFixed(2), g.bajo])
    );
}

// ── Descargar gráfica como imagen ─────────────────────────────────────────
function descargar(id) {
    const canvas = document.getElementById(id);
    if (!canvas) return;
    const a = document.createElement('a');
    a.download = `${id}_${new Date().toISOString().slice(0,10)}.png`;
    a.href = canvas.toDataURL();
    a.click();
    toast('Gráfica descargada');
}

// ── Init ──────────────────────────────────────────────────────────────────

// ══════════════════════════════════════════════════════════════
// REPORTE GENERAL PDF
// ══════════════════════════════════════════════════════════════
async function generarPDF() {
    if (!cacheProductos.length) { toast('Carga los datos primero', 'error'); return; }

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
    const fecha = new Date().toLocaleDateString('es-MX', { year:'numeric', month:'long', day:'numeric' });
    const ahora = new Date().toLocaleTimeString('es-MX', { hour:'2-digit', minute:'2-digit' });
    let y = 0;

    // ── Portada ──────────────────────────────────────────────
    doc.setFillColor(30, 60, 114);
    doc.rect(0, 0, 210, 60, 'F');
    doc.setTextColor(255, 255, 255);
    doc.setFontSize(24);
    doc.setFont('helvetica', 'bold');
    doc.text('Universal Inventory', 105, 25, { align: 'center' });
    doc.setFontSize(14);
    doc.setFont('helvetica', 'normal');
    doc.text('Reporte General de Inventario', 105, 35, { align: 'center' });
    doc.setFontSize(10);
    doc.text(`Generado el ${fecha} a las ${ahora}`, 105, 47, { align: 'center' });

    // ── Resumen Ejecutivo ────────────────────────────────────
    y = 75;
    doc.setTextColor(30, 60, 114);
    doc.setFontSize(14);
    doc.setFont('helvetica', 'bold');
    doc.text('Resumen Ejecutivo', 14, y);
    doc.setDrawColor(30, 60, 114);
    doc.line(14, y + 2, 196, y + 2);
    y += 12;

    const valor = cacheProductos.reduce((a, p) => a + parseFloat(p.precio_unitario) * p.stock_actual, 0);
    const enStock  = cacheProductos.filter(p => p.stock_actual > p.stock_minimo).length;
    const bajo     = cacheProductos.filter(p => p.stock_actual > 0 && p.stock_actual <= p.stock_minimo).length;
    const sinStock = cacheProductos.filter(p => p.stock_actual === 0).length;
    const entradas = cacheMovimientos.filter(m => m.tipo_movimiento === 'ENTRADA').length;
    const salidas  = cacheMovimientos.filter(m => m.tipo_movimiento === 'SALIDA').length;

    const stats = [
        ['Total de Productos', cacheProductos.length, 'Productos en catálogo'],
        ['En Stock',           enStock,                'Con stock sobre el mínimo'],
        ['Stock Bajo',         bajo,                   'Requieren reposición'],
        ['Sin Stock',          sinStock,               'Agotados'],
        ['Valor Total',        '$' + valor.toLocaleString('es-MX', {maximumFractionDigits:0}), 'Valor del inventario'],
        ['Total Movimientos',  cacheMovimientos.length,'Entradas y salidas'],
        ['Entradas',           entradas,               'Ingresos de productos'],
        ['Salidas',            salidas,                'Despachos de productos'],
    ];

    // Cards de stats en grid 2x4
    const cardW = 88, cardH = 20, marginX = 14;
    stats.forEach((s, i) => {
        const col = i % 2;
        const row = Math.floor(i / 2);
        const x = marginX + col * (cardW + 10);
        const cy = y + row * (cardH + 4);
        doc.setFillColor(248, 250, 252);
        doc.roundedRect(x, cy, cardW, cardH, 3, 3, 'F');
        doc.setDrawColor(226, 232, 240);
        doc.roundedRect(x, cy, cardW, cardH, 3, 3, 'S');
        doc.setTextColor(100, 116, 139);
        doc.setFontSize(8);
        doc.setFont('helvetica', 'normal');
        doc.text(s[0], x + 4, cy + 7);
        doc.setTextColor(30, 41, 59);
        doc.setFontSize(14);
        doc.setFont('helvetica', 'bold');
        doc.text(String(s[1]), x + 4, cy + 15);
        doc.setTextColor(148, 163, 184);
        doc.setFontSize(7);
        doc.setFont('helvetica', 'normal');
        doc.text(s[2], x + 50, cy + 15);
    });

    y += 4 * (cardH + 4) + 15;

    // ── Análisis por Categoría ───────────────────────────────
    doc.setTextColor(30, 60, 114);
    doc.setFontSize(14);
    doc.setFont('helvetica', 'bold');
    doc.text('Análisis por Categoría', 14, y);
    doc.line(14, y + 2, 196, y + 2);
    y += 8;

    const grupos = {};
    cacheProductos.forEach(p => {
        const cat = p.categoria ?? 'Otros';
        if (!grupos[cat]) grupos[cat] = { productos:0, stock:0, valor:0, bajo:0 };
        grupos[cat].productos++;
        grupos[cat].stock += p.stock_actual;
        grupos[cat].valor += parseFloat(p.precio_unitario) * p.stock_actual;
        if (p.stock_actual <= p.stock_minimo) grupos[cat].bajo++;
    });

    doc.autoTable({
        startY: y,
        head: [['Categoría', 'Productos', 'Stock Total', 'Valor Total', 'Stock Bajo', 'Estado']],
        body: Object.entries(grupos).map(([cat, g]) => [
            cat,
            g.productos,
            g.stock.toLocaleString(),
            '$' + g.valor.toLocaleString('es-MX', {maximumFractionDigits:0}),
            g.bajo,
            g.bajo === 0 ? 'OK' : g.bajo < g.productos/2 ? 'Atención' : 'Crítico'
        ]),
        styles: { fontSize: 9, cellPadding: 4 },
        headStyles: { fillColor: [30, 60, 114], textColor: 255, fontStyle: 'bold' },
        alternateRowStyles: { fillColor: [248, 250, 252] },
        margin: { left: 14, right: 14 },
    });

    y = doc.lastAutoTable.finalY + 15;

    // ── Nueva página: Catálogo de Productos ──────────────────
    doc.addPage();
    doc.setFillColor(30, 60, 114);
    doc.rect(0, 0, 210, 18, 'F');
    doc.setTextColor(255, 255, 255);
    doc.setFontSize(12);
    doc.setFont('helvetica', 'bold');
    doc.text('Catálogo Completo de Productos', 105, 12, { align: 'center' });

    doc.autoTable({
        startY: 25,
        head: [['ID', 'SKU', 'Nombre', 'Categoría', 'Stock', 'Mínimo', 'Precio', 'Estado']],
        body: cacheProductos.map(p => [
            p.id_producto,
            p.sku,
            p.nombre,
            p.categoria ?? 'Otros',
            p.stock_actual,
            p.stock_minimo,
            '$' + parseFloat(p.precio_unitario).toLocaleString('es-MX', {minimumFractionDigits:2}),
            p.estado ?? 'Activo'
        ]),
        styles: { fontSize: 8, cellPadding: 3 },
        headStyles: { fillColor: [30, 60, 114], textColor: 255, fontStyle: 'bold' },
        alternateRowStyles: { fillColor: [248, 250, 252] },
        margin: { left: 14, right: 14 },
        didParseCell: (data) => {
            if (data.section === 'body' && data.column.index === 7) {
                const val = data.cell.raw;
                if (val === 'Activo') data.cell.styles.textColor = [22, 163, 74];
                else data.cell.styles.textColor = [220, 38, 38];
            }
        }
    });

    // ── Nueva página: Movimientos ────────────────────────────
    if (cacheMovimientos.length > 0) {
        doc.addPage();
        doc.setFillColor(30, 60, 114);
        doc.rect(0, 0, 210, 18, 'F');
        doc.setTextColor(255, 255, 255);
        doc.setFontSize(12);
        doc.setFont('helvetica', 'bold');
        doc.text('Registro de Movimientos', 105, 12, { align: 'center' });

        doc.autoTable({
            startY: 25,
            head: [['ID', 'Producto', 'Tipo', 'Cantidad', 'Usuario', 'Fecha', 'Observaciones']],
            body: cacheMovimientos.map(m => [
                m.id_movimiento,
                m.id_producto,
                m.tipo_movimiento,
                m.cantidad,
                m.id_usuario,
                m.fecha_movimiento ? new Date(m.fecha_movimiento).toLocaleDateString('es-MX') : '—',
                m.observaciones ?? '—'
            ]),
            styles: { fontSize: 8, cellPadding: 3 },
            headStyles: { fillColor: [30, 60, 114], textColor: 255, fontStyle: 'bold' },
            alternateRowStyles: { fillColor: [248, 250, 252] },
            margin: { left: 14, right: 14 },
            didParseCell: (data) => {
                if (data.section === 'body' && data.column.index === 2) {
                    if (data.cell.raw === 'ENTRADA') data.cell.styles.textColor = [22, 163, 74];
                    else data.cell.styles.textColor = [220, 38, 38];
                }
            }
        });
    }

    // ── Pie de página en todas las páginas ───────────────────
    const totalPags = doc.internal.getNumberOfPages();
    for (let i = 1; i <= totalPags; i++) {
        doc.setPage(i);
        doc.setFillColor(248, 250, 252);
        doc.rect(0, 285, 210, 12, 'F');
        doc.setTextColor(148, 163, 184);
        doc.setFontSize(8);
        doc.setFont('helvetica', 'normal');
        doc.text('Universal Inventory — Reporte Confidencial', 14, 292);
        doc.text(`Página ${i} de ${totalPags}`, 196, 292, { align: 'right' });
    }

    doc.save(`reporte_general_${new Date().toISOString().slice(0,10)}.pdf`);
    toast('PDF generado correctamente');
}


// ══════════════════════════════════════════════════════════════
// INCIDENTES
// ══════════════════════════════════════════════════════════════
let cacheIncidentes = [];
let filtroEstadoInc = '';

const urgenciaMap = {
    'alta': 'ALTA', 'media': 'MEDIA', 'baja': 'BAJA',
    'ALTA': 'ALTA', 'MEDIA': 'MEDIA', 'BAJA': 'BAJA',
};

async function cargarIncidentes() {
    const icon = document.getElementById('inc-refresh');
    icon.classList.add('fa-spin');
    document.getElementById('inc-loading').style.display = 'block';
    document.getElementById('inc-lista').style.display = 'none';

    try {
        const r = await fetch('/inventario/api/incidentes');
        if (!r.ok) throw new Error('HTTP ' + r.status);
        const d = await r.json();
        cacheIncidentes = d.incidentes ?? [];
        document.getElementById('inc-total').textContent = cacheIncidentes.length;
        renderIncidentes();
    } catch(err) {
        document.getElementById('inc-loading').innerHTML = '<i class="fas fa-exclamation-circle" style="color:#ef4444;margin-right:8px;"></i> Error al cargar incidentes';
    } finally {
        icon.classList.remove('fa-spin');
    }
}

function filtrarIncidentes(btn) {
    document.querySelectorAll('.inc-filtro').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    filtroEstadoInc = btn.dataset.estado;
    renderIncidentes();
}

function renderIncidentes() {
    const loading = document.getElementById('inc-loading');
    const lista   = document.getElementById('inc-lista');
    loading.style.display = 'none';
    lista.style.display = 'block';

    const filtrados = filtroEstadoInc
        ? cacheIncidentes.filter(i => i.estado === filtroEstadoInc)
        : cacheIncidentes;

    if (!filtrados.length) {
        lista.innerHTML = `<div class="inc-empty">
            <i class="fas fa-check-circle" style="font-size:32px;color:#16a34a;display:block;margin-bottom:10px;"></i>
            No hay incidentes ${filtroEstadoInc || 'registrados'}
        </div>`;
        return;
    }

    lista.innerHTML = `
        <div class="inc-row inc-thead">
            <span>ID</span>
            <span>Problema</span>
            <span>Urgencia</span>
            <span>Estado</span>
            <span style="text-align:center;">Detalles</span>
            <span>Acción</span>
        </div>
        ${filtrados.map(inc => `
        <div class="inc-row" id="inc-row-${inc.id_incidente}">
            <span style="font-weight:700;color:#1e3c72;">#${inc.id_incidente}</span>
            <div>
                <div style="font-weight:600;color:#1e293b;font-size:13px;">${inc.tipo_problema ?? '—'}</div>
                <div style="font-size:11px;color:#64748b;margin-top:2px;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${inc.descripcion ?? '—'}</div>
            </div>
            <span>
                <span class="inc-badge urgencia-${(urgenciaMap[inc.nivel_urgencia] || 'MEDIA')}">
                    ${inc.nivel_urgencia ?? 'MEDIA'}
                </span>
            </span>
            <span>
                <span class="inc-badge estado-${inc.estado ?? 'Reportado'}">
                    ${inc.estado ?? 'Reportado'}
                </span>
            </span>
            <span style="text-align:center;">
                <button style="background:none;border:none;color:#2563eb;cursor:pointer;font-size:13px;font-weight:600;text-decoration:underline;"
                    onclick="verDetalleIncidente(${inc.id_incidente})">
                    Ver detalle
                </button>
            </span>
            <span>
                ${inc.estado !== 'Resuelto'
                    ? `<button class="btn-resolver" onclick="resolverIncidente(${inc.id_incidente})" id="btn-res-${inc.id_incidente}">
                        <i class="fas fa-check"></i> Resolver
                       </button>`
                    : `<span style="font-size:12px;color:#16a34a;font-weight:600;">
                        <i class="fas fa-check-circle"></i> Resuelto
                       </span>`
                }
            </span>
        </div>`).join('')}
    `;
}

// ── Ver detalle del incidente ──────────────────────────────────────────────
function verDetalleIncidente(id) {
    const inc = cacheIncidentes.find(i => i.id_incidente === id);
    if (!inc) return;

    document.getElementById('inc-modal-titulo').textContent = inc.tipo_problema ?? 'Incidente';
    document.getElementById('inc-modal-folio').textContent  = `Folio: INC-${inc.id_incidente} · Reportado por usuario #${inc.id_usuario_reporta}`;

    const fecha = inc.fecha_reporte
        ? new Date(inc.fecha_reporte).toLocaleDateString('es-MX', {year:'numeric',month:'long',day:'numeric',hour:'2-digit',minute:'2-digit'})
        : '—';

    document.getElementById('inc-modal-body').innerHTML = `
        <div class="inc-detail-row">
            <span class="inc-detail-label">Tipo de Problema</span>
            <span class="inc-detail-value">${inc.tipo_problema ?? '—'}</span>
        </div>
        <div class="inc-detail-row">
            <span class="inc-detail-label">Descripción</span>
            <span class="inc-detail-value" style="text-align:left;margin-left:20px;">${inc.descripcion ?? '—'}</span>
        </div>
        <div class="inc-detail-row">
            <span class="inc-detail-label">Nivel de Urgencia</span>
            <span class="inc-detail-value">
                <span class="inc-badge urgencia-${urgenciaMap[inc.nivel_urgencia] || 'MEDIA'}">${inc.nivel_urgencia ?? 'MEDIA'}</span>
            </span>
        </div>
        <div class="inc-detail-row">
            <span class="inc-detail-label">Estado</span>
            <span class="inc-detail-value">
                <span class="inc-badge estado-${inc.estado ?? 'Reportado'}">${inc.estado ?? 'Reportado'}</span>
            </span>
        </div>
        <div class="inc-detail-row">
            <span class="inc-detail-label">Producto ID</span>
            <span class="inc-detail-value">${inc.id_producto ? '#' + inc.id_producto : 'No especificado'}</span>
        </div>
        <div class="inc-detail-row">
            <span class="inc-detail-label">Fecha de Reporte</span>
            <span class="inc-detail-value">${fecha}</span>
        </div>
        <div class="inc-detail-row">
            <span class="inc-detail-label">Reportado por</span>
            <span class="inc-detail-value">Usuario #${inc.id_usuario_reporta}</span>
        </div>
    `;

    const accion = document.getElementById('inc-modal-accion');
    if (inc.estado !== 'Resuelto') {
        accion.innerHTML = `
            <button class="btn-resolver-modal" id="btn-modal-resolver" onclick="resolverIncidente(${inc.id_incidente}, true)">
                <i class="fas fa-check-circle"></i> Marcar como Resuelto
            </button>`;
    } else {
        accion.innerHTML = `
            <div class="btn-ya-resuelto">
                <i class="fas fa-check-circle"></i> Ya está Resuelto
            </div>`;
    }

    document.getElementById('incModalOverlay').classList.add('open');
}

function cerrarModalInc() {
    document.getElementById('incModalOverlay').classList.remove('open');
}

document.getElementById('incModalOverlay').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalInc();
});

// ── Resolver incidente ─────────────────────────────────────────────────────
async function resolverIncidente(id, desdeModal = false) {
    const btn = desdeModal
        ? document.getElementById('btn-modal-resolver')
        : document.getElementById(`btn-res-${id}`);

    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Resolviendo...'; }

    try {
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '{{ csrf_token() }}';
        const r = await fetch(`/inventario/api/incidentes/${id}/resolver`, {
            method: 'PUT',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' }
        });
        if (!r.ok) throw new Error('HTTP ' + r.status);

        toast('Incidente #' + id + ' marcado como resuelto ✓');
        if (desdeModal) cerrarModalInc();
        cargarIncidentes();

    } catch(err) {
        toast('Error al resolver el incidente', 'error');
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-check-circle"></i> Marcar como Resuelto'; }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initFechas();
    cargarTodo();
    cargarIncidentes();
});
</script>
@endsection