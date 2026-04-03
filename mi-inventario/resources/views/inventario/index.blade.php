@extends('layouts.app')

@section('title', 'Inventario - Universal Inventory')

@section('extra-css')
<style>
    .inv-page { background: #f8fafc; padding: 28px; font-family: 'Plus Jakarta Sans', sans-serif; }

    /* ── Header ── */
    .inv-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .inv-header h2 { font-size: 22px; font-weight: 700; color: #1e293b; margin: 0; }
    .inv-header p  { color: #64748b; font-size: 13px; margin: 4px 0 0; }
    .btn-nuevo {
        background: #2563eb; color: white; border: none; border-radius: 10px;
        padding: 10px 20px; font-weight: 600; font-size: 14px;
        cursor: pointer; display: flex; align-items: center; gap: 8px; transition: background .2s;
    }
    .btn-nuevo:hover { background: #1d4ed8; }

    /* ── Stat cards ── */
    .stat-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 14px; margin-bottom: 22px; }
    .stat-card { background: white; border-radius: 12px; padding: 16px 18px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 12px; }
    .stat-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
    .stat-label { font-size: 11px; color: #64748b; font-weight: 600; }
    .stat-value { font-size: 22px; font-weight: 800; color: #1e293b; line-height: 1.1; }

    /* ── Toolbar ── */
    .toolbar { background: white; border-radius: 12px; padding: 14px 18px; border: 1px solid #e2e8f0; margin-bottom: 16px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
    .search-box { flex: 1; min-width: 200px; position: relative; }
    .search-box input { width: 100%; padding: 9px 12px 9px 34px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px; outline: none; box-sizing: border-box; }
    .search-box input:focus { border-color: #2563eb; }
    .search-box i { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px; }
    .cat-tabs { display: flex; gap: 6px; flex-wrap: wrap; }
    .cat-tab { padding: 7px 16px; border-radius: 8px; border: 1px solid #e2e8f0; background: white; font-size: 13px; font-weight: 600; color: #64748b; cursor: pointer; transition: .2s; }
    .cat-tab.active { background: #2563eb; color: white; border-color: #2563eb; }
    .cat-tab:hover:not(.active) { background: #f1f5f9; }
    .btn-toolbar { background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 14px; cursor: pointer; color: #475569; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 6px; transition: .2s; white-space: nowrap; }
    .btn-toolbar:hover { background: #e2e8f0; }
    .btn-export { color: #16a34a; border-color: #bbf7d0; background: #f0fdf4; }
    .btn-export:hover { background: #dcfce7; }

    /* ── Table ── */
    .table-card { background: white; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; }
    .table-responsive { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    thead th { background: #f8fafc; padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: .5px; border-bottom: 1px solid #e2e8f0; white-space: nowrap; }
    tbody td { padding: 13px 16px; border-bottom: 1px solid #f1f5f9; color: #334155; vertical-align: middle; }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover { background: #f8fafc; }
    .sku-link { color: #2563eb; font-weight: 600; font-size: 12px; text-decoration: none; }
    .sku-link:hover { text-decoration: underline; }

    /* ── Badges ── */
    .badge-stock { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .stock-en   { background: #dcfce7; color: #166534; }
    .stock-bajo { background: #fef9c3; color: #854d0e; }
    .stock-sin  { background: #fee2e2; color: #991b1b; }

    /* ── Acciones ── */
    .btn-accion { background: none; border: none; cursor: pointer; padding: 5px 7px; border-radius: 6px; font-size: 14px; transition: .2s; }
    .btn-accion:hover { background: #f1f5f9; }
    .btn-edit { color: #2563eb; }
    .btn-del  { color: #dc2626; }

    /* ── Modal base ── */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(15,23,42,.5); z-index: 2000; align-items: center; justify-content: center; }
    .modal-overlay.open { display: flex; }
    .modal-box { background: white; border-radius: 16px; padding: 32px; width: 100%; max-width: 520px; box-shadow: 0 24px 64px rgba(0,0,0,.2); position: relative; max-height: 90vh; overflow-y: auto; animation: popIn .2s ease; }
    @keyframes popIn { from { transform: scale(.95); opacity:0; } to { transform: scale(1); opacity:1; } }
    .modal-title    { font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 4px; }
    .modal-subtitle { font-size: 13px; color: #64748b; margin-bottom: 22px; }
    .modal-close { position: absolute; top: 18px; right: 18px; background: #f1f5f9; border: none; width: 30px; height: 30px; border-radius: 8px; font-size: 14px; cursor: pointer; color: #64748b; display: flex; align-items: center; justify-content: center; }
    .modal-close:hover { background: #e2e8f0; }
    .modal-footer { display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; }

    /* ── Form ── */
    .form-row2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
    .form-group { margin-bottom: 14px; }
    .form-group label { display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px; text-transform: uppercase; letter-spacing: .3px; }
    .form-group input, .form-group select { width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px; outline: none; box-sizing: border-box; transition: border-color .2s; }
    .form-group input:focus, .form-group select:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.08); }

    /* ── Botones modal ── */
    .btn-cancel { background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 20px; font-weight: 600; cursor: pointer; font-size: 13px; color: #475569; }
    .btn-cancel:hover { background: #e2e8f0; }
    .btn-save   { background: #2563eb; color: white; border: none; border-radius: 8px; padding: 10px 24px; font-weight: 600; cursor: pointer; font-size: 13px; display: flex; align-items: center; gap: 6px; }
    .btn-save:hover { background: #1d4ed8; }
    .btn-save:disabled { opacity: .6; cursor: not-allowed; }
    .btn-danger { background: #dc2626; color: white; border: none; border-radius: 8px; padding: 10px 24px; font-weight: 600; cursor: pointer; font-size: 13px; display: flex; align-items: center; gap: 6px; }
    .btn-danger:hover { background: #b91c1c; }
    .btn-danger:disabled { opacity: .6; cursor: not-allowed; }

    /* ── Modal eliminar ── */
    .confirm-icon { width: 56px; height: 56px; background: #fee2e2; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: #dc2626; margin-bottom: 16px; }
    .confirm-product { background: #f8fafc; border-radius: 8px; padding: 10px 14px; font-weight: 600; color: #1e293b; font-size: 14px; border: 1px solid #e2e8f0; margin-bottom: 8px; }

    /* ── Loading / Empty ── */
    .loading-row td, .empty-row td { text-align: center; padding: 50px; color: #94a3b8; }
    .spinner { width: 32px; height: 32px; border: 3px solid #e2e8f0; border-top-color: #2563eb; border-radius: 50%; animation: spin .7s linear infinite; margin: 0 auto 10px; }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ── Toast ── */
    .toast-container { position: fixed; bottom: 24px; right: 24px; z-index: 5000; display: flex; flex-direction: column; gap: 8px; }
    .toast { background: #1e293b; color: white; padding: 12px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; animation: slideIn .3s ease; box-shadow: 0 4px 12px rgba(0,0,0,.2); }
    .toast.success { border-left: 4px solid #22c55e; }
    .toast.error   { border-left: 4px solid #ef4444; }
    @keyframes slideIn { from { transform: translateX(120%); opacity:0; } to { transform: translateX(0); opacity:1; } }

    @media (max-width: 900px) { .stat-grid { grid-template-columns: repeat(3,1fr); } }
    @media (max-width: 600px) { .stat-grid { grid-template-columns: 1fr 1fr; } .form-row2 { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
<div class="inv-page">

    {{-- Header --}}
    <div class="inv-header">
        <div>
            <h2>Gestión de Inventario</h2>
            <p>Control y administración de productos</p>
        </div>
        <button class="btn-nuevo" onclick="abrirModalCrear()">
            <i class="fas fa-plus"></i> Agregar Producto
        </button>
    </div>

    {{-- Stats --}}
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eff6ff;color:#2563eb;"><i class="fas fa-boxes"></i></div>
            <div><div class="stat-label">Total Items</div><div class="stat-value" id="stat-total">—</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#f0fdf4;color:#16a34a;"><i class="fas fa-check-circle"></i></div>
            <div><div class="stat-label">En Stock</div><div class="stat-value" id="stat-enstock">—</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fefce8;color:#ca8a04;"><i class="fas fa-exclamation-triangle"></i></div>
            <div><div class="stat-label">Stock Bajo</div><div class="stat-value" id="stat-bajo">—</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef2f2;color:#dc2626;"><i class="fas fa-times-circle"></i></div>
            <div><div class="stat-label">Sin Stock</div><div class="stat-value" id="stat-sinstock">—</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#f0fdf4;color:#16a34a;"><i class="fas fa-chart-line"></i></div>
            <div><div class="stat-label">Valor Total</div><div class="stat-value" id="stat-valor">—</div></div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="toolbar">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Buscar por nombre o SKU..." oninput="filtrarTabla()">
        </div>
        <div class="cat-tabs">
            <button class="cat-tab active" data-cat="" onclick="setCat(this)">Todos</button>
            <button class="cat-tab" data-cat="Electrónicos" onclick="setCat(this)">Electrónicos</button>
            <button class="cat-tab" data-cat="Mobiliario" onclick="setCat(this)">Mobiliario</button>
            <button class="cat-tab" data-cat="Papelería" onclick="setCat(this)">Papelería</button>
        </div>
        <button class="btn-toolbar" onclick="cargarProductos()">
            <i class="fas fa-sync-alt" id="refresh-icon"></i> Actualizar
        </button>
        <button class="btn-toolbar btn-export" onclick="exportarCSV()">
            <i class="fas fa-download"></i> Exportar
        </button>
    </div>

    {{-- Tabla --}}
    <div class="table-card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>SKU</th>
                        <th>Categoría</th>
                        <th>Stock</th>
                        <th>Valor Unit.</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaBody">
                    <tr class="loading-row">
                        <td colspan="7"><div class="spinner"></div>Conectando con la API...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ══ MODAL CREAR / EDITAR ══ --}}
<div class="modal-overlay" id="modalFormOverlay">
    <div class="modal-box">
        <button class="modal-close" onclick="cerrarModalForm()"><i class="fas fa-times"></i></button>
        <div class="modal-title" id="modalFormTitulo">Nuevo Producto</div>
        <div class="modal-subtitle" id="modalFormSubtitulo">Completa los datos del producto</div>
        <input type="hidden" id="editId">
        <div class="form-group">
            <label>SKU *</label>
            <input type="text" id="fSku" placeholder="Ej. LPT-001">
        </div>
        <div class="form-group">
            <label>Nombre del Producto *</label>
            <input type="text" id="fNombre" placeholder="Ej. Laptop Dell XPS 15">
        </div>
        <div class="form-row2">
            <div class="form-group">
                <label>Stock Actual *</label>
                <input type="number" id="fStockActual" min="0" placeholder="50">
            </div>
            <div class="form-group">
                <label>Stock Mínimo *</label>
                <input type="number" id="fStockMinimo" min="0" placeholder="10">
            </div>
        </div>
        <div class="form-row2">
            <div class="form-group">
                <label>Precio Unitario *</label>
                <input type="number" id="fPrecio" min="0" step="0.01" placeholder="1299.99">
            </div>
            <div class="form-group">
                <label>Categoría</label>
                <select id="fCategoria">
                    <option value="Electrónicos">Electrónicos</option>
                    <option value="Mobiliario">Mobiliario</option>
                    <option value="Papelería">Papelería</option>
                    <option value="Equipamiento">Equipamiento</option>
                    <option value="Otros">Otros</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="cerrarModalForm()">Cancelar</button>
            <button class="btn-save" id="btnGuardar" onclick="guardarProducto()">
                <i class="fas fa-save"></i> <span id="btnGuardarTexto">Guardar Producto</span>
            </button>
        </div>
    </div>
</div>

{{-- ══ MODAL CONFIRMAR ELIMINAR ══ --}}
<div class="modal-overlay" id="modalEliminarOverlay">
    <div class="modal-box" style="max-width:420px;">
        <button class="modal-close" onclick="cerrarModalEliminar()"><i class="fas fa-times"></i></button>
        <div class="confirm-icon"><i class="fas fa-trash-alt"></i></div>
        <div class="modal-title">Eliminar Producto</div>
        <div class="modal-subtitle">Esta acción no se puede deshacer.</div>
        <div class="confirm-product" id="confirmNombre">—</div>
        <p style="font-size:13px;color:#64748b;margin-top:8px;">
            ¿Estás seguro de que deseas eliminar este producto del inventario?
        </p>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="cerrarModalEliminar()">Cancelar</button>
            <button class="btn-danger" id="btnConfirmarEliminar">
                <i class="fas fa-trash-alt"></i> Eliminar
            </button>
        </div>
    </div>
</div>

<div class="toast-container" id="toastContainer"></div>
@endsection

@section('extra-js')
<script>
const API_BASE = '/inventario/api/productos';
let productosCache = [];
let categoriaActual = '';

// ── Toast ─────────────────────────────────────────────────────────────────
function toast(msg, tipo = 'success') {
    const c = document.getElementById('toastContainer');
    const t = document.createElement('div');
    t.className = `toast ${tipo}`;
    t.innerHTML = `<i class="fas fa-${tipo === 'success' ? 'check-circle' : 'times-circle'}"></i> ${msg}`;
    c.appendChild(t);
    setTimeout(() => t.remove(), 3500);
}

// ── Helpers ───────────────────────────────────────────────────────────────
function badgeStock(actual, minimo) {
    if (actual === 0)     return `<span class="badge-stock stock-sin">Sin Stock</span>`;
    if (actual <= minimo) return `<span class="badge-stock stock-bajo">Stock Bajo</span>`;
    return `<span class="badge-stock stock-en">En Stock</span>`;
}

function formatPrecio(val) {
    const n = parseFloat(val);
    return '$' + (n >= 1000 ? n.toLocaleString('es-MX', {maximumFractionDigits:0}) : n.toFixed(2));
}

// ── Cargar productos ──────────────────────────────────────────────────────
async function cargarProductos() {
    const icon = document.getElementById('refresh-icon');
    icon.classList.add('fa-spin');
    try {
        const resp = await fetch(API_BASE);
        if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
        const data = await resp.json();
        productosCache = data.productos ?? [];
        actualizarStats(productosCache);
        filtrarTabla();
    } catch (err) {
        document.getElementById('tablaBody').innerHTML = `
            <tr class="empty-row"><td colspan="7">
                <i class="fas fa-exclamation-circle" style="font-size:28px;color:#ef4444;display:block;margin-bottom:8px;"></i>
                No se pudo conectar con la API.<br>
                <small style="color:#94a3b8;">Verifica que FastAPI esté corriendo.</small>
            </td></tr>`;
        toast('Error al conectar con la API', 'error');
    } finally {
        icon.classList.remove('fa-spin');
    }
}

// ── Render ────────────────────────────────────────────────────────────────
function renderTabla(productos) {
    const tbody = document.getElementById('tablaBody');
    if (!productos.length) {
        tbody.innerHTML = `<tr class="empty-row"><td colspan="7">
            <i class="fas fa-inbox" style="font-size:32px;display:block;margin-bottom:8px;"></i>
            No se encontraron productos</td></tr>`;
        return;
    }
    tbody.innerHTML = productos.map(p => `
        <tr data-id="${p.id_producto}">
            <td><strong>${p.nombre}</strong></td>
            <td><a href="#" class="sku-link">${p.sku}</a></td>
            <td style="color:#64748b;">${p.categoria ?? '—'}</td>
            <td>${badgeStock(p.stock_actual, p.stock_minimo)}</td>
            <td style="font-weight:600;">${formatPrecio(p.precio_unitario)}</td>
            <td><span style="font-size:12px;color:#16a34a;font-weight:700;">${p.estado ?? 'Activo'}</span></td>
            <td>
                <button class="btn-accion btn-edit" onclick="abrirModalEditar(${p.id_producto})" title="Editar"><i class="fas fa-pen"></i></button>
                <button class="btn-accion btn-del" onclick="pedirConfirmacionEliminar(${p.id_producto}, '${p.nombre.replace(/'/g,"\\'")}', '${p.sku}')" title="Eliminar"><i class="fas fa-trash"></i></button>
            </td>
        </tr>`).join('');
}

// ── Stats ─────────────────────────────────────────────────────────────────
function actualizarStats(productos) {
    document.getElementById('stat-total').textContent    = productos.length;
    document.getElementById('stat-enstock').textContent  = productos.filter(p => p.stock_actual > p.stock_minimo).length;
    document.getElementById('stat-bajo').textContent     = productos.filter(p => p.stock_actual > 0 && p.stock_actual <= p.stock_minimo).length;
    document.getElementById('stat-sinstock').textContent = productos.filter(p => p.stock_actual === 0).length;
    const valor = productos.reduce((a, p) => a + parseFloat(p.precio_unitario) * p.stock_actual, 0);
    document.getElementById('stat-valor').textContent = valor >= 1000 ? '$' + (valor/1000).toFixed(0) + 'K' : '$' + valor.toFixed(0);
}

// ── Filtros ───────────────────────────────────────────────────────────────
function setCat(btn) {
    document.querySelectorAll('.cat-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    categoriaActual = btn.dataset.cat;
    filtrarTabla();
}

function filtrarTabla() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    const filtrados = productosCache.filter(p =>
        (p.nombre?.toLowerCase().includes(q) || p.sku?.toLowerCase().includes(q)) &&
        (!categoriaActual || (p.categoria ?? '') === categoriaActual)
    );
    renderTabla(filtrados);
}

// ── Modal Form ────────────────────────────────────────────────────────────
function abrirModalCrear() {
    document.getElementById('modalFormTitulo').textContent    = 'Nuevo Producto';
    document.getElementById('modalFormSubtitulo').textContent = 'Completa los datos del producto';
    document.getElementById('btnGuardarTexto').textContent    = 'Guardar Producto';
    document.getElementById('editId').value = '';
    ['fSku','fNombre','fStockActual','fStockMinimo','fPrecio'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('fCategoria').value = 'Electrónicos';
    document.getElementById('modalFormOverlay').classList.add('open');
    setTimeout(() => document.getElementById('fSku').focus(), 100);
}

function abrirModalEditar(id) {
    const p = productosCache.find(x => x.id_producto === id);
    if (!p) return;
    document.getElementById('modalFormTitulo').textContent    = 'Editar Producto';
    document.getElementById('modalFormSubtitulo').textContent = `Modificando: ${p.nombre}`;
    document.getElementById('btnGuardarTexto').textContent    = 'Guardar Cambios';
    document.getElementById('editId').value       = p.id_producto;
    document.getElementById('fSku').value         = p.sku;
    document.getElementById('fNombre').value      = p.nombre;
    document.getElementById('fStockActual').value = p.stock_actual;
    document.getElementById('fStockMinimo').value = p.stock_minimo;
    document.getElementById('fPrecio').value      = p.precio_unitario;
    document.getElementById('fCategoria').value   = p.categoria ?? 'Electrónicos';
    document.getElementById('modalFormOverlay').classList.add('open');
}

function cerrarModalForm() {
    document.getElementById('modalFormOverlay').classList.remove('open');
}

// ── Guardar ───────────────────────────────────────────────────────────────
async function guardarProducto() {
    const id          = document.getElementById('editId').value;
    const sku         = document.getElementById('fSku').value.trim();
    const nombre      = document.getElementById('fNombre').value.trim();
    const categoria   = document.getElementById('fCategoria').value;
    const stockActual = document.getElementById('fStockActual').value;
    const stockMinimo = document.getElementById('fStockMinimo').value;
    const precio      = document.getElementById('fPrecio').value;

    if (!sku || !nombre || stockActual === '' || stockMinimo === '' || !precio) {
        toast('Completa todos los campos obligatorios', 'error'); return;
    }

    const payload = { sku, nombre, categoria, stock_actual: parseInt(stockActual), stock_minimo: parseInt(stockMinimo), precio_unitario: parseFloat(precio) };
    const esEdicion = !!id;
    const url    = esEdicion ? `${API_BASE}/${id}` : API_BASE;
    const method = esEdicion ? 'PUT' : 'POST';

    const btn = document.getElementById('btnGuardar');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

    try {
        const resp = await fetch(url, { method, headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
        const data = await resp.json();
        if (!resp.ok) throw new Error(data.detail ?? `HTTP ${resp.status}`);
        toast(esEdicion ? 'Producto actualizado correctamente' : 'Producto creado correctamente');
        cerrarModalForm();
        cargarProductos();
    } catch (err) {
        toast('Error: ' + err.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = `<i class="fas fa-save"></i> <span id="btnGuardarTexto">${esEdicion ? 'Guardar Cambios' : 'Guardar Producto'}</span>`;
    }
}

// ── Modal Eliminar ────────────────────────────────────────────────────────
let _deleteId = null;

function pedirConfirmacionEliminar(id, nombre, sku) {
    _deleteId = id;
    document.getElementById('confirmNombre').textContent = `${nombre}  (${sku})`;
    document.getElementById('modalEliminarOverlay').classList.add('open');
}

function cerrarModalEliminar() {
    _deleteId = null;
    document.getElementById('modalEliminarOverlay').classList.remove('open');
}

document.getElementById('btnConfirmarEliminar').addEventListener('click', async () => {
    if (!_deleteId) return;
    const btn = document.getElementById('btnConfirmarEliminar');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Eliminando...';
    try {
        const resp = await fetch(`${API_BASE}/${_deleteId}`, { method: 'DELETE' });
        if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
        toast('Producto eliminado correctamente');
        cerrarModalEliminar();
        cargarProductos();
    } catch (err) {
        toast('Error al eliminar el producto', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-trash-alt"></i> Eliminar';
    }
});

// Cerrar al click fuera
document.getElementById('modalFormOverlay').addEventListener('click', function(e) { if (e.target===this) cerrarModalForm(); });
document.getElementById('modalEliminarOverlay').addEventListener('click', function(e) { if (e.target===this) cerrarModalEliminar(); });

// ── Exportar CSV ──────────────────────────────────────────────────────────
function exportarCSV() {
    if (!productosCache.length) { toast('No hay productos para exportar', 'error'); return; }
    const cols   = ['id_producto','sku','nombre','stock_actual','stock_minimo','precio_unitario','estado'];
    const header = ['ID','SKU','Nombre','Stock Actual','Stock Mínimo','Precio','Estado'];
    const rows   = productosCache.map(p => cols.map(c => `"${p[c] ?? ''}"`).join(','));
    const csv    = [header.join(','), ...rows].join('\n');
    const a = document.createElement('a');
    a.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
    a.download = `inventario_${new Date().toISOString().slice(0,10)}.csv`;
    a.click();
    toast('CSV exportado correctamente');
}

document.addEventListener('DOMContentLoaded', cargarProductos);
</script>
@endsection