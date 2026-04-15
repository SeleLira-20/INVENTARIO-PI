@extends('layouts.app')

@section('title', 'Gestión de Usuarios - Universal Inventory')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0" style="color: #1e3c72;">
                <i class="fas fa-users me-2"></i>Gestión de Usuarios
            </h2>
            <p class="text-muted mb-0">Panel de control administrativo</p>
        </div>
        <button class="btn btn-primary shadow-sm px-4 py-2"
                style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border: none; border-radius: 10px;"
                onclick="abrirModalNuevo()">
            <i class="fas fa-user-plus me-2"></i>Nuevo Usuario
        </button>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4 text-center">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-primary border-4">
                <div class="card-body">
                    <h3 class="fw-bold text-primary mb-1" id="stat-total">—</h3>
                    <span class="text-uppercase small fw-semibold text-muted">Total Usuarios</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-success border-4">
                <div class="card-body">
                    <h3 class="fw-bold text-success mb-1" id="stat-operador">—</h3>
                    <span class="text-uppercase small fw-semibold text-muted">Operadores</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-danger border-4">
                <div class="card-body">
                    <h3 class="fw-bold text-danger mb-1" id="stat-admin">—</h3>
                    <span class="text-uppercase small fw-semibold text-muted">Admins</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-warning border-4">
                <div class="card-body">
                    <h3 class="fw-bold text-warning mb-1" id="stat-pin">—</h3>
                    <span class="text-uppercase small fw-semibold text-muted">Con PIN Asignado</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-body p-3">
            <div class="row align-items-center g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="userInput" class="form-control bg-light border-0" placeholder="Buscar..." oninput="filtrarTabla()">
                    </div>
                </div>
                <div class="col-md-8 text-md-end">
                    <div class="d-flex gap-2 justify-content-md-end flex-wrap align-items-center">
                        <button class="btn btn-filter active" id="btn-Todos" onclick="filtrarTodos()">Todos</button>
                        <button class="btn btn-filter" id="btn-Administrador" onclick="filtrarRol('Administrador')">Admins</button>
                        <button class="btn btn-filter" id="btn-Gerente" onclick="filtrarRol('Gerente')">Gerentes</button>
                        <button class="btn btn-filter" id="btn-Operador" onclick="filtrarRol('Operador')">Operadores</button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="cargarUsuarios()" title="Actualizar">
                            <i class="fas fa-sync-alt" id="refresh-icon"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="userTable">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">Usuario</th>
                        <th class="py-3">ID Empleado</th>
                        <th class="py-3">Correo</th>
                        <th class="py-3">Rol</th>
                        <th class="py-3">PIN</th>
                        <th class="py-3">Permisos App</th>
                        <th class="px-4 py-3 text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                            Cargando usuarios...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .btn-filter { border-radius: 50px; padding: 6px 18px; font-size: 0.85rem; background: #f8f9fa; border: 1px solid #eee; color: #6c757d; }
    .btn-filter.active { background: #1e3c72 !important; color: white !important; border-color: #1e3c72; }
    .avatar-circle { width: 35px; height: 35px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 13px; }
    .badge-role { padding: 4px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
    .role-administrador { background: #fee2e2; color: #dc2626; }
    .role-gerente       { background: #dbeafe; color: #2563eb; }
    .role-operador      { background: #dcfce7; color: #16a34a; }
    .role-visualizador  { background: #f3f4f6; color: #6b7280; }
    .pin-badge { font-family: monospace; background: #1e3c72; color: white; border-radius: 6px; padding: 3px 10px; font-size: 13px; font-weight: 700; letter-spacing: 2px; }
    .pin-empty { background: #f1f5f9; color: #94a3b8; border-radius: 6px; padding: 3px 10px; font-size: 12px; }
    .permiso-tag { display: inline-block; background: #eff6ff; color: #2563eb; border-radius: 6px; padding: 3px 6px; font-size: 13px; margin: 1px; cursor: default; }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const API_USUARIOS = '/inventario/api/usuarios';
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '{{ csrf_token() }}';
let usuariosCache = [];
let filtroRolActual = '';

const PERMISOS_DISPONIBLES = [
    { key: 'inventario', label: 'Ver Inventario',   icon: '📦' },
    { key: 'escanear',   label: 'Escanear Códigos', icon: '📷' },
    { key: 'reportes',   label: 'Reportar Problemas', icon: '⚠️' },
    { key: 'picking',    label: 'Tareas de Picking', icon: '📋' },
];

function toast(msg, icon = 'success') {
    Swal.fire({ toast: true, position: 'top-end', icon, title: msg,
        showConfirmButton: false, timer: 2500, timerProgressBar: true });
}

async function cargarUsuarios() {
    const icon = document.getElementById('refresh-icon');
    icon.classList.add('fa-spin');
    try {
        const resp = await fetch(API_USUARIOS);
        if (!resp.ok) throw new Error('HTTP ' + resp.status);
        const data = await resp.json();
        usuariosCache = data.usuarios ?? [];
        actualizarStats(usuariosCache);
        renderTabla(usuariosCache);
    } catch (err) {
        document.getElementById('userTableBody').innerHTML = `
            <tr><td colspan="7" class="text-center py-5 text-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                No se pudo conectar con la API.
            </td></tr>`;
        toast('Error al conectar con la API', 'error');
    } finally {
        icon.classList.remove('fa-spin');
    }
}

function renderTabla(usuarios) {
    const tbody = document.getElementById('userTableBody');
    if (!usuarios.length) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5 text-muted">
            <i class="fas fa-users me-2"></i>No se encontraron usuarios</td></tr>`;
        return;
    }

    tbody.innerHTML = usuarios.map(u => {
        const iniciales = u.nombre.split(' ').map(n => n[0]).join('').substring(0,2).toUpperCase();
        const rolClass  = u.rol?.toLowerCase() ?? 'operador';
        const colores   = { administrador:'bg-danger', gerente:'bg-primary', operador:'bg-success', visualizador:'bg-secondary' };
        const bg        = colores[rolClass] ?? 'bg-secondary';
        const permisos  = (u.permisos || '').split(',').filter(Boolean);
        const permisosHtml = permisos.map(p => {
            const perm = PERMISOS_DISPONIBLES.find(x => x.key === p.trim());
            return perm ? `<span class="permiso-tag" title="${perm.label}">${perm.icon}</span>` : '';
        }).join('');

        return `
        <tr data-rol="${u.rol ?? 'Operador'}" data-id="${u.id_usuario}">
            <td class="px-4">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle ${bg} text-white me-3">${iniciales}</div>
                    <strong>${u.nombre}</strong>
                </div>
            </td>
            <td><code>${u.id_empleado || '—'}</code></td>
            <td class="text-muted">${u.email}</td>
            <td><span class="badge-role role-${rolClass}">${u.rol ?? 'Operador'}</span></td>
            <td>
                ${u.pin
                    ? `<span class="pin-badge">${u.pin}</span>`
                    : `<span class="pin-empty">Sin PIN</span>`}
            </td>
            <td>${permisosHtml || '<span class="text-muted small">Sin permisos</span>'}</td>
            <td class="px-4 text-end">
                <button class="btn btn-sm btn-light text-primary me-1" onclick="abrirModalEditar(${u.id_usuario})" title="Editar">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-light text-danger" onclick="eliminarUsuario(${u.id_usuario}, '${u.nombre.replace(/'/g,"\\'")}')">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>`;
    }).join('');
}

function actualizarStats(usuarios) {
    document.getElementById('stat-total').textContent    = usuarios.length;
    document.getElementById('stat-operador').textContent = usuarios.filter(u => u.rol === 'Operador').length;
    document.getElementById('stat-admin').textContent    = usuarios.filter(u => u.rol === 'Administrador').length;
    document.getElementById('stat-pin').textContent      = usuarios.filter(u => u.pin).length;
}

function filtrarTabla() {
    const q = document.getElementById('userInput').value.toLowerCase();
    const filtrados = usuariosCache.filter(u =>
        u.nombre.toLowerCase().includes(q) ||
        u.email.toLowerCase().includes(q) ||
        (u.id_empleado || '').toLowerCase().includes(q) ||
        (u.rol ?? '').toLowerCase().includes(q)
    ).filter(u => !filtroRolActual || u.rol === filtroRolActual);
    renderTabla(filtrados);
}

function filtrarRol(rol) {
    filtroRolActual = rol;
    document.querySelectorAll('.btn-filter').forEach(b => b.classList.remove('active'));
    const btn = document.getElementById('btn-' + rol);
    if (btn) btn.classList.add('active');
    filtrarTabla();
}

function filtrarTodos() {
    filtroRolActual = '';
    document.querySelectorAll('.btn-filter').forEach(b => b.classList.remove('active'));
    document.getElementById('btn-Todos').classList.add('active');
    filtrarTabla();
}

function getFormHtml(u = null) {
    const permisos = (u?.permisos || 'inventario,escanear,reportes,picking').split(',').map(p => p.trim());
    const permisosChecks = PERMISOS_DISPONIBLES.map(p => `
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="perm-${p.key}" value="${p.key}"
                ${permisos.includes(p.key) ? 'checked' : ''}>
            <label class="form-check-label small" for="perm-${p.key}">${p.icon} ${p.label}</label>
        </div>`).join('');

    return `
        <div class="text-start">
            <div class="row g-2 mb-2">
                <div class="col-8">
                    <label class="small fw-bold mb-1">Nombre completo *</label>
                    <input id="m-nombre" class="form-control form-control-sm" value="${u?.nombre || ''}" placeholder="Juan Pérez">
                </div>
                <div class="col-4">
                    <label class="small fw-bold mb-1">ID Empleado *</label>
                    <input id="m-idempleado" class="form-control form-control-sm" value="${u?.id_empleado || ''}" placeholder="EMP-001">
                </div>
            </div>
            <div class="mb-2">
                <label class="small fw-bold mb-1">Correo *</label>
                <input id="m-email" class="form-control form-control-sm" value="${u?.email || ''}" placeholder="correo@empresa.com">
            </div>
            <div class="row g-2 mb-2">
                <div class="col-6">
                    <label class="small fw-bold mb-1">Rol</label>
                    <select id="m-rol" class="form-select form-select-sm">
                        ${['Operador','Administrador','Gerente','Visualizador'].map(r =>
                            `<option value="${r}" ${u?.rol === r ? 'selected' : ''}>${r}</option>`
                        ).join('')}
                    </select>
                </div>
                <div class="col-6">
                    <label class="small fw-bold mb-1">PIN (4 dígitos)</label>
                    <input id="m-pin" class="form-control form-control-sm" value="${u?.pin || ''}"
                        placeholder="ej. 1234" maxlength="4" type="text" inputmode="numeric">
                </div>
            </div>
            <div class="mb-1">
                <label class="small fw-bold mb-2 d-block">Permisos en App Móvil</label>
                <div class="p-2" style="background:#f8fafc;border-radius:8px;">
                    ${permisosChecks}
                </div>
            </div>
        </div>`;
}

function getPermisos() {
    return PERMISOS_DISPONIBLES
        .filter(p => document.getElementById('perm-' + p.key)?.checked)
        .map(p => p.key)
        .join(',');
}

async function abrirModalNuevo() {
    const { value: confirmado } = await Swal.fire({
        title: 'Nuevo Usuario',
        html: getFormHtml(),
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-save me-1"></i> Guardar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#1e3c72',
        width: 560,
        preConfirm: async () => {
            const nombre     = document.getElementById('m-nombre').value.trim();
            const email      = document.getElementById('m-email').value.trim();
            const id_empleado= document.getElementById('m-idempleado').value.trim().toUpperCase();
            const rol        = document.getElementById('m-rol').value;
            const pin        = document.getElementById('m-pin').value.trim();
            const permisos   = getPermisos();

            if (!nombre || !email || !id_empleado) {
                Swal.showValidationMessage('Nombre, correo e ID de empleado son obligatorios');
                return false;
            }
            if (pin && !/^\d{4}$/.test(pin)) {
                Swal.showValidationMessage('El PIN debe ser exactamente 4 dígitos numéricos');
                return false;
            }
            try {
                const resp = await fetch(API_USUARIOS, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                    body: JSON.stringify({ nombre, email, rol, id_empleado, pin: pin || null, permisos }),
                });
                const data = await resp.json();
                if (data.status === '400') throw new Error(data.mensaje);
                if (!resp.ok) throw new Error('Error del servidor');
                return data;
            } catch (err) {
                Swal.showValidationMessage('Error: ' + err.message);
                return false;
            }
        }
    });
    if (confirmado) { toast('Usuario creado correctamente'); cargarUsuarios(); }
}

async function abrirModalEditar(id) {
    const u = usuariosCache.find(x => x.id_usuario === id);
    if (!u) return;

    const { value: confirmado } = await Swal.fire({
        title: `Editar: ${u.nombre}`,
        html: getFormHtml(u),
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-save me-1"></i> Guardar Cambios',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#1e3c72',
        width: 560,
        preConfirm: async () => {
            const nombre     = document.getElementById('m-nombre').value.trim();
            const email      = document.getElementById('m-email').value.trim();
            const id_empleado= document.getElementById('m-idempleado').value.trim().toUpperCase();
            const rol        = document.getElementById('m-rol').value;
            const pin        = document.getElementById('m-pin').value.trim();
            const permisos   = getPermisos();

            if (!nombre || !email || !id_empleado) {
                Swal.showValidationMessage('Nombre, correo e ID de empleado son obligatorios');
                return false;
            }
            if (pin && !/^\d{4}$/.test(pin)) {
                Swal.showValidationMessage('El PIN debe ser exactamente 4 dígitos numéricos');
                return false;
            }
            try {
                const resp = await fetch(`${API_USUARIOS}/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                    body: JSON.stringify({ nombre, email, rol, id_empleado, pin: pin || null, permisos }),
                });
                const data = await resp.json();
                if (data.status === '400' || data.status === '404') throw new Error(data.mensaje);
                if (!resp.ok) throw new Error('Error del servidor');
                return data;
            } catch (err) {
                Swal.showValidationMessage('Error: ' + err.message);
                return false;
            }
        }
    });
    if (confirmado) { toast('Usuario actualizado correctamente'); cargarUsuarios(); }
}

async function eliminarUsuario(id, nombre) {
    const result = await Swal.fire({
        title: '¿Eliminar usuario?',
        html: `Se eliminará a <strong>${nombre}</strong> del sistema.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-trash me-1"></i> Eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc2626',
    });
    if (!result.isConfirmed) return;
    try {
        const resp = await fetch(`${API_USUARIOS}/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF }
        });
        if (!resp.ok) throw new Error('HTTP ' + resp.status);
        toast(`"${nombre}" eliminado correctamente`);
        cargarUsuarios();
    } catch {
        toast('Error al eliminar el usuario', 'error');
    }
}

document.addEventListener('DOMContentLoaded', cargarUsuarios);
</script>
@endsection