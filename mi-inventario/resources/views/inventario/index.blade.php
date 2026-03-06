@extends('layouts.app')

@section('title', 'Gestión de Inventario - Universal Inventory')

@section('extra-css')
<style>
    .filter-bar {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        margin-bottom: 25px;
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        align-items: center;
    }

    .filter-bar input,
    .filter-bar select {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 13px;
    }

    .filter-bar button {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.3s ease;
    }

    .filter-bar button:hover {
        background: linear-gradient(135deg, #152d54 0%, #1d3f70 100%);
        transform: translateY(-2px);
    }

    .export-btn {
        background: white;
        color: #1e3c72;
        border: 1px solid #e9ecef;
        padding: 10px 20px;
        font-weight: 600;
        cursor: pointer;
        font-size: 13px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .export-btn:hover {
        background: #f8f9fa;
    }

    .stat-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .stat-item {
        background: white;
        border-radius: 12px;
        padding: 15px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        text-align: center;
    }

    .stat-item-number {
        font-size: 28px;
        font-weight: 700;
        color: #1e3c72;
        margin: 8px 0;
    }

    .stat-item-label {
        font-size: 11px;
        color: #6c757d;
        text-transform: uppercase;
        font-weight: 600;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-badge.in-stock {
        background-color: #d4edda;
        color: #155724;
    }

    .status-badge.low {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-badge.out-stock {
        background-color: #f8d7da;
        color: #721c24;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: none;
        background: #f0f4f8;
        color: #1e3c72;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .action-btn:hover {
        background: #e0e7f1;
        transform: translateY(-2px);
    }

    .action-btn.delete:hover {
        background: #f8d7da;
        color: #dc3545;
    }

    .add-product-btn {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 12px 24px;
        font-weight: 600;
        cursor: pointer;
        float: right;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .add-product-btn:hover {
        background: linear-gradient(135deg, #152d54 0%, #1d3f70 100%);
        transform: translateY(-2px);
    }

    .table-wrapper {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        overflow-x: auto;
    }
</style>
@endsection

@section('content')
<h2 style="font-size: 24px; font-weight: 700; color: #1e3c72; margin-bottom: 8px;">Gestión de Inventario</h2>
<p style="color: #6c757d; font-size: 13px; margin-bottom: 25px;">Control y administración de productos</p>

<!-- Stats -->
<div class="stat-row">
    <div class="stat-item">
        <div style="width: 40px; height: 40px; background: #cfe2ff; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; color: #0d6efd; font-size: 18px;">
            <i class="fas fa-cube"></i>
        </div>
        <div class="stat-item-number">8</div>
        <div class="stat-item-label">Total Items</div>
    </div>
    <div class="stat-item">
        <div style="width: 40px; height: 40px; background: #d4edda; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; color: #28a745; font-size: 18px;">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-item-number">5</div>
        <div class="stat-item-label">En Stock</div>
    </div>
    <div class="stat-item">
        <div style="width: 40px; height: 40px; background: #fff3cd; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; color: #ff6b35; font-size: 18px;">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div class="stat-item-number">2</div>
        <div class="stat-item-label">Stock Bajo</div>
    </div>
    <div class="stat-item">
        <div style="width: 40px; height: 40px; background: #f8d7da; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; color: #dc3545; font-size: 18px;">
            <i class="fas fa-times-circle"></i>
        </div>
        <div class="stat-item-number">1</div>
        <div class="stat-item-label">Sin Stock</div>
    </div>
    <div class="stat-item">
        <div style="width: 40px; height: 40px; background: #d1ecf1; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; color: #17a2b8; font-size: 18px;">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-item-number">$176K</div>
        <div class="stat-item-label">Valor Total</div>
    </div>
</div>

<!-- Filter Bar -->
<div class="filter-bar">
    <input type="text" placeholder="Buscar por nombre o SKU..." style="flex: 1; min-width: 250px;">
    <button onclick="alert('Filtro aplicado')">Todos</button>
    <button onclick="alert('Filtro aplicado')">Electrónicos</button>
    <button onclick="alert('Filtro aplicado')">Mobiliario</button>
    <button onclick="alert('Filtro aplicado')">Papelería</button>
    <button onclick="alert('Filtro aplicado')">Equipamiento</button>
    <button class="export-btn" onclick="alert('Exportando...')">
        <i class="fas fa-download"></i> Exportar
    </button>
</div>

<button class="add-product-btn" onclick="alert('Formulario para agregar producto')">
    <i class="fas fa-plus"></i> Agregar Producto
</button>

<!-- Table -->
<div class="table-wrapper">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th>Producto</th>
                <th>SKU</th>
                <th>Categoría</th>
                <th>Stock</th>
                <th>Ubicación</th>
                <th>Valor Unit.</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 36px; height: 36px; background: #e9ecef; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #666; font-size: 16px;">
                            <i class="fas fa-laptop"></i>
                        </div>
                        <span style="font-weight: 600; color: #333;">Laptop Dell XPS 15</span>
                    </div>
                </td>
                <td>LPT-001</td>
                <td><span style="background: #e3f2fd; color: #1976d2; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">Electrónicos</span></td>
                <td>45</td>
                <td>A-12-3</td>
                <td>$1299</td>
                <td><span class="status-badge in-stock">En Stock</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn" onclick="alert('Ver detalles')" title="Ver">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="action-btn" onclick="alert('Editar producto')" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn delete" onclick="alert('Producto eliminado')" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 36px; height: 36px; background: #e9ecef; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #666; font-size: 16px;">
                            <i class="fas fa-chair"></i>
                        </div>
                        <span style="font-weight: 600; color: #333;">Silla Ergonómica Pro</span>
                    </div>
                </td>
                <td>FUR-023</td>
                <td><span style="background: #f3e5f5; color: #7b1fa2; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">Mobiliario</span></td>
                <td>8</td>
                <td>B-05-1</td>
                <td>$450</td>
                <td><span class="status-badge low">Stock Bajo</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn" onclick="alert('Ver detalles')" title="Ver">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="action-btn" onclick="alert('Editar producto')" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn delete" onclick="alert('Producto eliminado')" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

@endsection