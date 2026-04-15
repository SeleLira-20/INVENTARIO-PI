@extends('layouts.app')

@section('content')
<div class="container-fluid p-4" style="background-color: #f8fafc;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Dashboard</h2>
            <p class="text-muted mb-0">Resumen general del sistema de inventario</p>
        </div>
        <button class="btn btn-light btn-sm" onclick="cargarTodo()" title="Actualizar">
            <i class="fas fa-sync-alt" id="refresh-icon"></i> Actualizar
        </button>
    </div>

    {{-- Tarjetas de stats --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 12px;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="rounded-3 p-2" style="background: #ecfdf5; color: #10b981;">
                        <i class="fas fa-check-circle fa-lg"></i>
                    </div>
                    <span class="small fw-bold text-muted" id="badge-enstock">—</span>
                </div>
                <div class="mt-3">
                    <div class="text-muted small">Productos en Stock</div>
                    <div class="h3 fw-bold mb-0" id="stat-enstock">—</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 12px;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="rounded-3 p-2" style="background: #eff6ff; color: #3b82f6;">
                        <i class="fas fa-exchange-alt fa-lg"></i>
                    </div>
                    <span class="small fw-bold text-muted" id="badge-movimientos">—</span>
                </div>
                <div class="mt-3">
                    <div class="text-muted small">Total Movimientos</div>
                    <div class="h3 fw-bold mb-0" id="stat-movimientos">—</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 12px;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="rounded-3 p-2" style="background: #fffbeb; color: #f59e0b;">
                        <i class="fas fa-box fa-lg"></i>
                    </div>
                    <span class="small fw-bold text-muted" id="badge-total">—</span>
                </div>
                <div class="mt-3">
                    <div class="text-muted small">Total Productos</div>
                    <div class="h3 fw-bold mb-0" id="stat-total">—</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 12px;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="rounded-3 p-2" style="background: #f0fdf4; color: #16a34a;">
                        <i class="fas fa-dollar-sign fa-lg"></i>
                    </div>
                    <span class="small fw-bold text-muted" id="badge-valor">—</span>
                </div>
                <div class="mt-3">
                    <div class="text-muted small">Valor Total</div>
                    <div class="h3 fw-bold mb-0" id="stat-valor">—</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Alertas de stock bajo --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body">
            <h6 class="fw-bold mb-3">
                <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                Alertas y Notificaciones
            </h6>
            <div id="alertas-container">
                <div class="text-center text-muted py-3">
                    <div class="spinner-border spinner-border-sm me-2"></div> Cargando alertas...
                </div>
            </div>
        </div>
    </div>

    {{-- Gráficas --}}
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 15px;">
                <h6 class="fw-bold mb-4">Movimientos por Tipo (Entradas vs Salidas)</h6>
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
let chartMovimientos = null;
let chartCategoria   = null;

// ── Cargar todo ────────────────────────────────────────────────────────────
async function cargarTodo() {
    const icon = document.getElementById('refresh-icon');
    icon.classList.add('fa-spin');

    try {
        const [rProductos, rMovimientos, rAlertas] = await Promise.all([
            fetch('/inventario/api/productos'),
            fetch('/inventario/api/movimientos'),
            fetch('/inventario/api/alertas'),
        ]);

        const dProductos   = await rProductos.json();
        const dMovimientos = await rMovimientos.json();
        const dAlertas     = await rAlertas.json();

        const productos   = dProductos.productos   ?? [];
        const movimientos = dMovimientos.movimientos ?? [];
        const alertas     = dAlertas.productos      ?? [];

        actualizarStats(productos, movimientos);
        renderAlertas(alertas);
        renderChartMovimientos(movimientos);
        renderChartCategoria(productos);

    } catch (err) {
        console.error('Error cargando dashboard:', err);
    } finally {
        icon.classList.remove('fa-spin');
    }
}

// ── Stats ──────────────────────────────────────────────────────────────────
function actualizarStats(productos, movimientos) {
    const enStock = productos.filter(p => p.stock_actual > p.stock_minimo).length;
    const valor   = productos.reduce((a, p) => a + parseFloat(p.precio_unitario) * p.stock_actual, 0);
    const valorStr = valor >= 1000000
        ? '$' + (valor/1000000).toFixed(1) + 'M'
        : valor >= 1000 ? '$' + (valor/1000).toFixed(0) + 'K' : '$' + valor.toFixed(0);

    document.getElementById('stat-enstock').textContent    = enStock;
    document.getElementById('stat-movimientos').textContent = movimientos.length;
    document.getElementById('stat-total').textContent      = productos.length;
    document.getElementById('stat-valor').textContent      = valorStr;

    document.getElementById('badge-enstock').textContent    = `${enStock} de ${productos.length}`;
    document.getElementById('badge-movimientos').textContent = `${movimientos.filter(m=>m.tipo_movimiento==='ENTRADA').length} ent. / ${movimientos.filter(m=>m.tipo_movimiento==='SALIDA').length} sal.`;
    document.getElementById('badge-total').textContent      = `${productos.filter(p=>p.stock_actual===0).length} sin stock`;
    document.getElementById('badge-valor').textContent      = `${productos.length} productos`;
}

// ── Alertas ────────────────────────────────────────────────────────────────
function renderAlertas(alertas) {
    const cont = document.getElementById('alertas-container');

    if (!alertas.length) {
        cont.innerHTML = `
            <div class="alert border-0 d-flex align-items-center" style="background:#f0fdf4;">
                <i class="fas fa-check-circle text-success me-2"></i>
                <span>Todo en orden — no hay productos con stock bajo.</span>
            </div>`;
        return;
    }

    cont.innerHTML = alertas.map(p => {
        const esCritico = p.stock_actual === 0;
        const bg    = esCritico ? '#fef2f2' : '#fffbeb';
        const icon  = esCritico ? 'fa-times-circle text-danger' : 'fa-exclamation-circle text-warning';
        const badge = esCritico ? 'bg-danger' : 'bg-warning text-dark';
        const nivel = esCritico ? 'Crítico' : 'Stock Bajo';
        return `
        <div class="alert border-0 mb-2 d-flex justify-content-between align-items-center" style="background-color:${bg};">
            <span>
                <i class="fas ${icon} me-2"></i>
                ${esCritico ? 'Sin stock' : 'Stock bajo'}:
                <strong>${p.nombre}</strong>
                — ${p.stock_actual} unidades (mín. ${p.stock_minimo})
                <small class="text-muted ms-1">SKU: ${p.sku}</small>
            </span>
            <span class="badge ${badge}">${nivel}</span>
        </div>`;
    }).join('');
}

// ── Gráfica movimientos ────────────────────────────────────────────────────
function renderChartMovimientos(movimientos) {
    const entradas = movimientos.filter(m => m.tipo_movimiento === 'ENTRADA').length;
    const salidas  = movimientos.filter(m => m.tipo_movimiento === 'SALIDA').length;

    if (chartMovimientos) { chartMovimientos.destroy(); chartMovimientos = null; }

    chartMovimientos = new Chart(document.getElementById('movementsChart'), {
        type: 'bar',
        data: {
            labels: ['Entradas', 'Salidas'],
            datasets: [{
                label: 'Movimientos',
                data: [entradas, salidas],
                backgroundColor: ['#10b981', '#3b82f6'],
                borderRadius: 8,
                barThickness: 60,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { color: '#94a3b8' } },
                x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
            }
        }
    });
}

// ── Gráfica categorías ─────────────────────────────────────────────────────
function renderChartCategoria(productos) {
    const grupos = {};
    productos.forEach(p => {
        const cat = p.categoria ?? 'Otros';
        grupos[cat] = (grupos[cat] || 0) + p.stock_actual;
    });

    const labels = Object.keys(grupos);
    const datos  = Object.values(grupos);
    const colores = ['#2563eb','#10b981','#f59e0b','#8b5cf6','#ef4444','#06b6d4','#ec4899','#84cc16'];

    if (chartCategoria) { chartCategoria.destroy(); chartCategoria = null; }

    chartCategoria = new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data: datos,
                backgroundColor: colores.slice(0, labels.length),
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, font: { size: 11 } } }
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', cargarTodo);
</script>
@endsection