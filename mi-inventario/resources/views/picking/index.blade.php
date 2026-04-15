@extends('layouts.app')

@section('title', 'Gestión de Picking - Universal Inventory')

@section('extra-css')
<style>
    .order-status-badge { display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
    .status-Pendiente   { background: #e3f2fd; color: #1976d2; }
    .status-En-Proceso  { background: #fff3cd; color: #856404; }
    .status-Completada  { background: #d4edda; color: #155724; }
    .status-Cancelada   { background: #f8d7da; color: #721c24; }

    .tabs-container { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 25px; overflow: hidden; }
    .tabs { display: flex; border-bottom: 2px solid #e9ecef; }
    .tab { flex: 1; padding: 15px; text-align: center; cursor: pointer; font-weight: 600; color: #6c757d; border-bottom: 3px solid transparent; transition: all 0.3s; }
    .tab:hover { color: #1e3c72; background: #f8f9fa; }
    .tab.active { color: #1e3c72; border-bottom-color: #2a5298; }

    .order-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 15px; transition: all 0.2s; cursor: pointer; border: 2px solid transparent; }
    .order-card:hover { transform: translateY(-2px); border-color: #e9ecef; }
    .order-card.selected { border-color: #2a5298; background: #f8f9ff; }
    .order-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #e9ecef; }
    .order-id { font-size: 16px; font-weight: 700; color: #1e3c72; }
    .order-info { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 15px; margin-bottom: 15px; }
    .info-item { font-size: 12px; }
    .info-label { color: #6c757d; font-weight: 600; text-transform: uppercase; margin-bottom: 4px; }
    .info-value { color: #333; font-weight: 600; font-size: 13px; }

    .stat-badges { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 12px; margin-bottom: 20px; }
    .stat-badge { background: white; border-radius: 8px; padding: 12px; text-align: center; box-shadow: 0 1px 4px rgba(0,0,0,0.05); }
    .stat-badge-number { font-size: 24px; font-weight: 700; color: #1e3c72; }
    .stat-badge-label { font-size: 11px; color: #6c757d; margin-top: 5px; }

    .empty-state { text-align: center; padding: 60px 20px; color: #6c757d; background: white; border-radius: 12px; }

    /* Panel de detalles */
    .detail-panel { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); height: fit-content; }
    .detail-field { margin-bottom: 16px; }
    .detail-label { font-size: 11px; font-weight: 700; color: #6c757d; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
    .detail-value { font-size: 14px; font-weight: 600; color: #1e293b; }

    .btn-estado { border: none; border-radius: 8px; padding: 8px 16px; font-size: 12px; font-weight: 600; cursor: pointer; transition: .2s; margin-right: 6px; margin-bottom: 6px; }
    .btn-pendiente  { background: #e3f2fd; color: #1976d2; }
    .btn-en-proceso { background: #fff3cd; color: #856404; }
    .btn-completada { background: #d4edda; color: #155724; }
    .btn-cancelada  { background: #f8d7da; color: #721c24; }
    .btn-estado:hover { filter: brightness(.92); }

    .btn-nueva-orden { background: linear-gradient(135deg, #1e3c72, #2a5298); color: white; border: none; border-radius: 10px; padding: 10px 20px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; }
    .btn-nueva-orden:hover { opacity: .9; }

    .spinner-pick { width: 28px; height: 28px; border: 3px solid #e9ecef; border-top-color: #2a5298; border-radius: 50%; animation: spin .7s linear infinite; margin: 0 auto 10px; }
    @keyframes spin { to { transform: rotate(360deg); } }

    .toast-container { position: fixed; bottom: 24px; right: 24px; z-index: 5000; display: flex; flex-direction: column; gap: 8px; }
    .toast-msg { background: #1e293b; color: white; padding: 12px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; animation: slideIn .3s ease; }
    .toast-msg.ok  { border-left: 4px solid #22c55e; }
    .toast-msg.err { border-left: 4px solid #ef4444; }
    @keyframes slideIn { from{transform:translateX(120%);opacity:0} to{transform:translateX(0);opacity:1} }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 style="font-size:24px;font-weight:700;color:#1e3c72;margin:0;">Gestión de Picking</h2>
        <p style="color:#6c757d;font-size:13px;margin:4px 0 0;">Control de órdenes de recolección</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <button class="btn btn-light btn-sm" onclick="cargarOrdenes()" title="Actualizar">
            <i class="fas fa-sync-alt" id="refresh-icon"></i>
        </button>
        <button class="btn-nueva-orden" onclick="abrirModalNueva()">
            <i class="fas fa-plus"></i> Nueva Orden
        </button>
    </div>
</div>

{{-- Stats --}}
<div class="stat-badges">
    <div class="stat-badge">
        <div style="width:36px;height:36px;background:#cfe2ff;border-radius:8px;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;color:#0d6efd;">
            <i class="fas fa-list"></i>
        </div>
        <div class="stat-badge-number" id="stat-total">—</div>
        <div class="stat-badge-label">Total Órdenes</div>
    </div>
    <div class="stat-badge">
        <div style="width:36px;height:36px;background:#e3f2fd;border-radius:8px;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;color:#1976d2;">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-badge-number" id="stat-pendiente">—</div>
        <div class="stat-badge-label">Pendientes</div>
    </div>
    <div class="stat-badge">
        <div style="width:36px;height:36px;background:#fff3cd;border-radius:8px;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;color:#856404;">
            <i class="fas fa-hourglass-half"></i>
        </div>
        <div class="stat-badge-number" id="stat-proceso">—</div>
        <div class="stat-badge-label">En Proceso</div>
    </div>
    <div class="stat-badge">
        <div style="width:36px;height:36px;background:#d4edda;border-radius:8px;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;color:#155724;">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-badge-number" id="stat-completada">—</div>
        <div class="stat-badge-label">Completadas</div>
    </div>
</div>

{{-- Tabs --}}
<div class="tabs-container">
    <div class="tabs">
        <div class="tab active" data-filter="all"><i class="fas fa-list"></i> Todos</div>
        <div class="tab" data-filter="Pendiente"><i class="fas fa-clock"></i> Pendientes</div>
        <div class="tab" data-filter="En Proceso"><i class="fas fa-hourglass-half"></i> En Proceso</div>
        <div class="tab" data-filter="Completada"><i class="fas fa-check-circle"></i> Completadas</div>
    </div>
</div>

{{-- Grid principal --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:30px;">

    {{-- Lista de órdenes --}}
    <div>
        <h4 style="font-weight:700;color:#1e3c72;margin-bottom:20px;">Órdenes de Picking</h4>
        <div id="orders-list">
            <div class="empty-state">
                <div class="spinner-pick"></div>
                Cargando órdenes...
            </div>
        </div>
    </div>

    {{-- Panel de detalles --}}
    <div>
        <h4 style="font-weight:700;color:#1e3c72;margin-bottom:20px;">Detalles de la Orden</h4>
        <div id="detail-panel">
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fas fa-clipboard-list"></i></div>
                <div style="font-weight:600;">No hay orden seleccionada</div>
                <p style="font-size:12px;color:#999;margin-top:8px;">Selecciona una orden para ver los detalles</p>
            </div>
        </div>
    </div>
</div>

<div class="toast-container" id="toastContainer"></div>
@endsection

@section('extra-js')
<script>
const API_PICKING = '/inventario/api/picking';
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '{{ csrf_token() }}';
let ordenesCache = [];
let filtroActual = 'all';
let ordenSeleccionada = null;

// ── Toast ──────────────────────────────────────────────────────────────────
function toast(msg, tipo = 'ok') {
    const c = document.getElementById('toastContainer');
    const t = document.createElement('div');
    t.className = `toast-msg ${tipo}`;
    t.innerHTML = `<i class="fas fa-${tipo==='ok'?'check-circle':'times-circle'}"></i> ${msg}`;
    c.appendChild(t);
    setTimeout(() => t.remove(), 3500);
}

// ── Cargar órdenes ─────────────────────────────────────────────────────────
async function cargarOrdenes() {
    const icon = document.getElementById('refresh-icon');
    icon.classList.add('fa-spin');

    try {
        const resp = await fetch(API_PICKING);
        if (!resp.ok) throw new Error('HTTP ' + resp.status);
        const data = await resp.json();
        ordenesCache = data.ordenes ?? [];
        actualizarStats(ordenesCache);
        renderOrdenes(filtroActual);
    } catch (err) {
        document.getElementById('orders-list').innerHTML = `
            <div class="empty-state">
                <i class="fas fa-exclamation-circle fa-2x text-danger d-block mb-3"></i>
                No se pudo conectar con la API.<br>
                <small class="text-muted">Verifica que FastAPI esté corriendo.</small>
            </div>`;
        toast('Error al conectar con la API', 'err');
    } finally {
        icon.classList.remove('fa-spin');
    }
}

// ── Stats ──────────────────────────────────────────────────────────────────
function actualizarStats(ordenes) {
    document.getElementById('stat-total').textContent     = ordenes.length;
    document.getElementById('stat-pendiente').textContent = ordenes.filter(o => o.estado === 'Pendiente').length;
    document.getElementById('stat-proceso').textContent   = ordenes.filter(o => o.estado === 'En Proceso').length;
    document.getElementById('stat-completada').textContent= ordenes.filter(o => o.estado === 'Completada').length;
}

// ── Render lista ───────────────────────────────────────────────────────────
function renderOrdenes(filtro) {
    filtroActual = filtro;
    const lista = document.getElementById('orders-list');
    const filtradas = filtro === 'all' ? ordenesCache : ordenesCache.filter(o => o.estado === filtro);

    if (!filtradas.length) {
        lista.innerHTML = `<div class="empty-state">
            <div class="empty-state-icon"><i class="fas fa-clipboard-list"></i></div>
            <div>No hay órdenes ${filtro !== 'all' ? filtro.toLowerCase()+'s' : ''}</div>
        </div>`;
        return;
    }

    lista.innerHTML = filtradas.map(o => {
        const statusClass = 'status-' + o.estado.replace(' ', '-');
        const fecha = o.fecha_creacion ? new Date(o.fecha_creacion).toLocaleDateString('es-MX') : '—';
        const isSelected = ordenSeleccionada?.id_orden === o.id_orden;
        return `
        <div class="order-card ${isSelected ? 'selected' : ''}" onclick="seleccionarOrden(${o.id_orden})">
            <div class="order-header">
                <span class="order-id">${o.numero_orden}</span>
                <span class="order-status-badge ${statusClass}">${o.estado}</span>
            </div>
            <div class="order-info">
                <div class="info-item">
                    <div class="info-label">Usuario</div>
                    <div class="info-value">#${o.id_usuario_asignado}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Fecha</div>
                    <div class="info-value">${fecha}</div>
                </div>
            </div>
        </div>`;
    }).join('');
}

// ── Seleccionar orden ──────────────────────────────────────────────────────
async function seleccionarOrden(id) {
    try {
        const resp = await fetch(`${API_PICKING}/${id}`);
        if (!resp.ok) throw new Error('HTTP ' + resp.status);
        const data = await resp.json();
        ordenSeleccionada = data.orden;
        renderDetalle(data.orden, data.detalles ?? []);
        renderOrdenes(filtroActual);
    } catch (err) {
        toast('Error al cargar detalles', 'err');
    }
}

// ── Render detalle ─────────────────────────────────────────────────────────
function renderDetalle(orden, detalles) {
    const fecha = orden.fecha_creacion ? new Date(orden.fecha_creacion).toLocaleDateString('es-MX', {
        year:'numeric', month:'long', day:'numeric', hour:'2-digit', minute:'2-digit'
    }) : '—';

    const statusClass = 'status-' + orden.estado.replace(' ', '-');

    document.getElementById('detail-panel').innerHTML = `
        <div class="detail-panel">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h5 style="font-weight:800;color:#1e3c72;margin:0;">${orden.numero_orden}</h5>
                    <span class="order-status-badge ${statusClass} mt-2 d-inline-block">${orden.estado}</span>
                </div>
                <button class="btn btn-sm btn-light text-danger" onclick="eliminarOrden(${orden.id_orden}, '${orden.numero_orden}')" title="Eliminar orden">
                    <i class="fas fa-trash"></i>
                </button>
            </div>

            <div class="row mb-4">
                <div class="col-6">
                    <div class="detail-field">
                        <div class="detail-label">ID Orden</div>
                        <div class="detail-value">#${orden.id_orden}</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="detail-field">
                        <div class="detail-label">Usuario Asignado</div>
                        <div class="detail-value">#${orden.id_usuario_asignado}</div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="detail-field">
                        <div class="detail-label">Fecha de Creación</div>
                        <div class="detail-value">${fecha}</div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <div class="detail-label mb-2">Cambiar Estado</div>
                <button class="btn-estado btn-pendiente"  onclick="cambiarEstado(${orden.id_orden}, 'Pendiente')"><i class="fas fa-clock me-1"></i>Pendiente</button>
                <button class="btn-estado btn-en-proceso" onclick="cambiarEstado(${orden.id_orden}, 'En Proceso')"><i class="fas fa-hourglass-half me-1"></i>En Proceso</button>
                <button class="btn-estado btn-completada" onclick="cambiarEstado(${orden.id_orden}, 'Completada')"><i class="fas fa-check me-1"></i>Completada</button>
                <button class="btn-estado btn-cancelada"  onclick="cambiarEstado(${orden.id_orden}, 'Cancelada')"><i class="fas fa-times me-1"></i>Cancelada</button>
            </div>

            <div>
                <div class="detail-label mb-2">Detalles de la Orden</div>
                ${detalles.length ? `
                <div class="table-responsive">
                    <table class="table table-sm table-borderless">
                        <thead class="bg-light">
                            <tr>
                                <th style="font-size:11px;">Producto ID</th>
                                <th style="font-size:11px;">Requerido</th>
                                <th style="font-size:11px;">Recolectado</th>
                                <th style="font-size:11px;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${detalles.map(d => `
                            <tr>
                                <td>#${d.id_producto}</td>
                                <td>${d.cantidad_requerida}</td>
                                <td>${d.cantidad_recolectada}</td>
                                <td><span class="order-status-badge status-${d.estado?.replace(' ','-')}" style="font-size:10px;padding:3px 8px;">${d.estado}</span></td>
                            </tr>`).join('')}
                        </tbody>
                    </table>
                </div>` : '<p class="text-muted small">Sin detalles registrados para esta orden.</p>'}
            </div>
        </div>`;
}

// ── Cambiar estado ─────────────────────────────────────────────────────────
async function cambiarEstado(id, nuevoEstado) {
    try {
        const resp = await fetch(`${API_PICKING}/${id}/estado`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ estado: nuevoEstado }),
        });
        const data = await resp.json();
        if (!resp.ok) throw new Error(data.mensaje ?? 'Error');
        toast(`Orden actualizada a "${nuevoEstado}"`);
        await cargarOrdenes();
        await seleccionarOrden(id);
    } catch (err) {
        toast('Error al actualizar estado', 'err');
    }
}

// ── Eliminar orden ─────────────────────────────────────────────────────────
async function eliminarOrden(id, numero) {
    if (!confirm(`¿Eliminar la orden ${numero}? Esta acción no se puede deshacer.`)) return;

    try {
        const resp = await fetch(`${API_PICKING}/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF },
        });
        if (!resp.ok) throw new Error('HTTP ' + resp.status);
        toast(`Orden "${numero}" eliminada`);
        ordenSeleccionada = null;
        document.getElementById('detail-panel').innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fas fa-clipboard-list"></i></div>
                <div style="font-weight:600;">No hay orden seleccionada</div>
            </div>`;
        cargarOrdenes();
    } catch (err) {
        toast('Error al eliminar la orden', 'err');
    }
}

// ── Nueva orden ────────────────────────────────────────────────────────────
function abrirModalNueva() {
    const ahora = new Date();
    const num = `PCK-${ahora.getFullYear()}-${String(ahora.getMonth()+1).padStart(2,'0')}${String(ahora.getDate()).padStart(2,'0')}-${String(ahora.getTime()).slice(-4)}`;

    const html = `
        <div class="text-start">
            <label class="small fw-bold mb-1">Número de Orden</label>
            <input id="m-numero" class="form-control mb-3" value="${num}">
            <label class="small fw-bold mb-1">ID Usuario Asignado</label>
            <input id="m-usuario" class="form-control mb-3" type="number" placeholder="1" min="1">
            <label class="small fw-bold mb-1">Estado Inicial</label>
            <select id="m-estado" class="form-select">
                <option value="Pendiente">Pendiente</option>
                <option value="En Proceso">En Proceso</option>
            </select>
        </div>`;

    // Usar SweetAlert2 si está disponible, sino prompt básico
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Nueva Orden de Picking',
            html,
            showCancelButton: true,
            confirmButtonText: 'Crear Orden',
            confirmButtonColor: '#1e3c72',
            cancelButtonText: 'Cancelar',
            preConfirm: async () => {
                const numero  = document.getElementById('m-numero').value.trim();
                const usuario = parseInt(document.getElementById('m-usuario').value);
                const estado  = document.getElementById('m-estado').value;
                if (!numero || !usuario) { Swal.showValidationMessage('Completa todos los campos'); return false; }
                try {
                    const resp = await fetch(API_PICKING, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                        body: JSON.stringify({ numero_orden: numero, id_usuario_asignado: usuario, estado }),
                    });
                    const data = await resp.json();
                    if (!resp.ok || data.status === '400') throw new Error(data.mensaje ?? 'Error');
                    return data;
                } catch (err) { Swal.showValidationMessage('Error: ' + err.message); return false; }
            }
        }).then(result => {
            if (result.isConfirmed) { toast('Orden creada correctamente'); cargarOrdenes(); }
        });
    }
}

// ── Tabs ───────────────────────────────────────────────────────────────────
document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        renderOrdenes(tab.dataset.filter);
    });
});

document.addEventListener('DOMContentLoaded', cargarOrdenes);
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection