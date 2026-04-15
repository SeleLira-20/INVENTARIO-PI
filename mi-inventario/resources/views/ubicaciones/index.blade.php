@extends('layouts.app')

@section('title', 'Ubicaciones - Universal Inventory')

@section('extra-css')
<style>
    :root {
        --ui-primary: #0061f2;
        --ui-bg: #f8f9fc;
        --ui-card-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,0.1);
        --ui-text-main: #2d3748;
        --ui-text-muted: #858796;
        --ui-warning: #f6993f;
        --ui-danger: #e74a3b;
        --ui-success: #38b2ac;
    }
    body { background-color: var(--ui-bg); font-family: 'Nunito', sans-serif; color: var(--ui-text-main); }
    .inventory-wrapper { padding: 1.5rem; }
    .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
    .btn-ui-primary { background-color: var(--ui-primary); color: white; border-radius: 8px; padding: 10px 20px; font-weight: 700; border: none; font-size: 0.85rem; transition: all 0.3s; cursor: pointer; }
    .btn-ui-primary:hover { background-color: #0052cc; }

    .stats-row { display: grid; grid-template-columns: repeat(4,1fr); gap: 1.5rem; margin-bottom: 1.5rem; }
    .stat-card-ui { background: white; border-radius: 12px; padding: 1.2rem; box-shadow: var(--ui-card-shadow); display: flex; align-items: center; gap: 1rem; }
    .icon-box { width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }

    .alert-capacity { background-color: #fffdf0; border: 1px solid #ffeeba; border-radius: 12px; padding: 1rem 1.5rem; margin-bottom: 2rem; display: flex; gap: 1rem; align-items: center; }

    .main-card-ui { background: white; border-radius: 15px; padding: 1.5rem; box-shadow: var(--ui-card-shadow); }

    .location-item { background: #f8faff; border: 1px solid #edf2f7; border-radius: 12px; margin-bottom: 0.8rem; padding: 0.8rem 1.2rem; display: flex; align-items: center; transition: all 0.3s cubic-bezier(0.4,0,0.2,1); }
    .location-item:hover { background: white; border-color: var(--ui-primary); }
    .level-1 { margin-left: 2.5rem; }
    .level-2 { margin-left: 5rem; background: #fff; }

    .progress-container-ui { flex: 0 0 280px; margin: 0 1.5rem; display: flex; align-items: center; gap: 12px; }
    .progress-ui { flex: 1; height: 8px; background: #eaecf4; border-radius: 20px; overflow: hidden; }
    .progress-bar-ui { height: 100%; border-radius: 20px; }

    .loc-info-text { font-size: 0.75rem; color: #a0aec0; }
    .loc-code { font-size: 0.65rem; font-weight: 700; background: #f1f3f9; padding: 2px 6px; border-radius: 4px; margin-left: 5px; }
    .text-percentage { font-weight: 800; font-size: 0.75rem; color: #4a5568; width: 35px; }
    .action-btns { display: flex; gap: 15px; color: #b7b9cc; }
    .action-btns i { cursor: pointer; transition: 0.2s; }
    .action-btns i:hover { color: var(--ui-primary); transform: scale(1.1); }
    .action-btns i.fa-trash:hover { color: var(--ui-danger); }
    .chevron-toggle { width: 20px; cursor: pointer; color: #a0aec0; transition: transform 0.3s; }
    .collapsed-icon { transform: rotate(-90deg); }

    .empty-state { text-align: center; padding: 3rem; color: #a0aec0; }
    .spinner-loc { width: 32px; height: 32px; border: 3px solid #edf2f7; border-top-color: var(--ui-primary); border-radius: 50%; animation: spin .7s linear infinite; margin: 0 auto 1rem; }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>
@endsection

@section('content')
<div class="inventory-wrapper">
    <div class="header-flex">
        <div>
            <h2 style="font-weight:800;color:#1a202c;margin:0;">Ubicaciones del Almacén</h2>
            <p style="color:#a0aec0;font-size:0.9rem;margin:0;">Jerarquía y gestión de zonas de almacenamiento</p>
        </div>
        <button class="btn-ui-primary" onclick="abrirModalCrear()">
            <i class="fas fa-plus"></i> Nueva Ubicación
        </button>
    </div>

    {{-- Stats --}}
    <div class="stats-row">
        <div class="stat-card-ui">
            <div class="icon-box" style="background:#eef2ff;color:var(--ui-primary);"><i class="fas fa-warehouse"></i></div>
            <div><small class="text-muted d-block fw-bold" style="font-size:0.7rem;">Total Ubicaciones</small><h4 class="m-0 fw-bold" id="stat-total">—</h4></div>
        </div>
        <div class="stat-card-ui">
            <div class="icon-box" style="background:#e6fffa;color:var(--ui-success);"><i class="fas fa-layer-group"></i></div>
            <div><small class="text-muted d-block fw-bold" style="font-size:0.7rem;">Almacenes</small><h4 class="m-0 fw-bold" id="stat-almacenes">—</h4></div>
        </div>
        <div class="stat-card-ui">
            <div class="icon-box" style="background:#fffaf0;color:var(--ui-warning);"><i class="fas fa-map-marker-alt"></i></div>
            <div><small class="text-muted d-block fw-bold" style="font-size:0.7rem;">Zonas</small><h4 class="m-0 fw-bold" id="stat-zonas">—</h4></div>
        </div>
        <div class="stat-card-ui">
            <div class="icon-box" style="background:#fff5f5;color:var(--ui-danger);"><i class="fas fa-exclamation-circle"></i></div>
            <div><small class="text-muted d-block fw-bold" style="font-size:0.7rem;">Capacidad Alta</small><h4 class="m-0 fw-bold" id="stat-alta">—</h4></div>
        </div>
    </div>

    {{-- Alerta capacidad --}}
    <div class="alert-capacity" id="alertaCapacidad" style="display:none;">
        <i class="fas fa-exclamation-triangle" style="color:var(--ui-warning);font-size:1.2rem;"></i>
        <div>
            <strong style="font-size:0.9rem;display:block;" id="alertaTexto"></strong>
            <span style="font-size:0.8rem;color:#718096;">Ubicaciones con más del 80% de capacidad utilizada.</span>
        </div>
    </div>

    {{-- Árbol de ubicaciones --}}
    <div class="main-card-ui">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold m-0">Estructura de Ubicaciones</h5>
            <div class="d-flex gap-2">
                <button class="btn btn-light btn-sm fw-bold text-muted" onclick="expandAll(true)">Expandir todo</button>
                <button class="btn btn-light btn-sm fw-bold text-muted" onclick="expandAll(false)">Contraer todo</button>
                <button class="btn btn-light btn-sm" onclick="cargarUbicaciones()" title="Actualizar">
                    <i class="fas fa-sync-alt" id="refresh-icon"></i>
                </button>
            </div>
        </div>

        <div id="ubicaciones-tree">
            <div class="empty-state">
                <div class="spinner-loc"></div>
                Cargando ubicaciones...
            </div>
        </div>
    </div>
</div>

{{-- Modal Crear / Editar --}}
<div class="modal fade" id="modalUbicacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:15px;border:none;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="modalHeader">Nueva Ubicación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="editUbicacionId">
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">NOMBRE *</label>
                    <input type="text" id="inputNombre" class="form-control" style="border-radius:10px;padding:12px;" placeholder="Ej. Almacén Principal">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">CÓDIGO *</label>
                    <input type="text" id="inputCodigo" class="form-control" style="border-radius:10px;padding:12px;" placeholder="Ej. A-01">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">DESCRIPCIÓN</label>
                    <input type="text" id="inputDescripcion" class="form-control" style="border-radius:10px;padding:12px;" placeholder="Opcional">
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold text-muted small">CAPACIDAD</label>
                        <input type="number" id="inputCapacidad" class="form-control" style="border-radius:10px;padding:12px;" value="1000" min="0">
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold text-muted small">NIVEL</label>
                        <select id="inputNivel" class="form-select" style="border-radius:10px;padding:12px;">
                            <option value="1">1 - Almacén</option>
                            <option value="2">2 - Zona</option>
                            <option value="3">3 - Pasillo</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">UBICACIÓN PADRE (opcional)</label>
                    <select id="inputPadre" class="form-select" style="border-radius:10px;padding:12px;">
                        <option value="">Sin padre (raíz)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0 pb-4 px-4">
                <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn-ui-primary" id="btnGuardarUbicacion" onclick="guardarUbicacion()">
                    <i class="fas fa-save me-1"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:5000;" id="toastContainer"></div>
@endsection

@section('extra-js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const API_UBICACIONES = '/inventario/api/ubicaciones';
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '{{ csrf_token() }}';
let ubicacionesCache = [];

// ── Toast ──────────────────────────────────────────────────────────────────
function toast(msg, tipo = 'success') {
    const c = document.getElementById('toastContainer');
    const t = document.createElement('div');
    t.className = `toast align-items-center text-white bg-${tipo === 'success' ? 'success' : 'danger'} border-0 show`;
    t.innerHTML = `<div class="d-flex"><div class="toast-body">${msg}</div></div>`;
    c.appendChild(t);
    setTimeout(() => t.remove(), 3000);
}

// ── Cargar ubicaciones ─────────────────────────────────────────────────────
async function cargarUbicaciones() {
    const icon = document.getElementById('refresh-icon');
    icon.classList.add('fa-spin');

    try {
        const resp = await fetch(API_UBICACIONES);
        if (!resp.ok) throw new Error('HTTP ' + resp.status);
        const data = await resp.json();
        ubicacionesCache = data.ubicaciones ?? [];
        actualizarStats(ubicacionesCache);
        renderArbol(ubicacionesCache);
        poblarSelectPadre(ubicacionesCache);
    } catch (err) {
        document.getElementById('ubicaciones-tree').innerHTML = `
            <div class="empty-state">
                <i class="fas fa-exclamation-circle fa-2x text-danger mb-3 d-block"></i>
                No se pudo conectar con la API.<br>
                <small class="text-muted">Verifica que FastAPI esté corriendo.</small>
            </div>`;
        toast('Error al conectar con la API', 'error');
    } finally {
        icon.classList.remove('fa-spin');
    }
}

// ── Stats ──────────────────────────────────────────────────────────────────
function actualizarStats(ubicaciones) {
    document.getElementById('stat-total').textContent    = ubicaciones.length;
    document.getElementById('stat-almacenes').textContent = ubicaciones.filter(u => u.nivel === 1).length;
    document.getElementById('stat-zonas').textContent    = ubicaciones.filter(u => u.nivel === 2).length;
    const alta = ubicaciones.filter(u => u.capacidad > 0 && (u.ocupacion / u.capacidad) >= 0.8);
    document.getElementById('stat-alta').textContent = alta.length;

    if (alta.length > 0) {
        document.getElementById('alertaCapacidad').style.display = 'flex';
        document.getElementById('alertaTexto').textContent = `${alta.length} ubicación${alta.length > 1 ? 'es' : ''} cerca del límite de capacidad`;
    } else {
        document.getElementById('alertaCapacidad').style.display = 'none';
    }
}

// ── Render árbol jerárquico ────────────────────────────────────────────────
function renderArbol(ubicaciones) {
    const tree = document.getElementById('ubicaciones-tree');

    if (!ubicaciones.length) {
        tree.innerHTML = `<div class="empty-state">
            <i class="fas fa-warehouse fa-2x mb-3 d-block" style="opacity:.3;"></i>
            No hay ubicaciones registradas.<br>
            <small>Crea la primera con el botón "Nueva Ubicación".</small>
        </div>`;
        return;
    }

    // Organizar por jerarquía
    const raices = ubicaciones.filter(u => !u.id_padre);
    const hijos  = (padreId) => ubicaciones.filter(u => u.id_padre === padreId);

    const iconNivel = { 1: 'fa-warehouse', 2: 'fa-layer-group', 3: 'fa-map-marker-alt' };
    const colNivel  = { 1: '#eef2ff', 2: '#f1f3f9', 3: '#eef2ff' };

    function renderNodo(u, nivel = 0) {
        const pct      = u.capacidad > 0 ? Math.round((u.ocupacion / u.capacidad) * 100) : 0;
        const barColor = pct >= 80 ? 'var(--ui-danger)' : pct >= 60 ? 'var(--ui-warning)' : 'var(--ui-success)';
        const subHijos = hijos(u.id_ubicacion);
        const groupId  = `group-${u.id_ubicacion}`;
        const levelClass = nivel === 1 ? 'level-1' : nivel >= 2 ? 'level-2' : '';

        let html = `
        <div class="location-group" id="node-wrap-${u.id_ubicacion}">
            <div class="location-item ${levelClass}" id="node-${u.id_ubicacion}">
                ${subHijos.length ? `<i class="fas fa-chevron-down chevron-toggle" onclick="toggleGroup('${groupId}', this)"></i>` : '<div style="width:20px"></div>'}
                <div class="icon-box" style="background:${colNivel[u.nivel]||'#eef2ff'};color:var(--ui-primary);width:32px;height:32px;margin:0 15px;">
                    <i class="fas ${iconNivel[u.nivel]||'fa-map-marker-alt'} fa-sm"></i>
                </div>
                <div style="flex:1">
                    <span class="fw-bold" style="font-size:0.9rem;">${u.nombre}</span>
                    <span class="loc-code">${u.codigo}</span>
                    <div class="loc-info-text">${u.ocupacion} / ${u.capacidad} unidades</div>
                </div>
                <div class="progress-container-ui">
                    <div class="progress-ui"><div class="progress-bar-ui" style="width:${pct}%;background:${barColor};"></div></div>
                    <span class="text-percentage">${pct}%</span>
                </div>
                <div class="action-btns">
                    <i class="fas fa-pen" onclick="abrirEditar(${u.id_ubicacion})" title="Editar"></i>
                    <i class="fas fa-trash" onclick="eliminarUbicacion(${u.id_ubicacion}, '${u.nombre.replace(/'/g,"\\'")}')"></i>
                </div>
            </div>`;

        if (subHijos.length) {
            html += `<div id="${groupId}">`;
            subHijos.forEach(hijo => { html += renderNodo(hijo, nivel + 1); });
            html += `</div>`;
        }

        html += `</div>`;
        return html;
    }

    tree.innerHTML = raices.map(u => renderNodo(u)).join('');
}

// ── Expand / Collapse ──────────────────────────────────────────────────────
function toggleGroup(id, icon) {
    const el = document.getElementById(id);
    const isHidden = el.style.display === 'none';
    el.style.display = isHidden ? 'block' : 'none';
    icon.classList.toggle('collapsed-icon', !isHidden);
}

function expandAll(expand) {
    document.querySelectorAll('[id^="group-"]').forEach(el => {
        el.style.display = expand ? 'block' : 'none';
    });
    document.querySelectorAll('.chevron-toggle').forEach(i => {
        i.classList.toggle('collapsed-icon', !expand);
    });
}

// ── Poblar select de padre ─────────────────────────────────────────────────
function poblarSelectPadre(ubicaciones) {
    const sel = document.getElementById('inputPadre');
    sel.innerHTML = '<option value="">Sin padre (raíz)</option>';
    ubicaciones.forEach(u => {
        const opt = document.createElement('option');
        opt.value = u.id_ubicacion;
        opt.textContent = `${u.codigo} — ${u.nombre}`;
        sel.appendChild(opt);
    });
}

// ── Modal Crear ────────────────────────────────────────────────────────────
function abrirModalCrear() {
    document.getElementById('modalHeader').textContent = 'Nueva Ubicación';
    document.getElementById('editUbicacionId').value   = '';
    document.getElementById('inputNombre').value       = '';
    document.getElementById('inputCodigo').value       = '';
    document.getElementById('inputDescripcion').value  = '';
    document.getElementById('inputCapacidad').value    = '1000';
    document.getElementById('inputNivel').value        = '1';
    document.getElementById('inputPadre').value        = '';
    new bootstrap.Modal(document.getElementById('modalUbicacion')).show();
}

// ── Modal Editar ───────────────────────────────────────────────────────────
function abrirEditar(id) {
    const u = ubicacionesCache.find(x => x.id_ubicacion === id);
    if (!u) return;
    document.getElementById('modalHeader').textContent = 'Editar Ubicación';
    document.getElementById('editUbicacionId').value   = u.id_ubicacion;
    document.getElementById('inputNombre').value       = u.nombre;
    document.getElementById('inputCodigo').value       = u.codigo;
    document.getElementById('inputDescripcion').value  = u.descripcion ?? '';
    document.getElementById('inputCapacidad').value    = u.capacidad;
    document.getElementById('inputNivel').value        = u.nivel;
    document.getElementById('inputPadre').value        = u.id_padre ?? '';
    new bootstrap.Modal(document.getElementById('modalUbicacion')).show();
}

// ── Guardar ────────────────────────────────────────────────────────────────
async function guardarUbicacion() {
    const id          = document.getElementById('editUbicacionId').value;
    const nombre      = document.getElementById('inputNombre').value.trim();
    const codigo      = document.getElementById('inputCodigo').value.trim();
    const descripcion = document.getElementById('inputDescripcion').value.trim();
    const capacidad   = parseInt(document.getElementById('inputCapacidad').value);
    const nivel       = parseInt(document.getElementById('inputNivel').value);
    const id_padre    = document.getElementById('inputPadre').value || null;

    if (!nombre || !codigo) { toast('Nombre y código son obligatorios', 'error'); return; }

    const payload = { nombre, codigo, descripcion: descripcion || null, capacidad, nivel, id_padre: id_padre ? parseInt(id_padre) : null };
    const esEdicion = !!id;
    const url    = esEdicion ? `${API_UBICACIONES}/${id}` : API_UBICACIONES;
    const method = esEdicion ? 'PUT' : 'POST';

    const btn = document.getElementById('btnGuardarUbicacion');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Guardando...';

    try {
        const resp = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify(payload),
        });
        const data = await resp.json();
        if (!resp.ok) throw new Error(data.detail ?? 'Error al guardar');
        toast(esEdicion ? 'Ubicación actualizada' : 'Ubicación creada');
        bootstrap.Modal.getInstance(document.getElementById('modalUbicacion')).hide();
        cargarUbicaciones();
    } catch (err) {
        toast('Error: ' + err.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-1"></i> Guardar';
    }
}

// ── Eliminar ───────────────────────────────────────────────────────────────
async function eliminarUbicacion(id, nombre) {
    const result = await Swal.fire({
        title: '¿Eliminar ubicación?',
        html: `Se eliminará <strong>${nombre}</strong> y todas sus sub-ubicaciones.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74a3b',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    });

    if (!result.isConfirmed) return;

    try {
        const resp = await fetch(`${API_UBICACIONES}/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF },
        });
        if (!resp.ok) throw new Error('HTTP ' + resp.status);
        toast(`"${nombre}" eliminada`);
        cargarUbicaciones();
    } catch (err) {
        toast('Error al eliminar', 'error');
    }
}

document.addEventListener('DOMContentLoaded', cargarUbicaciones);
</script>
@endsection