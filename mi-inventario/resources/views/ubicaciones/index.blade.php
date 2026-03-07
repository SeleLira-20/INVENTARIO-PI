@extends('layouts.app')

@section('title', 'Ubicaciones - Universal Inventory')

@section('extra-css')
<style>
    :root {
        --ui-primary: #0061f2;
        --ui-bg: #f8f9fc;
        --ui-card-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        --ui-text-main: #2d3748;
        --ui-text-muted: #858796;
        --ui-warning: #f6993f;
        --ui-danger: #e74a3b;
        --ui-success: #38b2ac;
    }

    body { background-color: var(--ui-bg); font-family: 'Nunito', sans-serif; color: var(--ui-text-main); }
    .inventory-wrapper { padding: 1.5rem; }
    
    .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
    .btn-ui-primary {
        background-color: var(--ui-primary); color: white; border-radius: 8px;
        padding: 10px 20px; font-weight: 700; border: none; font-size: 0.85rem;
        transition: all 0.3s;
    }

    .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 1.5rem; }
    .stat-card-ui {
        background: white; border-radius: 12px; padding: 1.2rem;
        box-shadow: var(--ui-card-shadow); display: flex; align-items: center; gap: 1rem;
    }
    .icon-box {
        width: 40px; height: 40px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center; font-size: 1.1rem;
    }

    .alert-capacity {
        background-color: #fffdf0; border: 1px solid #ffeeba; border-radius: 12px;
        padding: 1rem 1.5rem; margin-bottom: 2rem; display: flex; gap: 1rem; align-items: center;
    }

    .main-card-ui { background: white; border-radius: 15px; padding: 1.5rem; box-shadow: var(--ui-card-shadow); }
    
    .location-item {
        background: #f8faff; border: 1px solid #edf2f7; border-radius: 12px;
        margin-bottom: 0.8rem; padding: 0.8rem 1.2rem; display: flex; align-items: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
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
</style>
@endsection

@section('content')
<div class="inventory-wrapper">
    <div class="header-flex">
        <div>
            <h2 style="font-weight: 800; color: #1a202c; margin:0;">Ubicaciones del Almacén</h2>
            <p style="color: #a0aec0; font-size: 0.9rem; margin:0;">Jerarquía y gestión de zonas de almacenamiento</p>
        </div>
        <button class="btn-ui-primary" onclick="abrirModalCrear()">
            <i class="fas fa-plus"></i> Nueva Ubicación
        </button>
    </div>

    <div class="stats-row">
        <div class="stat-card-ui">
            <div class="icon-box" style="background: #eef2ff; color: var(--ui-primary);"><i class="fas fa-warehouse"></i></div>
            <div><small class="text-muted d-block fw-bold" style="font-size:0.7rem;">Almacenes</small><h4 class="m-0 fw-bold">2</h4></div>
        </div>
        <div class="stat-card-ui">
            <div class="icon-box" style="background: #e6fffa; color: var(--ui-success);"><i class="fas fa-box"></i></div>
            <div><small class="text-muted d-block fw-bold" style="font-size:0.7rem;">Total Items</small><h4 class="m-0 fw-bold">401</h4></div>
        </div>
        <div class="stat-card-ui">
            <div class="icon-box" style="background: #fffaf0; color: var(--ui-warning);"><i class="fas fa-database"></i></div>
            <div><small class="text-muted d-block fw-bold" style="font-size:0.7rem;">Capacidad Total</small><h4 class="m-0 fw-bold">15,000</h4></div>
        </div>
        <div class="stat-card-ui">
            <div class="icon-box" style="background: #fff5f5; color: var(--ui-warning);"><i class="fas fa-exclamation-circle"></i></div>
            <div><small class="text-muted d-block fw-bold" style="font-size:0.7rem;">Ocupación</small><h4 class="m-0 fw-bold">67.3%</h4></div>
        </div>
    </div>

    <div class="alert-capacity">
        <i class="fas fa-exclamation-triangle" style="color: var(--ui-warning); font-size: 1.2rem;"></i>
        <div>
            <strong style="font-size: 0.9rem; display: block;">2 ubicaciones cerca del límite de capacidad</strong>
            <span style="font-size: 0.8rem; color: #718096;">Las zonas A-02-2 y B-01-1 han alcanzado más del 80% de su capacidad.</span>
        </div>
    </div>

    <div class="main-card-ui">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold m-0">Estructura de Ubicaciones</h5>
            <div>
                <button class="btn btn-light btn-sm fw-bold text-muted" onclick="expandAll(true)">Expandir todo</button>
                <button class="btn btn-light btn-sm fw-bold text-muted" onclick="expandAll(false)">Contraer todo</button>
            </div>
        </div>

        <div class="location-group mb-3" id="node-A">
            <div class="location-item">
                <i class="fas fa-chevron-down chevron-toggle" onclick="toggleGroup('group-a', this)"></i>
                <div class="icon-box" style="background: #eef2ff; color: var(--ui-primary); width:32px; height:32px; margin: 0 15px;"><i class="fas fa-warehouse fa-sm"></i></div>
                <div style="flex:1">
                    <span class="fw-bold loc-display-name" style="font-size: 0.9rem;">Almacén Principal</span> 
                    <span class="loc-code loc-display-code">A</span>
                    <div class="loc-info-text">245 items &nbsp; 6845/10000 unidades</div>
                </div>
                <div class="progress-container-ui">
                    <div class="progress-ui"><div class="progress-bar-ui" style="width: 68%; background: var(--ui-warning);"></div></div>
                    <span class="text-percentage">68%</span>
                </div>
                <div class="action-btns">
                    <i class="fas fa-pen" onclick="abrirEditar('node-A')"></i>
                    <i class="fas fa-trash" onclick="eliminarVisual('node-A')"></i>
                </div>
            </div>

            <div id="group-a">
                <div class="location-item level-1" id="node-A-02">
                    <i class="fas fa-chevron-down chevron-toggle" onclick="toggleGroup('sub-a-02', this)"></i>
                    <div class="icon-box" style="background: #f1f3f9; color: var(--ui-primary); width:32px; height:32px; margin: 0 15px;"><i class="fas fa-layer-group fa-sm"></i></div>
                    <div style="flex:1">
                        <span class="fw-bold loc-display-name" style="font-size: 0.85rem;">Zona Mobiliario</span> 
                        <span class="loc-code loc-display-code">A-02</span>
                        <div class="loc-info-text">67 items &nbsp; 2134/3000 unidades</div>
                    </div>
                    <div class="progress-container-ui">
                        <div class="progress-ui"><div class="progress-bar-ui" style="width: 71%; background: var(--ui-warning);"></div></div>
                        <span class="text-percentage">71%</span>
                    </div>
                    <div class="action-btns">
                        <i class="fas fa-pen" onclick="abrirEditar('node-A-02')"></i>
                        <i class="fas fa-trash" onclick="eliminarVisual('node-A-02')"></i>
                    </div>
                </div>

                <div id="sub-a-02">
                    <div class="location-item level-2" id="node-A-02-2">
                        <div style="width:20px"></div>
                        <div class="icon-box" style="background: #eef2ff; color: var(--ui-primary); width:32px; height:32px; margin: 0 15px;"><i class="fas fa-map-marker-alt fa-sm"></i></div>
                        <div style="flex:1">
                            <span class="fw-bold loc-display-name" style="font-size: 0.85rem;">Pasillo 2</span> 
                            <span class="loc-code loc-display-code">A-02-2</span>
                            <div class="loc-info-text">39 items &nbsp; 845/1000 unidades</div>
                        </div>
                        <div class="progress-container-ui">
                            <div class="progress-ui"><div class="progress-bar-ui" style="width: 85%; background: var(--ui-danger);"></div></div>
                            <span class="text-percentage">85%</span>
                        </div>
                        <div class="action-btns">
                            <i class="fas fa-pen" onclick="abrirEditar('node-A-02-2')"></i>
                            <i class="fas fa-trash" onclick="eliminarVisual('node-A-02-2')"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUbicacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="modalHeader">Nueva Ubicación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="editNodeId">
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">NOMBRE DE LA UBICACIÓN</label>
                    <input type="text" id="inputNombre" class="form-control" style="border-radius: 10px; padding: 12px;">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">CÓDIGO (SKU/ID)</label>
                    <input type="text" id="inputCodigo" class="form-control" style="border-radius: 10px; padding: 12px;">
                </div>
            </div>
            <div class="modal-footer border-0 pb-4 px-4">
                <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn-ui-primary" onclick="guardarCambiosVisuales()">Guardar Ubicación</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra-js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // --- Lógica Visual Interactiva ---

    function toggleGroup(id, icon) {
        const content = document.getElementById(id);
        const isHidden = content.style.display === "none";
        content.style.display = isHidden ? "block" : "none";
        icon.classList.toggle('collapsed-icon', !isHidden);
    }

    function expandAll(expand) {
        ['group-a', 'sub-a-02'].forEach(id => {
            document.getElementById(id).style.display = expand ? "block" : "none";
        });
        document.querySelectorAll('.chevron-toggle').forEach(i => {
            i.classList.toggle('collapsed-icon', !expand);
        });
    }

    // ELIMINAR (Visual)
    function eliminarVisual(nodeId) {
        Swal.fire({
            title: '¿Eliminar ubicación?',
            text: "Se borrará de la vista actual.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0061f2',
            cancelButtonColor: '#e74a3b',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const element = document.getElementById(nodeId);
                element.style.opacity = '0';
                element.style.transform = 'translateX(20px)';
                setTimeout(() => element.remove(), 300);
                Swal.fire('¡Listo!', 'Ubicación eliminada visualmente.', 'success');
            }
        });
    }

    // ABRIR EDITAR (Cargar datos al modal)
    function abrirEditar(nodeId) {
        const node = document.getElementById(nodeId);
        const nombreActual = node.querySelector('.loc-display-name').innerText;
        const codigoActual = node.querySelector('.loc-display-code').innerText;

        document.getElementById('modalHeader').innerText = "Editar Ubicación";
        document.getElementById('editNodeId').value = nodeId;
        document.getElementById('inputNombre').value = nombreActual;
        document.getElementById('inputCodigo').value = codigoActual;

        new bootstrap.Modal(document.getElementById('modalUbicacion')).show();
    }

    function abrirModalCrear() {
        document.getElementById('modalHeader').innerText = "Nueva Ubicación";
        document.getElementById('editNodeId').value = "";
        document.getElementById('inputNombre').value = "";
        document.getElementById('inputCodigo').value = "";
        new bootstrap.Modal(document.getElementById('modalUbicacion')).show();
    }

    // GUARDAR (Actualizar la vista)
    function guardarCambiosVisuales() {
        const nodeId = document.getElementById('editNodeId').value;
        const nuevoNombre = document.getElementById('inputNombre').value;
        const nuevoCodigo = document.getElementById('inputCodigo').value;

        if(!nuevoNombre || !nuevoCodigo) return Swal.fire('Error', 'Completa los campos', 'error');

        if(nodeId) {
            // Es edición
            const node = document.getElementById(nodeId);
            node.querySelector('.loc-display-name').innerText = nuevoNombre;
            node.querySelector('.loc-display-code').innerText = nuevoCodigo;
            
            // Efecto de brillo para indicar cambio
            node.style.backgroundColor = '#eef2ff';
            setTimeout(() => node.style.backgroundColor = '', 1000);
        }

        bootstrap.Modal.getInstance(document.getElementById('modalUbicacion')).hide();
        Swal.fire('Guardado', 'Cambios aplicados visualmente.', 'success');
    }
</script>
@endsection