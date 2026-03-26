@extends('layouts.app')

@section('extra-css')
<style>
    /* Grid de estadísticas restaurado */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.25rem;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .stat-icon {
        width: 42px; height: 42px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        margin-right: 12px; font-size: 1.1rem;
    }

    /* Colores oficiales de las tarjetas */
    .icon-total { background: #eff6ff; color: #2563eb; }
    .icon-stock { background: #f0fdf4; color: #16a34a; }
    .icon-bajo  { background: #fffbeb; color: #d97706; }
    .icon-sin   { background: #fef2f2; color: #dc2626; }
    .icon-valor { background: #f0fdfa; color: #0d9488; }

    .btn-add-inventory {
        background-color: #2563eb;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
    }

    .inventory-container {
        background: white;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 20px;
    }

    .search-input {
        border-radius: 10px;
        border: 1px solid #d1d5db;
        padding: 8px 15px 8px 35px;
        width: 280px;
    }

    .btn-pill {
        border-radius: 8px;
        background: #f3f4f6;
        color: #4b5563;
        font-weight: 600;
        margin-right: 5px;
        padding: 6px 15px;
        border: none;
    }

    .btn-pill.active { background: #2563eb; color: white; }
    
    .badge-status { padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 0.8rem; }

    /* Modal de confirmación de eliminación */
    #modalConfirmDelete .modal-content {
        border: 0;
        box-shadow: 0 10px 40px rgba(0,0,0,0.12);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Gestión de Inventario</h2>
            <p class="text-muted">Control y administración de productos</p>
        </div>
        <button class="btn-add-inventory" data-bs-toggle="modal" data-bs-target="#modalProducto" onclick="abrirModalNuevo()">
            <i class="fas fa-plus me-2"></i> Agregar Producto
        </button>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon icon-total"><i class="fas fa-box"></i></div>
            <div><small class="text-muted d-block">Total Items</small><span class="h5 fw-bold mb-0">8</span></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-stock"><i class="fas fa-check-circle"></i></div>
            <div><small class="text-muted d-block">En Stock</small><span class="h5 fw-bold mb-0">5</span></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-bajo"><i class="fas fa-exclamation-triangle"></i></div>
            <div><small class="text-muted d-block">Stock Bajo</small><span class="h5 fw-bold mb-0">2</span></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-sin"><i class="fas fa-times-circle"></i></div>
            <div><small class="text-muted d-block">Sin Stock</small><span class="h5 fw-bold mb-0">1</span></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-valor"><i class="fas fa-chart-line"></i></div>
            <div><small class="text-muted d-block">Valor Total</small><span class="h5 fw-bold mb-0">$176K</span></div>
        </div>
    </div>

    <div class="inventory-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <div class="position-relative me-3">
                    <i class="fas fa-search position-absolute text-muted" style="left: 12px; top: 12px;"></i>
                    <input type="text" id="searchInput" class="search-input" placeholder="Buscar por nombre o SKU...">
                </div>
                <button class="btn-pill active filter-btn" data-category="all">Todos</button>
                <button class="btn-pill filter-btn" data-category="Electrónicos">Electrónicos</button>
                <button class="btn-pill filter-btn" data-category="Mobiliario">Mobiliario</button>
            </div>
            <div>
                <button class="btn btn-light border fw-bold text-muted me-2"><i class="fas fa-filter"></i> Filtros</button>
                <button class="btn btn-light border fw-bold text-muted"><i class="fas fa-download"></i> Exportar</button>
            </div>
        </div>

        <table class="table align-middle" id="inventoryTable">
            <thead class="bg-light">
                <tr>
                    <th>Producto</th>
                    <th>SKU</th>
                    <th>Categoría</th>
                    <th>Stock</th>
                    <th>Valor Unit.</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr data-category="Electrónicos">
                    <td><strong>Laptop Dell XPS 15</strong></td>
                    <td><span class="text-primary fw-bold">LPT-001</span></td>
                    <td>Electrónicos</td>
                    <td>45</td>
                    <td>$1299</td>
                    <td><span class="badge-status bg-success-subtle text-success">En Stock</span></td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-light border text-primary edit-btn"><i class="far fa-edit"></i></button>
                        <button class="btn btn-sm btn-light border text-danger delete-btn"><i class="far fa-trash-alt"></i></button>
                    </td>
                </tr>
                <tr data-category="Mobiliario">
                    <td><strong>Silla Ergonómica Pro</strong></td>
                    <td><span class="text-primary fw-bold">FUR-023</span></td>
                    <td>Mobiliario</td>
                    <td>8</td>
                    <td>$450</td>
                    <td><span class="badge-status bg-warning-subtle text-warning">Stock Bajo</span></td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-light border text-primary edit-btn"><i class="far fa-edit"></i></button>
                        <button class="btn btn-sm btn-light border text-danger delete-btn"><i class="far fa-trash-alt"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- ===== MODAL AGREGAR / EDITAR (mismo formato) ===== --}}
<div class="modal fade" id="modalProducto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 bg-light">
                <h5 class="modal-title fw-bold" id="modalProductoTitulo">Detalles del Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="productoForm">
                    {{-- Campo oculto para identificar la fila que se edita --}}
                    <input type="hidden" id="editRowIndex" value="">

                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold">Nombre</label>
                        <input type="text" id="inputNombre" class="form-control" placeholder="Ej: Monitor 4K">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted fw-bold">SKU</label>
                            <input type="text" id="inputSKU" class="form-control" placeholder="MNT-001">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted fw-bold">Categoría</label>
                            <select id="inputCategoria" class="form-select">
                                <option>Electrónicos</option>
                                <option>Mobiliario</option>
                                <option>Papelería</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted fw-bold">Stock</label>
                            <input type="number" id="inputStock" class="form-control" placeholder="0" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted fw-bold">Valor Unitario</label>
                            <input type="text" id="inputValor" class="form-control" placeholder="$0">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary fw-bold px-4" id="btnGuardar">Guardar</button>
            </div>
        </div>
    </div>
</div>

{{-- ===== MODAL CONFIRMAR ELIMINACIÓN ===== --}}
<div class="modal fade" id="modalConfirmDelete" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 bg-light">
                <h5 class="modal-title fw-bold">Eliminar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3" style="font-size: 2.5rem; color: #dc2626;">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <p class="fw-bold fs-5 mb-1">¿Deseas eliminar este producto?</p>
                <p class="text-muted mb-0" id="deleteProductoNombre"></p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger fw-bold px-4" id="btnConfirmarEliminar">Eliminar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra-js')
<script>
    // ─── Referencia a la fila activa ──────────────────────────────────────────
    let filaActiva = null;

    // ─── Función: determinar badge de estado según stock ─────────────────────
    function getBadgeEstado(stock) {
        stock = parseInt(stock);
        if (stock <= 0)  return '<span class="badge-status bg-danger-subtle text-danger">Sin Stock</span>';
        if (stock <= 10) return '<span class="badge-status bg-warning-subtle text-warning">Stock Bajo</span>';
        return '<span class="badge-status bg-success-subtle text-success">En Stock</span>';
    }

    // ─── Abrir modal en modo NUEVO ────────────────────────────────────────────
    function abrirModalNuevo() {
        document.getElementById('modalProductoTitulo').textContent = 'Agregar Producto';
        document.getElementById('productoForm').reset();
        document.getElementById('editRowIndex').value = '';
        filaActiva = null;
    }

    // ─── Abrir modal en modo EDITAR ───────────────────────────────────────────
    function abrirModalEditar(fila) {
        filaActiva = fila;
        const celdas = fila.querySelectorAll('td');

        document.getElementById('modalProductoTitulo').textContent = 'Editar Producto';
        document.getElementById('inputNombre').value   = celdas[0].querySelector('strong').textContent.trim();
        document.getElementById('inputSKU').value      = celdas[1].querySelector('span').textContent.trim();
        document.getElementById('inputStock').value    = celdas[3].textContent.trim();
        document.getElementById('inputValor').value    = celdas[4].textContent.trim();

        // Seleccionar la categoría correcta
        const cat = celdas[2].textContent.trim();
        const selectCat = document.getElementById('inputCategoria');
        for (let opt of selectCat.options) {
            opt.selected = (opt.value === cat);
        }

        const modal = new bootstrap.Modal(document.getElementById('modalProducto'));
        modal.show();
    }

    // ─── Guardar (nuevo o editar) ─────────────────────────────────────────────
    document.getElementById('btnGuardar').addEventListener('click', function () {
        const nombre   = document.getElementById('inputNombre').value.trim();
        const sku      = document.getElementById('inputSKU').value.trim();
        const categoria = document.getElementById('inputCategoria').value;
        const stock    = document.getElementById('inputStock').value.trim();
        const valor    = document.getElementById('inputValor').value.trim();

        if (!nombre || !sku) {
            alert('Por favor completa al menos el Nombre y el SKU.');
            return;
        }

        const badge = getBadgeEstado(stock || 0);

        if (filaActiva) {
            // ── Modo edición: actualizar celdas de la fila existente ──
            const celdas = filaActiva.querySelectorAll('td');
            celdas[0].innerHTML = `<strong>${nombre}</strong>`;
            celdas[1].innerHTML = `<span class="text-primary fw-bold">${sku}</span>`;
            celdas[2].textContent = categoria;
            celdas[3].textContent = stock;
            celdas[4].textContent = valor;
            celdas[5].innerHTML   = badge;
            filaActiva.dataset.category = categoria;
        } else {
            // ── Modo nuevo: agregar fila a la tabla ──
            const tbody = document.querySelector('#inventoryTable tbody');
            const nuevaFila = document.createElement('tr');
            nuevaFila.dataset.category = categoria;
            nuevaFila.innerHTML = `
                <td><strong>${nombre}</strong></td>
                <td><span class="text-primary fw-bold">${sku}</span></td>
                <td>${categoria}</td>
                <td>${stock}</td>
                <td>${valor}</td>
                <td>${badge}</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-light border text-primary edit-btn"><i class="far fa-edit"></i></button>
                    <button class="btn btn-sm btn-light border text-danger delete-btn"><i class="far fa-trash-alt"></i></button>
                </td>`;
            tbody.appendChild(nuevaFila);
            bindBotones(nuevaFila); // registrar eventos en la nueva fila
        }

        bootstrap.Modal.getInstance(document.getElementById('modalProducto')).hide();
    });

    // ─── Lógica de eliminación ────────────────────────────────────────────────
    let filaAEliminar = null;

    function abrirModalEliminar(fila) {
        filaAEliminar = fila;
        const nombre = fila.querySelector('td strong').textContent.trim();
        document.getElementById('deleteProductoNombre').textContent = nombre;
        const modal = new bootstrap.Modal(document.getElementById('modalConfirmDelete'));
        modal.show();
    }

    document.getElementById('btnConfirmarEliminar').addEventListener('click', function () {
        if (filaAEliminar) {
            filaAEliminar.remove();
            filaAEliminar = null;
        }
        bootstrap.Modal.getInstance(document.getElementById('modalConfirmDelete')).hide();
    });

    // ─── Bind de botones (editar / eliminar) ──────────────────────────────────
    function bindBotones(scope) {
        scope.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                abrirModalEditar(this.closest('tr'));
            });
        });
        scope.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                abrirModalEliminar(this.closest('tr'));
            });
        });
    }

    // Inicializar en las filas existentes
    bindBotones(document.getElementById('inventoryTable'));

    // ─── Buscador ─────────────────────────────────────────────────────────────
    document.getElementById('searchInput').addEventListener('keyup', function () {
        const val = this.value.toLowerCase();
        document.querySelectorAll('#inventoryTable tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(val) ? '' : 'none';
        });
    });

    // ─── Filtros de categoría ─────────────────────────────────────────────────
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const cat = this.dataset.category;
            document.querySelectorAll('#inventoryTable tbody tr').forEach(row => {
                row.style.display = (cat === 'all' || row.dataset.category === cat) ? '' : 'none';
            });
        });
    });
</script>
@endsection