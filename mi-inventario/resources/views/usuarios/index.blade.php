@extends('layouts.app')
@section('title', 'Gestión de Usuarios - Universal Inventory')
@section('content')
<h2 style="font-size: 24px; font-weight: 700; color: #1e3c72; margin-bottom: 25px;">
    <i class="fas fa-users"></i> Gestión de Usuarios
</h2>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 25px;">
    <div style="background: white; padding: 15px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); text-align: center;">
        <div style="font-size: 28px; font-weight: 700; color: #0d6efd;">6</div>
        <div style="font-size: 11px; color: #6c757d; margin-top: 8px; text-transform: uppercase; font-weight: 600;">Total Usuarios</div>
    </div>
    <div style="background: white; padding: 15px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); text-align: center;">
        <div style="font-size: 28px; font-weight: 700; color: #28a745;">5</div>
        <div style="font-size: 11px; color: #6c757d; margin-top: 8px; text-transform: uppercase; font-weight: 600;">Activos</div>
    </div>
    <div style="background: white; padding: 15px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); text-align: center;">
        <div style="font-size: 28px; font-weight: 700; color: #dc3545;">1</div>
        <div style="font-size: 11px; color: #6c757d; margin-top: 8px; text-transform: uppercase; font-weight: 600;">Inactivos</div>
    </div>
    <div style="background: white; padding: 15px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); text-align: center;">
        <div style="font-size: 28px; font-weight: 700; color: #ff9800;">1</div>
        <div style="font-size: 11px; color: #6c757d; margin-top: 8px; text-transform: uppercase; font-weight: 600;">Administradores</div>
    </div>
</div>

<h4 style="font-weight: 700; color: #1e3c72; margin-bottom: 15px;">Roles y Permisos</h4>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 25px;">
    <div style="background: white; padding: 15px; border-radius: 12px; border-left: 3px solid #dc3545;">
        <div style="font-weight: 600; color: #333; margin-bottom: 8px;">
            <i class="fas fa-crown"></i> Administrador
        </div>
        <ul style="font-size: 11px; color: #666; margin: 0; padding-left: 20px;">
            <li>Acceso total</li>
            <li>Gestión de usuarios</li>
            <li>Configuración</li>
        </ul>
    </div>
    <div style="background: white; padding: 15px; border-radius: 12px; border-left: 3px solid #0d6efd;">
        <div style="font-weight: 600; color: #333; margin-bottom: 8px;">
            <i class="fas fa-briefcase"></i> Gerente
        </div>
        <ul style="font-size: 11px; color: #666; margin: 0; padding-left: 20px;">
            <li>Gestión de inventario</li>
            <li>Reportes</li>
            <li>Aprobaciones</li>
        </ul>
    </div>
    <div style="background: white; padding: 15px; border-radius: 12px; border-left: 3px solid #28a745;">
        <div style="font-weight: 600; color: #333; margin-bottom: 8px;">
            <i class="fas fa-check-circle"></i> Operador
        </div>
        <ul style="font-size: 11px; color: #666; margin: 0; padding-left: 20px;">
            <li>Picking</li>
            <li>Movimientos</li>
            <li>Consultas</li>
        </ul>
    </div>
    <div style="background: white; padding: 15px; border-radius: 12px; border-left: 3px solid #17a2b8;">
        <div style="font-weight: 600; color: #333; margin-bottom: 8px;">
            <i class="fas fa-eye"></i> Visualizador
        </div>
        <ul style="font-size: 11px; color: #666; margin: 0; padding-left: 20px;">
            <li>Solo lectura</li>
            <li>Reportes básicos</li>
        </ul>
    </div>
</div>

<button style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; border: none; border-radius: 8px; padding: 10px 20px; font-weight: 600; cursor: pointer; float: right; margin-bottom: 20px;" onclick="alert('Crear nuevo usuario')">
    <i class="fas fa-plus"></i> Nuevo Usuario
</button>

<div style="clear: both;"></div>

<div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
    <table class="table table-hover mb-0" style="font-size: 13px;">
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Contacto</th>
                <th>Rol</th>
                <th>Departamento</th>
                <th>Último Acceso</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>John Doe</strong></td>
                <td>john.doe@empresa.com</td>
                <td><span style="background: #dcccff; color: #5e35b1; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">Administrador</span></td>
                <td>Administración</td>
                <td>2026-02-17 10:30</td>
                <td><span style="background: #d4edda; color: #155724; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">Activo</span></td>
            </tr>
            <tr>
                <td><strong>María García</strong></td>
                <td>maria.garcia@empresa.com</td>
                <td><span style="background: #cfe2ff; color: #0d6efd; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">Gerente</span></td>
                <td>Logística</td>
                <td>2026-02-17 09:15</td>
                <td><span style="background: #d4edda; color: #155724; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">Activo</span></td>
            </tr>
        </tbody>
    </table>
</div>
@endsection