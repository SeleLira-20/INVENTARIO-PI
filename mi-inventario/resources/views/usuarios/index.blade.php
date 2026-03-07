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

    <div class="row g-3 mb-4 text-center">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-primary border-4">
                <div class="card-body">
                    <h3 class="fw-bold text-primary mb-1" id="stat-total">7</h3>
                    <span class="text-uppercase small fw-semibold text-muted">Total Usuarios</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-success border-4">
                <div class="card-body">
                    <h3 class="fw-bold text-success mb-1" id="stat-active">6</h3>
                    <span class="text-uppercase small fw-semibold text-muted">Activos</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-danger border-4">
                <div class="card-body">
                    <h3 class="fw-bold text-danger mb-1" id="stat-inactive">1</h3>
                    <span class="text-uppercase small fw-semibold text-muted">Inactivos</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-warning border-4">
                <div class="card-body">
                    <h3 class="fw-bold text-warning mb-1" id="stat-admins">2</h3>
                    <span class="text-uppercase small fw-semibold text-muted">Admins</span>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 role-card-hover" onclick="filterByRole('Administrador')">
                    <div class="card-body">
                        <h6 class="fw-bold mb-2 text-danger"><i class="fas fa-crown me-2"></i>Administrador</h6>
                        <p class="small text-muted mb-0">Control total del sistema.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 role-card-hover" onclick="filterByRole('Gerente')">
                    <div class="card-body">
                        <h6 class="fw-bold mb-2 text-primary"><i class="fas fa-briefcase me-2"></i>Gerente</h6>
                        <p class="small text-muted mb-0">Gestión de stock y reportes.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 role-card-hover" onclick="filterByRole('Operador')">
                    <div class="card-body">
                        <h6 class="fw-bold mb-2 text-success"><i class="fas fa-tools me-2"></i>Operador</h6>
                        <p class="small text-muted mb-0">Entradas y salidas físicas.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 role-card-hover" onclick="filterByRole('Visualizador')">
                    <div class="card-body">
                        <h6 class="fw-bold mb-2 text-secondary"><i class="fas fa-eye me-2"></i>Visualizador</h6>
                        <p class="small text-muted mb-0">Solo consulta de datos.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-body p-3">
            <div class="row align-items-center g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="userInput" class="form-control bg-light border-0" placeholder="Buscar..." onkeyup="filterUsers()">
                    </div>
                </div>
                <div class="col-md-8 text-md-end">
                    <div class="d-flex gap-2 justify-content-md-end flex-wrap">
                        <button class="btn btn-filter active" id="btn-Todos" onclick="filterAll()">Todos</button>
                        <button class="btn btn-filter" id="btn-Administrador" onclick="filterByRole('Administrador')">Admins</button>
                        <button class="btn btn-filter" id="btn-Gerente" onclick="filterByRole('Gerente')">Gerentes</button>
                        <button class="btn btn-filter" id="btn-Operador" onclick="filterByRole('Operador')">Operadores</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="userTable">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">Usuario</th>
                        <th class="py-3">Correo</th>
                        <th class="py-3">Rol</th>
                        <th class="py-3">Departamento</th>
                        <th class="py-3">Estado</th>
                        <th class="px-4 py-3 text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr data-role="Administrador">
                        <td class="px-4"><div class="d-flex align-items-center"><div class="avatar-circle bg-primary text-white me-3">JD</div><strong class="u-name">John Doe</strong></div></td>
                        <td class="u-email text-muted">john.doe@empresa.com</td>
                        <td><span class="badge-role role-admin">Administrador</span></td>
                        <td class="u-dept">Administración</td>
                        <td><span class="status-pill status-active">Activo</span></td>
                        <td class="px-4 text-end">
                            <button class="btn btn-sm btn-light text-primary" onclick="abrirModalEditar(this)"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-light text-danger" onclick="eliminarFila(this)"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .role-card-hover { transition: 0.3s; cursor: pointer; }
    .role-card-hover:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.08) !important; }
    .btn-filter { border-radius: 50px; padding: 6px 18px; font-size: 0.85rem; background: #f8f9fa; border: 1px solid #eee; color: #6c757d; }
    .btn-filter.active { background: #1e3c72 !important; color: white !important; border-color: #1e3c72; }
    .avatar-circle { width: 35px; height: 35px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: bold; }
    .badge-role { padding: 4px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
    .role-admin { background: #fee2e2; color: #dc2626; }
    .role-gerente { background: #dbeafe; color: #2563eb; }
    .role-operador { background: #dcfce7; color: #16a34a; }
    .status-pill { padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: bold; }
    .status-active { color: #16a34a; background: #f0fdf4; }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function filterUsers() {
        let val = document.getElementById("userInput").value.toLowerCase();
        document.querySelectorAll("#userTable tbody tr").forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(val) ? "" : "none";
        });
    }

    function filterByRole(role) {
        document.querySelectorAll("#userTable tbody tr").forEach(row => {
            row.style.display = (row.getAttribute('data-role') === role) ? "" : "none";
        });
        updatePills(role);
    }

    function filterAll() {
        document.querySelectorAll("#userTable tbody tr").forEach(row => row.style.display = "");
        updatePills('Todos');
    }

    // CORRECCIÓN: Ahora el cambio de color es exacto por ID
    function updatePills(role) {
        document.querySelectorAll('.btn-filter').forEach(btn => btn.classList.remove('active'));
        let activeBtn = document.getElementById('btn-' + role);
        if (activeBtn) activeBtn.classList.add('active');
    }

    // FORMULARIO NUEVO USUARIO FUNCIONAL
    function abrirModalNuevo() {
        Swal.fire({
            title: 'Registrar Nuevo Usuario',
            html: `
                <div class="text-start">
                    <label class="small fw-bold">Nombre Completo</label>
                    <input id="n-name" class="form-control mb-3" placeholder="Ej. Selene Lira">
                    <label class="small fw-bold">Correo Institucional</label>
                    <input id="n-email" class="form-control mb-3" placeholder="usuario@upq.edu.mx">
                    <div class="row">
                        <div class="col-6">
                            <label class="small fw-bold">Rol</label>
                            <select id="n-role" class="form-select">
                                <option value="Administrador">Administrador</option>
                                <option value="Gerente">Gerente</option>
                                <option value="Operador">Operador</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="small fw-bold">Departamento</label>
                            <input id="n-dept" class="form-control" placeholder="Sistemas">
                        </div>
                    </div>
                </div>`,
            showCancelButton: true,
            confirmButtonText: 'Guardar Usuario',
            confirmButtonColor: '#1e3c72'
        }).then((result) => {
            if (result.isConfirmed) {
                const name = document.getElementById('n-name').value;
                const email = document.getElementById('n-email').value;
                const role = document.getElementById('n-role').value;
                const dept = document.getElementById('n-dept').value;
                
                // Agregar a la tabla físicamente
                const table = document.getElementById('userTable').getElementsByTagName('tbody')[0];
                const newRow = table.insertRow();
                newRow.setAttribute('data-role', role);
                newRow.innerHTML = `
                    <td class="px-4"><div class="d-flex align-items-center"><div class="avatar-circle bg-secondary text-white me-3">${name.substring(0,2).toUpperCase()}</div><strong class="u-name">${name}</strong></div></td>
                    <td class="u-email text-muted">${email}</td>
                    <td><span class="badge-role role-${role.toLowerCase()}">${role}</span></td>
                    <td class="u-dept">${dept}</td>
                    <td><span class="status-pill status-active">Activo</span></td>
                    <td class="px-4 text-end">
                        <button class="btn btn-sm btn-light text-primary" onclick="abrirModalEditar(this)"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-light text-danger" onclick="eliminarFila(this)"><i class="fas fa-trash"></i></button>
                    </td>`;
                actualizarEstadisticas();
                Swal.fire('¡Éxito!', 'Usuario agregado al inventario.', 'success');
            }
        });
    }

    function abrirModalEditar(btn) {
        let row = btn.closest('tr');
        Swal.fire({
            title: 'Editar Usuario',
            html: `<input id="e-name" class="form-control mb-2" value="${row.querySelector('.u-name').innerText}">
                   <input id="e-email" class="form-control" value="${row.querySelector('.u-email').innerText}">`,
            confirmButtonText: 'Actualizar',
            confirmButtonColor: '#1e3c72',
            showCancelButton: true
        }).then((res) => {
            if(res.isConfirmed) {
                row.querySelector('.u-name').innerText = document.getElementById('e-name').value;
                row.querySelector('.u-email').innerText = document.getElementById('e-email').value;
                Swal.fire('Actualizado', '', 'success');
            }
        });
    }

    function eliminarFila(btn) {
        Swal.fire({ title: '¿Eliminar?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Sí' })
        .then((res) => { if(res.isConfirmed) { btn.closest('tr').remove(); actualizarEstadisticas(); } });
    }

    function actualizarEstadisticas() {
        const rows = document.querySelectorAll('#userTable tbody tr');
        document.getElementById('stat-total').innerText = rows.length;
    }
</script>
@endsection