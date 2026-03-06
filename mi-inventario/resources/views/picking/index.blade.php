@extends('layouts.app')

@section('title', 'Gestión de Picking - Universal Inventory')

@section('extra-css')
<style>
    .order-status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-pending { background: #e3f2fd; color: #1976d2; }
    .status-in-process { background: #fff3cd; color: #856404; }
    .status-completed { background: #d4edda; color: #155724; }

    .tabs-container {
        background: white;
        border-radius: 12px;
        padding: 0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        margin-bottom: 25px;
        overflow: hidden;
    }

    .tabs {
        display: flex;
        border-bottom: 2px solid #e9ecef;
    }

    .tab {
        flex: 1;
        padding: 15px;
        text-align: center;
        cursor: pointer;
        font-weight: 600;
        color: #6c757d;
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
    }

    .tab:hover {
        color: #1e3c72;
        background: #f8f9fa;
    }

    .tab.active {
        color: #1e3c72;
        border-bottom-color: #2a5298;
    }

    .order-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        margin-bottom: 15px;
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e9ecef;
    }

    .order-id {
        font-size: 16px;
        font-weight: 700;
        color: #1e3c72;
    }

    .order-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 20px;
        margin-bottom: 15px;
    }

    .info-item {
        font-size: 12px;
    }

    .info-label {
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .info-value {
        color: #333;
        font-weight: 600;
        font-size: 13px;
    }

    .stat-badges {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }

    .stat-badge {
        background: white;
        border-radius: 8px;
        padding: 12px;
        text-align: center;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
    }

    .stat-badge-number {
        font-size: 24px;
        font-weight: 700;
        color: #1e3c72;
    }

    .stat-badge-label {
        font-size: 11px;
        color: #6c757d;
        margin-top: 5px;
    }

    .order-details-toggle {
        background: none;
        border: none;
        color: #2a5298;
        cursor: pointer;
        font-weight: 600;
        font-size: 12px;
        text-decoration: underline;
    }

    .details-section {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
    }

    .details-title {
        font-weight: 700;
        color: #1e3c72;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e9ecef;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }

    .empty-state-icon {
        font-size: 48px;
        margin-bottom: 15px;
        opacity: 0.3;
    }

    .empty-state-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 5px;
        color: #333;
    }

    .action-button {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        border: none;
        border-radius: 6px;
        padding: 8px 16px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .action-button:hover {
        background: linear-gradient(135deg, #152d54 0%, #1d3f70 100%);
        transform: translateY(-2px);
    }

    .action-button-secondary {
        background: white;
        color: #1e3c72;
        border: 1px solid #e9ecef;
    }

    .action-button-secondary:hover {
        background: #f8f9fa;
    }
</style>
@endsection

@section('content')
<h2 style="font-size: 24px; font-weight: 700; color: #1e3c72; margin-bottom: 8px;">Gestión de Picking</h2>
<p style="color: #6c757d; font-size: 13px; margin-bottom: 25px;">Control de órdenes de recolección y ubicaciones</p>

<!-- Stats -->
<div class="stat-badges">
    <div class="stat-badge">
        <div style="width: 36px; height: 36px; background: #cfe2ff; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; color: #0d6efd;">
            <i class="fas fa-list"></i>
        </div>
        <div class="stat-badge-number">4</div>
        <div class="stat-badge-label">Total Órdenes</div>
    </div>
    <div class="stat-badge">
        <div style="width: 36px; height: 36px; background: #fff3cd; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; color: #ff6b35;">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-badge-number">2</div>
        <div class="stat-badge-label">Pendientes</div>
    </div>
    <div class="stat-badge">
        <div style="width: 36px; height: 36px; background: #fff3cd; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; color: #ff6b35;">
            <i class="fas fa-hourglass-half"></i>
        </div>
        <div class="stat-badge-number">1</div>
        <div class="stat-badge-label">En Proceso</div>
    </div>
    <div class="stat-badge">
        <div style="width: 36px; height: 36px; background: #d4edda; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; color: #28a745;">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-badge-number">1</div>
        <div class="stat-badge-label">Completadas</div>
    </div>
</div>

<!-- Tabs -->
<div class="tabs-container">
    <div class="tabs">
        <div class="tab active">
            <i class="fas fa-list"></i> Todos
        </div>
        <div class="tab">
            <i class="fas fa-clock"></i> Pendientes
        </div>
        <div class="tab">
            <i class="fas fa-hourglass-half"></i> En Proceso
        </div>
        <div class="tab">
            <i class="fas fa-check-circle"></i> Completadas
        </div>
    </div>
</div>

<!-- Orders Grid -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
    <div>
        <h4 style="font-weight: 700; color: #1e3c72; margin-bottom: 20px;">Órdenes de Picking</h4>
        
        <div class="order-card">
            <div class="order-header">
                <span class="order-id">PCK-2026-001</span>
                <span class="order-status-badge status-in-process">En Proceso</span>
            </div>
            <div class="order-info">
                <div class="info-item">
                    <div class="info-label">Responsable</div>
                    <div class="info-value">Carlos López</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Fecha</div>
                    <div class="info-value">2026-02-17 08:30</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Items</div>
                    <div class="info-value">5</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tiempo Est.</div>
                    <div class="info-value">15 min</div>
                </div>
            </div>
            <button class="order-details-toggle">Selecciona una orden para los detalles</button>
        </div>

        <div class="order-card">
            <div class="order-header">
                <span class="order-id">PCK-2026-002</span>
                <span class="order-status-badge status-pending">Pendiente</span>
            </div>
            <div class="order-info">
                <div class="info-item">
                    <div class="info-label">Responsable</div>
                    <div class="info-value">No asignado</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Fecha</div>
                    <div class="info-value">2026-02-17 09:00</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Items</div>
                    <div class="info-value">8</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tiempo Est.</div>
                    <div class="info-value">20 min</div>
                </div>
            </div>
            <button class="order-details-toggle">Ver detalles</button>
        </div>
    </div>

    <div>
        <h4 style="font-weight: 700; color: #1e3c72; margin-bottom: 20px;">Detalles de la Orden</h4>
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="empty-state-title">No hay orden seleccionada</div>
            <p style="font-size: 12px; color: #999; margin-top: 8px;">Selecciona una orden para ver los detalles</p>
        </div>
    </div>
</div>

@endsection