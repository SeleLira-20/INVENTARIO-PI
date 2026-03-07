@extends('layouts.app')

@section('title', 'Mi Perfil - Universal Inventory')

@section('content')
<style>
    .profile-container { padding: 10px; background-color: #f8fafc; }
    .card { background: white; border-radius: 12px; border: 1px solid #e2e8f0; padding: 24px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .section-title { font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
    .form-label { font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block; }
    .form-input { width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; background: #fcfdfe; transition: 0.3s; }
    /* Estilo para cuando los campos están bloqueados */
    .form-input:disabled { background: #f1f5f9; cursor: not-allowed; color: #64748b; border-color: #cbd5e1; }
    .btn-blue { background: #2563eb; color: white; border: none; border-radius: 8px; padding: 10px 20px; font-weight: 600; cursor: pointer; transition: 0.3s; }
    .btn-blue:hover { background: #1d4ed8; }
    .btn-red { background: #ef4444; color: white; border: none; border-radius: 8px; padding: 10px 20px; font-weight: 600; cursor: pointer; }
    .avatar-circle { width: 100px; height: 100px; background: #2563eb; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 32px; color: white; position: relative; font-weight: bold; }
    .camera-icon { position: absolute; bottom: 5px; right: 5px; background: #2563eb; border: 2px solid white; border-radius: 50%; width: 26px; height: 26px; font-size: 12px; display: flex; align-items: center; justify-content: center; }
    .switch { position: relative; display: inline-block; width: 40px; height: 22px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #cbd5e1; transition: .4s; border-radius: 34px; }
    .slider:before { position: absolute; content: ""; height: 16px; width: 16px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
    input:checked + .slider { background-color: #2563eb; }
    input:checked + .slider:before { transform: translateX(18px); }
</style>

<div class="profile-container">
    <h2 style="font-weight: 700; color: #1e293b; margin-bottom: 5px;">Mi Perfil</h2>
    <p style="color: #64748b; font-size: 14px; margin-bottom: 25px;">Información personal y configuración de cuenta</p>

    <div class="row">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="avatar-circle">JD<div class="camera-icon"><i class="fas fa-camera"></i></div></div>
                <h4 id="userNameDisplay" style="margin-bottom: 5px; font-weight: 700;">John Doe</h4>
                <p id="userEmailDisplay" style="color: #64748b; font-size: 14px;">john.doe@empresa.com</p>
                <span style="background: #eff6ff; color: #2563eb; padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: 600;">Administrador</span>
                
                <div style="text-align: left; margin-top: 25px; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                    <p style="font-size: 13px; color: #64748b; margin-bottom: 10px;"><i class="fas fa-phone mr-2"></i> +1 234 567 8900</p>
                    <p style="font-size: 13px; color: #64748b; margin-bottom: 10px;"><i class="fas fa-building mr-2"></i> Administración</p>
                    <p style="font-size: 13px; color: #64748b;"><i class="far fa-calendar-alt mr-2"></i> Enero 2026</p>
                </div>
                <button type="button" class="btn-blue w-100 mt-3" id="btnEditMaster"><i class="fas fa-edit"></i> Editar Perfil</button>
            </div>

            <div class="card">
                <h5 class="section-title">Estadísticas</h5>
                <div class="d-flex justify-content-between mb-3"><span style="color: #64748b; font-size: 14px;">Órdenes Completadas</span><span style="font-weight: 700;">234</span></div>
                <div class="d-flex justify-content-between mb-3"><span style="color: #64748b; font-size: 14px;">Picking Realizados</span><span style="font-weight: 700;">156</span></div>
                <div class="d-flex justify-content-between mb-3"><span style="color: #64748b; font-size: 14px;">Movimientos</span><span style="font-weight: 700;">892</span></div>
                <div class="d-flex justify-content-between"><span style="color: #64748b; font-size: 14px;">Días Activo</span><span style="font-weight: 700;">45</span></div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <h5 class="section-title">Información Personal</h5>
                <form id="formInfoPersonal">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Nombre Completo</label><input type="text" id="inputNombre" class="form-input editable-field" value="John Doe" disabled></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Email</label><input type="email" id="inputEmail" class="form-input editable-field" value="john.doe@empresa.com" disabled></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Teléfono</label><input type="text" class="form-input editable-field" value="+1 234 567 8900" disabled></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Departamento</label><select class="form-input editable-field" disabled><option>Administración</option></select></div>
                    </div>
                    <button type="submit" class="btn-blue" id="btnSubmitPersonal">Guardar Cambios</button>
                </form>
            </div>

            <div class="card">
                <h5 class="section-title">Seguridad</h5>
                <form id="formSeguridad">
                    <div class="mb-3"><label class="form-label">Contraseña Actual</label><input type="password" class="form-input" placeholder="••••••••"></div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Nueva Contraseña</label><input type="password" class="form-input" placeholder="••••••••"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Confirmar Contraseña</label><input type="password" class="form-input" placeholder="••••••••"></div>
                    </div>
                    <button type="submit" class="btn-red">Cambiar Contraseña</button>
                </form>
            </div>

            <div class="card">
                <h5 class="section-title">Preferencias de Notificaciones</h5>
                @php $notifs = ['Actualizaciones por Email', 'Notificaciones Push', 'Alertas de Picking', 'Alertas de Inventario']; @endphp
                @foreach($notifs as $n)
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div><div style="font-weight: 600; font-size: 14px;">{{ $n }}</div><div style="font-size: 12px; color: #64748b;">Habilitar alertas</div></div>
                    <label class="switch">
                        <input type="checkbox" checked class="notif-toggle" data-name="{{ $n }}">
                        <span class="slider"></span>
                    </label>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    // Función para alertas visuales
    function mostrarAnuncio(titulo, icono = 'success') {
        Swal.fire({
            title: titulo,
            text: 'Operación realizada visualmente con éxito.',
            icon: icono,
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#2563eb',
            timer: 2000
        });
    }

    // 1. FUNCIONALIDAD EDITAR PERFIL (Habilitar campos)
    const btnEditMaster = document.getElementById('btnEditMaster');
    const fields = document.querySelectorAll('.editable-field');

    btnEditMaster.addEventListener('click', function() {
        fields.forEach(f => f.disabled = false); // Habilita todos los campos
        fields[0].focus(); // Pone el cursor en el nombre
        mostrarAnuncio('Modo edición activado', 'info');
    });

    // 2. FORMULARIO INFORMACIÓN PERSONAL (Visual)
    document.getElementById('formInfoPersonal').addEventListener('submit', function(e) {
        e.preventDefault();
        // Actualizar los textos de la tarjeta lateral para que se vea el cambio
        document.getElementById('userNameDisplay').innerText = document.getElementById('inputNombre').value;
        document.getElementById('userEmailDisplay').innerText = document.getElementById('inputEmail').value;
        
        // Volver a bloquear campos
        fields.forEach(f => f.disabled = true);
        mostrarAnuncio('¡Información Actualizada!');
    });

    // 3. FORMULARIO SEGURIDAD (Visual)
    document.getElementById('formSeguridad').addEventListener('submit', function(e) {
        e.preventDefault();
        this.reset();
        mostrarAnuncio('¡Contraseña Cambiada!');
    });

    // 4. FUNCIONALIDAD NOTIFICACIONES (Los de abajo)
    document.querySelectorAll('.notif-toggle').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const nombre = this.getAttribute('data-name');
            const estado = this.checked ? 'Activada' : 'Desactivada';
            
            // Usamos un Toast para que no sea molesto
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true
            });

            Toast.fire({
                icon: 'success',
                title: `${nombre}: ${estado}`
            });
        });
    });
</script>
@endsection