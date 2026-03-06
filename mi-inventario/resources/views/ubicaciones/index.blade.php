# UBICACIONES_INDEX.BLADE.PHP
@extends('layouts.app')
@section('title', 'Ubicaciones - Universal Inventory')
@section('content')
<h2 style="font-size: 24px; font-weight: 700; color: #1e3c72; margin-bottom: 25px;">
    <i class="fas fa-map-marker-alt"></i> Ubicaciones del Almacén
</h2>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); text-align: center;">
        <div style="font-size: 28px; font-weight: 700; color: #1e3c72; margin-bottom: 8px;">2</div>
        <div style="font-size: 12px; color: #6c757d; text-transform: uppercase; font-weight: 600;">Almacenes</div>
    </div>
    <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); text-align: center;">
        <div style="font-size: 28px; font-weight: 700; color: #1e3c72; margin-bottom: 8px;">401</div>
        <div style="font-size: 12px; color: #6c757d; text-transform: uppercase; font-weight: 600;">Total Items</div>
    </div>
    <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); text-align: center;">
        <div style="font-size: 28px; font-weight: 700; color: #1e3c72; margin-bottom: 8px;">15,000</div>
        <div style="font-size: 12px; color: #6c757d; text-transform: uppercase; font-weight: 600;">Capacidad Total</div>
    </div>
    <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); text-align: center;">
        <div style="font-size: 28px; font-weight: 700; color: #28a745; margin-bottom: 8px;">67.3%</div>
        <div style="font-size: 12px; color: #6c757d; text-transform: uppercase; font-weight: 600;">Ocupado</div>
    </div>
</div>

<button style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; border: none; border-radius: 8px; padding: 10px 20px; font-weight: 600; cursor: pointer; float: right; margin-bottom: 20px;" onclick="alert('Crear nueva ubicación')">
    <i class="fas fa-plus"></i> Nueva Ubicación
</button>

<div style="clear: both;"></div>

<div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th>Ubicación</th>
                <th>Items</th>
                <th>Capacidad</th>
                <th>% Utilizado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Almacén Principal</strong></td>
                <td>245 items | 6545/10000 unidades</td>
                <td>10,000 unidades</td>
                <td>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="flex: 1; height: 8px; background: #e9ecef; border-radius: 4px; overflow: hidden;">
                            <div style="width: 65%; height: 100%; background: #ffc107;"></div>
                        </div>
                        <span style="font-weight: 600; color: #333;">65%</span>
                    </div>
                </td>
                <td>
                    <button style="background: white; border: 1px solid #e9ecef; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px;" onclick="alert('Ver detalles')">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
            <tr>
                <td><strong>Almacén Secundario</strong></td>
                <td>156 items | 3245/5000 unidades</td>
                <td>5,000 unidades</td>
                <td>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="flex: 1; height: 8px; background: #e9ecef; border-radius: 4px; overflow: hidden;">
                            <div style="width: 65%; height: 100%; background: #ffc107;"></div>
                        </div>
                        <span style="font-weight: 600; color: #333;">65%</span>
                    </div>
                </td>
                <td>
                    <button style="background: white; border: 1px solid #e9ecef; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px;" onclick="alert('Ver detalles')">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection

---

# USUARIOS_INDEX.BLADE.PHP
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

---

# CONFIGURACION_INDEX.BLADE.PHP
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

---

# PERFIL_INDEX.BLADE.PHP
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