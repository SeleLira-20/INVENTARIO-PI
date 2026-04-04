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
            <a href="#" class="template-card" onclick="exportarInventarioCSV(); return false;">
                <div class="icon-box"><i class="far fa-file-alt"></i></div>
                <div class="template-content">
                    <h6>Reporte de Inventario Completo</h6>
                    <p>Estado actual de todo el inventario con categorías y valores</p>
                    <div class="format-info"><span class="badge-format">CSV</span></div>
                </div>
                <div class="btn-download-mini"><i class="fas fa-download"></i></div>
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

<div class="toast-container" id="toastContainer"></div>
@endsection

@section('extra-js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ── Config ────────────────────────────────────────────────────────────────
const API_PRODUCTOS  = '/inventario/api/productos';
const API_ALERTAS    = '/inventario/api/alertas';
const API_MOV        = 'http://localhost:8000/v1/movimientos/';

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

    // Agrupar por tipo
    const entradas = movimientos.filter(m => m.tipo_movimiento === 'ENTRADA').length;
    const salidas  = movimientos.filter(m => m.tipo_movimiento === 'SALIDA').length;

    if (chartMov) chartMov.destroy();

    // Si no hay movimientos mostrar mensaje
    if (!movimientos.length) {
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = '#a0aec0';
        ctx.font = '14px Inter';
        ctx.textAlign = 'center';
        ctx.fillText('Sin movimientos registrados', canvas.width/2, 80);
        return;
    }

    chartMov = new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: ['Entradas', 'Salidas'],
            datasets: [{
                data: [entradas, salidas],
                backgroundColor: ['#48bb78', '#f56565'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            cutout: '70%',
            plugins: {
                legend: { position: 'bottom' },
                tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.raw} movimientos` } }
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
document.addEventListener('DOMContentLoaded', () => {
    initFechas();
    cargarTodo();
});
</script>
@endsection