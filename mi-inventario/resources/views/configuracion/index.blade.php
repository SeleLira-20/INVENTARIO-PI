@extends('layouts.app')
@section('title', 'Configuración - Universal Inventory')
@section('content')
<h2 style="font-size: 24px; font-weight: 700; color: #1e3c72; margin-bottom: 25px;">
    <i class="fas fa-cog"></i> Configuración del Sistema
</h2>

<div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 20px;">
    <h5 style="font-weight: 700; color: #1e3c72; margin-bottom: 20px;">Configuración General</h5>
    
    <div style="margin-bottom: 25px;">
        <label style="display: block; font-weight: 600; color: #333; margin-bottom: 8px; font-size: 13px;">Nombre de la Empresa</label>
        <input type="text" value="Universal Inventory" style="width: 100%; max-width: 400px; padding: 10px; border: 1px solid #e9ecef; border-radius: 8px;">
    </div>

    <div style="margin-bottom: 25px;">
        <label style="display: block; font-weight: 600; color: #333; margin-bottom: 8px; font-size: 13px;">Zona Horaria</label>
        <select style="width: 100%; max-width: 400px; padding: 10px; border: 1px solid #e9ecef; border-radius: 8px;">
            <option>UTC-5 (América/Lima)</option>
            <option>UTC-6 (América/México)</option>
        </select>
    </div>

    <div style="margin-bottom: 25px;">
        <label style="display: block; font-weight: 600; color: #333; margin-bottom: 8px; font-size: 13px;">Idioma del Sistema</label>
        <select style="width: 100%; max-width: 400px; padding: 10px; border: 1px solid #e9ecef; border-radius: 8px;">
            <option>Español</option>
            <option>English</option>
        </select>
    </div>

    <div style="margin-bottom: 25px;">
        <label style="display: block; font-weight: 600; color: #333; margin-bottom: 8px; font-size: 13px;">Moneda</label>
        <select style="width: 100%; max-width: 400px; padding: 10px; border: 1px solid #e9ecef; border-radius: 8px;">
            <option>USD - Dólar Estadounidense</option>
            <option>PEN - Sol Peruano</option>
            <option>MXN - Peso Mexicano</option>
        </select>
    </div>

    <button style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; border: none; border-radius: 8px; padding: 10px 25px; font-weight: 600; cursor: pointer;" onclick="alert('Configuración guardada')">
        <i class="fas fa-save"></i> Guardar Cambios
    </button>
</div>

<div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
    <h5 style="font-weight: 700; color: #1e3c72; margin-bottom: 20px;">Configuración de Códigos de Barras</h5>
    
    <div style="margin-bottom: 20px;">
        <label style="display: block; font-weight: 600; color: #333; margin-bottom: 8px; font-size: 13px;">Tipo de Código de Barras</label>
        <select style="width: 100%; max-width: 400px; padding: 10px; border: 1px solid #e9ecef; border-radius: 8px;">
            <option>Code 128</option>
            <option>EAN-13</option>
        </select>
    </div>

    <div style="margin-bottom: 20px;">
        <label style="display: block; font-weight: 600; color: #333; margin-bottom: 8px; font-size: 13px;">Prefijo del SKU</label>
        <input type="text" value="INV" style="width: 100%; max-width: 400px; padding: 10px; border: 1px solid #e9ecef; border-radius: 8px;">
    </div>

    <div style="text-align: center; background: #f8f9fa; padding: 20px; border-radius: 8px; margin-top: 20px;">
        <div style="font-size: 28px; margin-bottom: 10px;">
            <svg viewBox="0 0 128 128" style="width: 100px; height: 40px;">
                <rect x="0" y="0" width="128" height="40" fill="white" stroke="black" stroke-width="1"/>
                <text x="64" y="25" text-anchor="middle" font-size="12">INV-2026-001234</text>
                <line x1="10" y1="28" x2="10" y2="38" stroke="black" stroke-width="1"/>
                <line x1="118" y1="28" x2="118" y2="38" stroke="black" stroke-width="1"/>
            </svg>
        </div>
        <p style="font-size: 12px; color: #666;">Vista previa del código</p>
    </div>

    <button style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; border: none; border-radius: 8px; padding: 10px 25px; font-weight: 600; cursor: pointer; margin-top: 20px;" onclick="alert('Configuración guardada')">
        <i class="fas fa-save"></i> Guardar Cambios
    </button>
</div>
@endsection