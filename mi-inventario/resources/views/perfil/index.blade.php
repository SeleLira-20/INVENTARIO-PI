@extends('layouts.app')

@section('title', 'Mi Perfil - Universal Inventory')

@section('content')
<style>
    .profile-container { padding: 10px; background-color: #f8fafc; }
    .card { background: white; border-radius: 12px; border: 1px solid #e2e8f0; padding: 24px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .section-title { font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
    .form-label { font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block; }
    .form-input { width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; background: #fcfdfe; transition: 0.3s; }
    .form-input:disabled { background: #f1f5f9; cursor: not-allowed; color: #64748b; border-color: #cbd5e1; }
    .btn-blue { background: #2563eb; color: white; border: none; border-radius: 8px; padding: 10px 20px; font-weight: 600; cursor: pointer; transition: 0.3s; }
    .btn-blue:hover { background: #1d4ed8; }
    .btn-red { background: #ef4444; color: white; border: none; border-radius: 8px; padding: 10px 20px; font-weight: 600; cursor: pointer; }
    .avatar-circle { width: 100px; height: 100px; background: #2563eb; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 32px; color: white; position: relative; font-weight: bold; }
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
                {{-- Avatar con iniciales reales --}}
                <div class="avatar-circle" id="avatarCircle">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}{{ strtoupper(substr(strstr(Auth::user()->name, ' '), 1, 1)) }}
                </div>
                <h4 id="userNameDisplay" style="margin-bottom: 5px; font-weight: 700;">{{ Auth::user()->name }}</h4>
                <p id="userEmailDisplay" style="color: #64748b; font-size: 14px;">{{ Auth::user()->email }}</p>
                <span style="background: #eff6ff; color: #2563eb; padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: 600;">Administrador</span>

                <div style="text-align: left; margin-top: 25px; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                    <p style="font-size: 13px; color: #64748b; margin-bottom: 10px;">
                        <i class="fas fa-envelope me-2"></i> {{ Auth::user()->email }}
                    </p>
                    <p style="font-size: 13px; color: #64748b; margin-bottom: 10px;">
                        <i class="fas fa-calendar me-2"></i> Desde {{ Auth::user()->created_at->format('M Y') }}
                    </p>
                </div>
                <button type="button" class="btn-blue w-100 mt-3" id="btnEditMaster">
                    <i class="fas fa-edit"></i> Editar Perfil
                </button>
            </div>

            <div class="card">
                <h5 class="section-title">Acceso Rápido</h5>
                <a href="{{ route('usuarios') }}" class="btn btn-outline-primary w-100 mb-2" style="border-radius:8px;font-size:13px;">
                    <i class="fas fa-users me-2"></i>Gestionar Usuarios
                </a>
                <a href="{{ route('inventario') }}" class="btn btn-outline-success w-100 mb-2" style="border-radius:8px;font-size:13px;">
                    <i class="fas fa-boxes me-2"></i>Ver Inventario
                </a>
                <a href="{{ route('reportes') }}" class="btn btn-outline-warning w-100" style="border-radius:8px;font-size:13px;">
                    <i class="fas fa-chart-bar me-2"></i>Reportes
                </a>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <h5 class="section-title">Información Personal</h5>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
                </div>
                @endif

                <form id="formInfoPersonal" method="POST" action="{{ route('perfil.actualizar') }}">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" name="name" id="inputNombre" class="form-input editable-field"
                                value="{{ Auth::user()->name }}" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="inputEmail" class="form-input editable-field"
                                value="{{ Auth::user()->email }}" disabled>
                        </div>
                    </div>
                    <button type="submit" class="btn-blue" id="btnSubmitPersonal" style="display:none;">
                        <i class="fas fa-save me-1"></i>Guardar Cambios
                    </button>
                </form>
            </div>

            <div class="card">
                <h5 class="section-title">Seguridad</h5>
                <form id="formSeguridad" method="POST" action="{{ route('perfil.password') }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Contraseña Actual</label>
                        <input type="password" name="current_password" class="form-input" placeholder="••••••••">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nueva Contraseña</label>
                            <input type="password" name="password" class="form-input" placeholder="••••••••">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" class="form-input" placeholder="••••••••">
                        </div>
                    </div>
                    <button type="submit" class="btn-red">
                        <i class="fas fa-lock me-1"></i>Cambiar Contraseña
                    </button>
                </form>
            </div>

            <div class="card">
                <h5 class="section-title">Preferencias de Notificaciones</h5>
                @php $notifs = ['Actualizaciones por Email', 'Notificaciones Push', 'Alertas de Picking', 'Alertas de Inventario']; @endphp
                @foreach($notifs as $n)
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div>
                        <div style="font-weight: 600; font-size: 14px;">{{ $n }}</div>
                        <div style="font-size: 12px; color: #64748b;">Habilitar alertas</div>
                    </div>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const btnEdit   = document.getElementById('btnEditMaster');
    const btnSave   = document.getElementById('btnSubmitPersonal');
    const fields    = document.querySelectorAll('.editable-field');

    btnEdit.addEventListener('click', () => {
        fields.forEach(f => f.disabled = false);
        fields[0].focus();
        btnSave.style.display = 'inline-block';
        btnEdit.style.display = 'none';
    });

    document.querySelectorAll('.notif-toggle').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
            Toast.fire({ icon: 'success', title: `${this.getAttribute('data-name')}: ${this.checked ? 'Activada' : 'Desactivada'}` });
        });
    });
</script>
@endsection