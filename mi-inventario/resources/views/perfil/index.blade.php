@extends('layouts.app')
@section('title', 'Mi Perfil - Universal Inventory')
@section('content')
<h2 style="font-size: 24px; font-weight: 700; color: #1e3c72; margin-bottom: 25px;">
    <i class="fas fa-user-circle"></i> Mi Perfil
</h2>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 25px;">
    <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); text-align: center;">
        <div style="width: 100px; height: 100px; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 48px; color: white; font-weight: bold;">JD</div>
        
        <h4 style="font-weight: 700; color: #1e3c72; margin-bottom: 5px;">John Doe</h4>
        <p style="color: #6c757d; font-size: 13px; margin-bottom: 20px;">Administrador</p>

        <div style="border-top: 1px solid #e9ecef; padding-top: 20px; margin-top: 20px;">
            <div style="margin-bottom: 15px;">
                <div style="font-size: 24px; font-weight: 700; color: #1e3c72;">234</div>
                <div style="font-size: 11px; color: #6c757d; text-transform: uppercase;">Órdenes Completadas</div>
            </div>
            <div style="margin-bottom: 15px;">
                <div style="font-size: 24px; font-weight: 700; color: #1e3c72;">156</div>
                <div style="font-size: 11px; color: #6c757d; text-transform: uppercase;">Picking Realizado</div>
            </div>
            <div>
                <div style="font-size: 24px; font-weight: 700; color: #1e3c72;">45</div>
                <div style="font-size: 11px; color: #6c757d; text-transform: uppercase;">Días Activo</div>
            </div>
        </div>

        <button style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; border: none; border-radius: 8px; padding: 10px 20px; font-weight: 600; cursor: pointer; width: 100%; margin-top: 20px;" onclick="alert('Perfil actualizado')">
            <i class="fas fa-edit"></i> Editar Perfil
        </button>
    </div>

    <div>
        <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 20px;">
            <h5 style="font-weight: 700; color: #1e3c72; margin-bottom: 20px;">Información Personal</h5>
            
            <div style="display: grid; gap: 15px;">
                <div>
                    <label style="display: block; font-weight: 600; color: #333; margin-bottom: 6px; font-size: 13px;">Nombre Completo</label>
                    <input type="text" value="John Doe" style="width: 100%; padding: 10px; border: 1px solid #e9ecef; border-radius: 8px; font-size: 13px;">
                </div>

                <div>
                    <label style="display: block; font-weight: 600; color: #333; margin-bottom: 6px; font-size: 13px;">Correo Electrónico</label>
                    <input type="email" value="john.doe@empresa.com" style="width: 100%; padding: 10px; border: 1px solid #e9ecef; border-radius: 8px; font-size: 13px;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label style="display: block; font-weight: 600; color: #333; margin-bottom: 6px; font-size: 13px;">Teléfono</label>
                        <input type="tel" value="+1 234 567 8900" style="width: 100%; padding: 10px; border: 1px solid #e9ecef; border-radius: 8px; font-size: 13px;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; color: #333; margin-bottom: 6px; font-size: 13px;">Departamento</label>
                        <input type="text" value="Administración" style="width: 100%; padding: 10px; border: 1px solid #e9ecef; border-radius: 8px; font-size: 13px;">
                    </div>
                </div>

                <div>
                    <label style="display: block; font-weight: 600; color: #333; margin-bottom: 6px; font-size: 13px;">Cargo</label>
                    <input type="text" value="Director de Operaciones" style="width: 100%; padding: 10px; border: 1px solid #e9ecef; border-radius: 8px; font-size: 13px;">
                </div>
            </div>
        </div>

        <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <h5 style="font-weight: 700; color: #1e3c72; margin-bottom: 20px;">Seguridad</h5>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; color: #333; margin-bottom: 6px; font-size: 13px;">Contraseña Actual</label>
                <input type="password" placeholder="••••••••" style="width: 100%; padding: 10px; border: 1px solid #e9ecef; border-radius: 8px; font-size: 13px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; color: #333; margin-bottom: 6px; font-size: 13px;">Nueva Contraseña</label>
                <input type="password" placeholder="••••••••" style="width: 100%; padding: 10px; border: 1px solid #e9ecef; border-radius: 8px; font-size: 13px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; color: #333; margin-bottom: 6px; font-size: 13px;">Confirmar Contraseña</label>
                <input type="password" placeholder="••••••••" style="width: 100%; padding: 10px; border: 1px solid #e9ecef; border-radius: 8px; font-size: 13px;">
            </div>

            <button style="background: #dc3545; color: white; border: none; border-radius: 8px; padding: 10px 25px; font-weight: 600; cursor: pointer;" onclick="alert('Contraseña actualizada')">
                <i class="fas fa-key"></i> Cambiar Contraseña
            </button>
        </div>
    </div>
</div>
@endsection