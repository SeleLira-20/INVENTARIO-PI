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
        transition: transform 0.2s;
    }
    
    .order-card:hover {
        transform: translateY(-2px);
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
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 15px;
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
        padding: 0;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
        background: white;
        border-radius: 12px;
    }

    .empty-state-icon {
        font-size: 48px;
        margin-bottom: 15px;
        opacity: 0.3;
    }
</style>
@endsection

@section('content')
<h2 style="font-size: 24px; font-weight: 700; color: #1e3c72; margin-bottom: 8px;">Gestión de Picking</h2>
<p style="color: #6c757d; font-size: 13px; margin-bottom: 25px;">Control de órdenes de recolección y ubicaciones</p>

<div class="stat-badges">
    <div class="stat-badge">
        <div style="width: 36px; height: 36px; background: #cfe2ff; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; color: #0d6efd;">
            <i class="fas fa-list"></i>
        </div>
        <div class="stat-badge-number">4</div>
        <div class="stat-badge-label">Total Órdenes</div>
    </div>
    <div class="stat-badge">
        <div style="width: 36px; height: 36px; background: #e3f2fd; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; color: #1976d2;">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-badge-number">2</div>
        <div class="stat-badge-label">Pendientes</div>
    </div>
    <div class="stat-badge">
        <div style="width: 36px; height: 36px; background: #fff3cd; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; color: #856404;">
            <i class="fas fa-hourglass-half"></i>
        </div>
        <div class="stat-badge-number">1</div>
        <div class="stat-badge-label">En Proceso</div>
    </div>
    <div class="stat-badge">
        <div style="width: 36px; height: 36px; background: #d4edda; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; color: #155724;">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-badge-number">1</div>
        <div class="stat-badge-label">Completadas</div>
    </div>
</div>

<div class="tabs-container">
    <div class="tabs">
        <div class="tab active" data-filter="all">
            <i class="fas fa-list"></i> Todos
        </div>
        <div class="tab" data-filter="status-pending">
            <i class="fas fa-clock"></i> Pendientes
        </div>
        <div class="tab" data-filter="status-in-process">
            <i class="fas fa-hourglass-half"></i> En Proceso
        </div>
        <div class="tab" data-filter="status-completed">
            <i class="fas fa-check-circle"></i> Completadas
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
    <div id="orders-list">
        <h4 style="font-weight: 700; color: #1e3c72; margin-bottom: 20px;">Órdenes de Picking</h4>
        
        <div class="order-card" data-status="status-in-process">
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
                    <div class="info-value">2026-02-17</div>
                </div>
            </div>
            <button class="order-details-toggle">Ver detalles</button>
        </div>

        <div class="order-card" data-status="status-pending">
            <div class="order-header">
                <span class="order-id">PCK-2026-002</span>
                <span class="order-status-badge status-pending">Pendiente</span>
            </div>
            <div class="order-info">
                <div class="info-item">
                    <div class="info-label">Responsable</div>
                    <div class="info-value">Sin asignar</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Fecha</div>
                    <div class="info-value">2026-02-17</div>
                </div>
            </div>
            <button class="order-details-toggle">Ver detalles</button>
        </div>

        <div class="order-card" data-status="status-completed">
            <div class="order-header">
                <span class="order-id">PCK-2026-003</span>
                <span class="order-status-badge status-completed">Completada</span>
            </div>
            <div class="order-info">
                <div class="info-item">
                    <div class="info-label">Responsable</div>
                    <div class="info-value">Ana Martínez</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Fecha</div>
                    <div class="info-value">2026-02-16</div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tab');
    const cards = document.querySelectorAll('.order-card');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Remover clase activa de todas las tabs y ponerla en la seleccionada
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            const filter = tab.getAttribute('data-filter');

            cards.forEach(card => {
                const status = card.getAttribute('data-status');
                
                if (filter === 'all' || status === filter) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
</script>

@endsection