@extends('layouts.app')

@section('title', 'Configuración - Universal Inventory')

@section('content')
<style>
    /* Contenedor Principal con el gris muy claro de la imagen */
    .config-page {
        background-color: #f8fafc;
        padding: 30px;
        font-family: 'Inter', 'Segoe UI', sans-serif;
    }

    .main-title { font-size: 22px; font-weight: 700; color: #1e293b; margin-bottom: 4px; }
    .main-subtitle { color: #64748b; font-size: 14px; margin-bottom: 25px; }

    /* Tarjetas con bordes sutiles */
    .config-section {
        background: white;
        border-radius: 12px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        margin-bottom: 24px;
    }

    /* Encabezados de Sección */
    .section-header { display: flex; align-items: center; margin-bottom: 24px; }
    .icon-wrapper {
        width: 36px; height: 36px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        margin-right: 12px; font-size: 16px;
    }
    .header-text h3 { font-size: 16px; font-weight: 700; color: #1e293b; margin: 0; }
    .header-text p { font-size: 12px; color: #94a3b8; margin: 0; }

    /* Formulario Estilo Captura */
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
    label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 8px; }
    .form-input {
        width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0;
        border-radius: 8px; color: #1e293b; font-size: 14px; outline: none;
    }

    /* Switches (Interruptores) */
    .switch { position: relative; display: inline-block; width: 40px; height: 20px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider {
        position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1; transition: .3s; border-radius: 20px;
    }
    .slider:before {
        position: absolute; content: ""; height: 14px; width: 14px;
        left: 3px; bottom: 3px; background-color: white; transition: .3s; border-radius: 50%;
    }
    input:checked + .slider { background-color: #2563eb; }
    input:checked + .slider:before { transform: translateX(20px); }

    /* Botones Inferiores */
    .footer-actions { display: flex; justify-content: flex-end; align-items: center; margin-top: 10px; }
    .btn-reset { background: white; border: 1px solid #e2e8f0; padding: 10px 20px; border-radius: 8px; color: #475569; font-weight: 600; cursor: pointer; margin-right: 12px; }
    .btn-save { background: #2563eb; color: white; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; }

    /* Vista Previa de Código */
    .barcode-preview { background: #f8fafc; border-radius: 8px; padding: 24px; text-align: center; border: 1px dashed #cbd5e1; margin-top: 20px; }
</style>

<div class="config-page">
    <h1 class="main-title">Configuración del Sistema</h1>
    <p class="main-subtitle">Ajustes generales y preferencias</p>

    <div class="config-section">
        <div class="section-header">
            <div class="icon-wrapper" style="background: #eff6ff; color: #3b82f6;"><i class="fas fa-cog"></i></div>
            <div class="header-text">
                <h3>Configuración General</h3>
                <p>Preferencias básicas del sistema</p>
            </div>
        </div>
        <div class="form-row">
            <div><label>Nombre de la Empresa</label><input type="text" class="form-input" value="Universal Inventory"></div>
            <div><label>Zona Horaria</label><input type="text" class="form-input" value="UTC-5 (América/Lima)"></div>
        </div>
        <div class="form-row">
            <div><label>Idioma del Sistema</label><input type="text" class="form-input" value="Español"></div>
            <div><label>Formato de Fecha</label><input type="text" class="form-input" value="DD/MM/YYYY"></div>
        </div>
        <div style="display: grid; grid-template-columns: 2fr 1.5fr 1.5fr; gap: 20px;">
            <div><label>Moneda</label><input type="text" class="form-input" value="USD - Dólar Estadounidense"></div>
            <div><label>&nbsp;</label><input type="text" class="form-input" value="$" style="text-align: center;"></div>
            <div><label>&nbsp;</label><input type="text" class="form-input" value="Posición: Antes"></div>
        </div>
    </div>

    <div class="config-section">
        <div class="section-header">
            <div class="icon-wrapper" style="background: #f0fdf4; color: #22c55e;"><i class="fas fa-bars"></i></div>
            <div class="header-text">
                <h3>Configuración de Códigos de Barras</h3>
                <p>Estándares y formatos de códigos</p>
            </div>
        </div>
        <div class="form-row">
            <div><label>Tipo de Código de Barras</label><input type="text" class="form-input" value="Code 128"></div>
            <div><label>Prefijo del SKU</label><input type="text" class="form-input" value="INV"></div>
        </div>
        <div class="form-row">
            <div><label>Longitud del Código</label><input type="text" class="form-input" value="12"></div>
            <div><label>Incluir Checksum</label><input type="text" class="form-input" value="Sí"></div>
        </div>
        <div class="barcode-preview">
            <p style="font-size: 14px; font-weight: 600; margin-bottom: 12px;">Vista Previa del Código</p>
            <div style="background: white; display: inline-block; padding: 12px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <i class="fas fa-barcode" style="font-size: 40px; color: #1e293b;"></i>
                <p style="font-family: monospace; font-size: 12px; margin: 4px 0 0 0;">INV-2026-001234</p>
            </div>
            <p style="color: #94a3b8; font-size: 11px; margin-top: 8px;">Ejemplo: INV-2026-001234</p>
        </div>
    </div>

    <div class="config-section">
        <div class="section-header">
            <div class="icon-wrapper" style="background: #fffbeb; color: #f59e0b;"><i class="fas fa-bell"></i></div>
            <div class="header-text">
                <h3>Notificaciones</h3>
                <p>Configuración de alertas y notificaciones</p>
            </div>
        </div>
        @foreach(['Alerta de stock bajo' => 'Notificar cuando el inventario alcance el mínimo', 
                  'Alerta de sin stock' => 'Notificar cuando un producto se agote',
                  'Picking completado' => 'Notificar al completar una orden de picking',
                  'Discrepancias detectadas' => 'Notificar diferencias en el inventario'] as $title => $desc)
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f8fafc;">
            <div>
                <p style="font-weight: 600; color: #1e293b; margin: 0; font-size: 14px;">{{ $title }}</p>
                <p style="color: #94a3b8; font-size: 12px; margin: 0;">{{ $desc }}</p>
            </div>
            <div style="display: flex; align-items: center; gap: 16px;">
                <i class="far fa-envelope" style="color: #94a3b8;"></i>
                <i class="fas fa-mobile-alt" style="color: #94a3b8;"></i>
                <label class="switch"><input type="checkbox" checked><span class="slider"></span></label>
            </div>
        </div>
        @endforeach
    </div>

    <div class="config-section">
        <div class="section-header">
            <div class="icon-wrapper" style="background: #fef2f2; color: #ef4444;"><i class="fas fa-shield-alt"></i></div>
            <div class="header-text">
                <h3>Seguridad</h3>
                <p>Configuración de seguridad del sistema</p>
            </div>
        </div>
        <div class="form-row">
            <div><label>Tiempo de Sesión (minutos)</label><input type="text" class="form-input" value="30"></div>
            <div><label>Intentos de Login Máximos</label><input type="text" class="form-input" value="5"></div>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 0;">
            <div>
                <p style="font-weight: 600; color: #1e293b; margin: 0; font-size: 14px;">Autenticación de Dos Factores</p>
                <p style="color: #94a3b8; font-size: 12px; margin: 0;">Requerir código adicional al iniciar sesión</p>
            </div>
            <label class="switch"><input type="checkbox"><span class="slider"></span></label>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 0;">
            <div>
                <p style="font-weight: 600; color: #1e293b; margin: 0; font-size: 14px;">Registro de Auditoría</p>
                <p style="color: #94a3b8; font-size: 12px; margin: 0;">Guardar log de todas las acciones del sistema</p>
            </div>
            <label class="switch"><input type="checkbox" checked><span class="slider"></span></label>
        </div>
    </div>

    <div class="footer-actions">
        <div style="margin-right: auto; color: #94a3b8; font-size: 12px; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-database"></i> Última actualización: 17 de febrero de 2026, 10:30 AM
        </div>
        <button class="btn-reset" onclick="alert('Restablecido')"><i class="fas fa-sync-alt" style="margin-right: 6px;"></i> Restablecer</button>
        <button class="btn-save" onclick="alert('Guardado')"><i class="fas fa-save"></i> Guardar Cambios</button>
    </div>
</div>
@endsection