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
    }

    body { background-color: var(--ui-bg); font-family: 'Nunito', sans-serif; }
    .inventory-wrapper { padding: 1.5rem; }
    .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
    
    .btn-ui-primary {
        background-color: var(--ui-primary); color: white; border-radius: 10px;
        padding: 10px 20px; font-weight: 700; border: none; font-size: 0.85rem;
        transition: all 0.3s;
    }
    .btn-ui-primary:hover { opacity: 0.9; transform: translateY(-1px); }

    .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 1.5rem; }
    .stat-card-ui {
        background: white; border-radius: 15px; padding: 1.2rem;
        box-shadow: var(--ui-card-shadow); display: flex; align-items: center; gap: 1rem;
        border: 1px solid rgba(0,0,0,0.03);
    }
    .icon-shape {
        width: 42px; height: 42px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; font-size: 1.1rem;
    }

    .alert-ui-warning {
        background-color: #fffdf0; border: 1px solid #ffeeba; border-radius: 12px;
        padding: 1rem 1.5rem; margin-bottom: 2rem; display: flex; gap: 1rem; align-items: center;
    }

    .main-card-ui { background: white; border-radius: 15px; padding: 1.5rem; box-shadow: var(--ui-card-shadow); }
    
    .location-item {
        background: #f8faff; border: 1px solid #edf2f7; border-radius: 12px;
        margin-bottom: 0.8rem; padding: 0.8rem 1.2rem; display: flex; align-items: center;
        transition: all 0.2s;
    }
    .location-item:hover { background: white; border-color: var(--ui-primary); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }

    .level-1 { margin-left: 2.5rem; }
    .level-2 { margin-left: 5rem; background: #fff; }

    .progress-container-ui { flex: 1; max-width: 250px; margin: 0 1.5rem; display: flex; align-items: center; gap: 10px; }
    .progress-ui { flex: 1; height: 8px; background: #eaecf4; border-radius: 20px; overflow: hidden; }
    .progress-bar-ui { height: 100%; border-radius: 20px; }
    
    .text-percentage { font-weight: 800; font-size: 0.75rem; color: #4a5568; width: 35px; }

    .action-btns { display: flex; gap: 15px; color: #b7b9cc; }
    .action-btns i { cursor: pointer; transition: 0.2s; padding: 5px; }
    .action-btns i:hover { color: var(--ui-primary); transform: scale(1.2); }
    .action-btns i.fa-trash:hover { color: #e74a3b; }

    .bg-ui-blue { background: #e0ebff; color: #0061f2; }
    .bg-ui-orange { background: #fff4e5; color: #f6993f; }
    .bg-ui-red { background: #e74a3b; }
    
    .chevron-toggle { transition: transform 0.3s; cursor: pointer; }
</style>
@endsection

@section('content')
<div class="inventory-wrapper">
    <div class="header-flex">
        <div>
            <h2 style="font-weight: 800; color: #1a202c; margin:0;">Ubicaciones del Almacén</h2>
            <p style="color: #a0aec0; font-size: 0.9rem; margin:0;">Jerarquía y gestión de zonas</p>
        </div>
        <button class="btn-ui-primary" data-bs-toggle="modal" data-bs-target="#modalUbicacion" onclick="prepararModal('nueva')">
            <i class="fas fa-plus"></i> Nueva Ubicación
        </button>
    </div>

    <div class="stats-row">
        <div class="stat-card-ui">
            <div class="icon-shape bg-ui-blue"><i class="fas fa-warehouse"></i></div>
            <div><p style="margin:0; font-size: 0.75rem; color: #a0aec0; font-weight:700;">Almacenes</p><h4 style="margin:0; font-weight:800;">2</h4></div>
        </div>
        <div class="stat-card-ui">
            <div class="icon-shape" style="background: #e6fffa; color: #38b2ac;"><i class="fas fa-box"></i></div>
            <div><p style="margin:0; font-size: 0.75rem; color: #a0aec0; font-weight:700;">Total Items</p><h4 style="margin:0; font-weight:800;">401</h4></div>
        </div>
        <div class="stat-card-ui">
            <div class="icon-shape" style="background: #fffaf0; color: #ed8936;"><i class="fas fa-hdd"></i></div>
            <div><p style="margin:0; font-size: 0.75rem; color: #a0aec0; font-weight:700;">Capacidad Total</p><h4 style="margin:0; font-weight:800;">15,000</h4></div>
        </div>
        <div class="stat-card-ui">
            <div class="icon-shape" style="background: #fff5f5; color: #f56565;"><i class="fas fa-chart-pie"></i></div>
            <div><p style="margin:0; font-size: 0.75rem; color: #a0aec0; font-weight:700;">Ocupación</p><h4 style="margin:0; font-weight:800;">67.3%</h4></div>
        </div>
    </div>

    <div class="main-card-ui">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h5 style="font-weight: 800; margin:0;">Estructura de Ubicaciones</h5>
            <div>
                <button class="btn btn-light btn-sm" style="font-size: 0.7rem; font-weight:700;" onclick="expandAll(true)">Expandir todo</button>
                <button class="btn btn-light btn-sm" style="font-size: 0.7rem; font-weight:700;" onclick="expandAll(false)">Contraer todo</button>
            </div>
        </div>

        <div class="location-group mb-4" id="item-warehouse-1">
            <div class="location-item">
                <i class="fas fa-chevron-right chevron-toggle" onclick="toggleLevel('group-a', this)"></i>
                <div class="icon-shape bg-ui-blue" style="width:32px; height:32px; margin: 0 15px;"><i class="fas fa-warehouse" style="font-size: 0.8rem;"></i></div>
                <div style="flex:1">
                    <span class="loc-name" style="font-weight: 800; font-size: 0.9rem;">Almacén Principal</span> 
                    <span class="badge bg-light text-muted loc-code">A</span>
                </div>
                <div class="progress-container-ui">
                    <div class="progress-ui"><div class="progress-bar-ui" style="width: 68%; background: #ed8936;"></div></div>
                    <span class="text-percentage">68%</span>
                </div>
                <div class="action-btns">
                    <i class="fas fa-pen" onclick="editarUbicacion('Almacén Principal', 'A')"></i>
                    <i class="fas fa-trash" onclick="eliminarUbicacion('item-warehouse-1')"></i>
                </div>
            </div>

            <div id="group-a" style="display:none;">
                <div class="location-item level-1" id="item-zone-1">
                    <i class="fas fa-chevron-right chevron-toggle" onclick="toggleLevel('sub-a-02', this)"></i>
                    <div class="icon-shape bg-ui-orange" style="width:32px; height:32px; margin: 0 15px;"><i class="fas fa-layer-group" style="font-size: 0.8rem;"></i></div>
                    <div style="flex:1">
                        <span class="loc-name" style="font-weight: 800; font-size: 0.9rem;">Zona Mobiliario</span> 
                        <span class="badge bg-light text-muted loc-code">A-02</span>
                    </div>
                    <div class="action-btns">
                        <i class="fas fa-pen" onclick="editarUbicacion('Zona Mobiliario', 'A-02')"></i>
                        <i class="fas fa-trash" onclick="eliminarUbicacion('item-zone-1')"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="location-group" id="item-warehouse-2">
            <div class="location-item">
                <i class="fas fa-chevron-right chevron-toggle" onclick="toggleLevel('group-b', this)"></i>
                <div class="icon-shape" style="width:32px; height:32px; margin: 0 15px; background: #e6fffa; color: #38b2ac;"><i class="fas fa-warehouse" style="font-size: 0.8rem;"></i></div>
                <div style="flex:1">
                    <span class="loc-name" style="font-weight: 800; font-size: 0.9rem;">Almacén Secundario</span> 
                    <span class="badge bg-light text-muted loc-code">B</span>
                </div>
                <div class="progress-container-ui">
                    <div class="progress-ui"><div class="progress-bar-ui" style="width: 25%; background: #38b2ac;"></div></div>
                    <span class="text-percentage">25%</span>
                </div>
                <div class="action-btns">
                    <i class="fas fa-pen" onclick="editarUbicacion('Almacén Secundario', 'B')"></i>
                    <i class="fas fa-trash" onclick="eliminarUbicacion('item-warehouse-2')"></i>
                </div>
            </div>
            <div id="group-b" style="display:none; padding: 10px 0 10px 40px; color: #a0aec0; font-size: 0.8rem;">
                No hay sub-ubicaciones registradas.
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUbicacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title" id="modalTitle" style="font-weight: 800;">Nueva Ubicación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formUbicacion">
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.8rem; font-weight: 700; color: #a0aec0;">NOMBRE DE LA UBICACIÓN</label>
                        <input type="text" id="locName" class="form-control" placeholder="Ej: Pasillo Norte" style="border-radius: 10px; padding: 12px;" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.8rem; font-weight: 700; color: #a0aec0;">CÓDIGO (SKU/ID)</label>
                        <input type="text" id="locCode" class="form-control" placeholder="Ej: A-01" style="border-radius: 10px; padding: 12px;" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.8rem; font-weight: 700; color: #a0aec0;">UBICACIÓN PADRE (Opcional)</label>
                        <select class="form-select" style="border-radius: 10px; padding: 12px;">
                            <option value="">Ninguna (Es Almacén Principal)</option>
                            <option value="1">Almacén Principal</option>
                            <option value="2">Almacén Secundario</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pb-4 px-4">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 10px; font-weight: 700;">Cancelar</button>
                <button type="button" class="btn-ui-primary" onclick="guardarCambios()">Guardar Ubicación</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('extra-js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Toggle de niveles
    function toggleLevel(id, icon) {
        const content = document.getElementById(id);
        if (content.style.display === "none" || content.style.display === "") {
            content.style.display = "block";
            icon.style.transform = "rotate(90deg)";
        } else {
            content.style.display = "none";
            icon.style.transform = "rotate(0deg)";
        }
    }

    function expandAll(expand) {
        const groups = ['group-a', 'sub-a-02', 'group-b'];
        groups.forEach(id => {
            const el = document.getElementById(id);
            if(el) el.style.display = expand ? "block" : "none";
        });
        document.querySelectorAll('.chevron-toggle').forEach(i => {
            i.style.transform = expand ? "rotate(90deg)" : "rotate(0deg)";
        });
    }

    // Lógica del Modal
    function prepararModal(tipo) {
        const title = document.getElementById('modalTitle');
        if (tipo === 'nueva') {
            title.innerText = 'Nueva Ubicación';
            document.getElementById('formUbicacion').reset();
        }
    }

    function editarUbicacion(nombre, codigo) {
        document.getElementById('modalTitle').innerText = 'Editar Ubicación';
        document.getElementById('locName').value = nombre;
        document.getElementById('locCode').value = codigo;
        // Abrir modal manualmente
        var myModal = new bootstrap.Modal(document.getElementById('modalUbicacion'));
        myModal.show();
    }

    function eliminarUbicacion(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Se eliminará esta ubicación y sus niveles inferiores.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0061f2',
            cancelButtonColor: '#e74a3b',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(id).remove();
                Swal.fire('Eliminado', 'La ubicación ha sido borrada.', 'success');
            }
        });
    }

    function guardarCambios() {
        // Aquí iría tu petición AJAX a Laravel
        const name = document.getElementById('locName').value;
        if(name === "") return alert("Por favor completa los datos");
        
        Swal.fire({
            title: '¡Éxito!',
            text: 'Ubicación procesada correctamente (Simulación)',
            icon: 'success',
            confirmButtonColor: '#0061f2'
        });
        
        // Cerrar modal
        var modalEl = document.getElementById('modalUbicacion');
        var modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();
    }
</script>
@endsection