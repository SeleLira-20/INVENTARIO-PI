<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - Universal Inventory</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 20px;
        }
        .register-container {
            width: 100%; max-width: 550px;
            padding: 40px; background: white;
            border-radius: 16px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        .logo-section { text-align: center; margin-bottom: 35px; }
        .logo-icon {
            width: 90px; height: 90px; background: transparent;
            border-radius: 12px; display: flex; align-items: center;
            justify-content: center; margin: 0 auto 15px; overflow: hidden;
        }
        .logo-icon img { width: 100%; height: 100%; object-fit: contain; }
        .logo-title { font-size: 24px; font-weight: 700; color: #1e3c72; margin-bottom: 5px; font-family: 'Poppins', sans-serif; }
        .logo-subtitle { font-size: 12px; color: #6c757d; font-weight: 500; }
        .form-title { font-size: 18px; font-weight: 700; color: #1e3c72; margin-bottom: 8px; text-align: center; }
        .form-description { font-size: 12px; color: #6c757d; text-align: center; margin-bottom: 25px; }
        .form-group { margin-bottom: 18px; }
        .form-label { font-weight: 600; color: #333; font-size: 12px; margin-bottom: 6px; display: block; }
        .input-wrapper { position: relative; }
        .form-control, .form-select {
            border: 1.5px solid #e9ecef; border-radius: 8px;
            padding: 11px 13px; font-size: 13px;
            transition: all 0.3s ease; height: auto; width: 100%;
        }
        .form-control.with-toggle { padding-right: 40px; }
        .form-control:focus, .form-select:focus {
            border-color: #2a5298;
            box-shadow: 0 0 0 0.2rem rgba(42, 82, 152, 0.15);
            outline: none;
        }
        .form-control.is-invalid, .form-select.is-invalid { border-color: #dc3545; }
        .form-control.is-valid, .form-select.is-valid { border-color: #198754; }
        .toggle-password {
            position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: #6c757d; cursor: pointer; font-size: 13px;
        }
        .invalid-feedback { color: #dc3545; font-size: 11px; margin-top: 4px; display: none; }
        .invalid-feedback.show { display: block; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }

        /* Indicador de fortaleza de contraseña */
        .password-strength { margin-top: 8px; }
        .strength-bars { display: flex; gap: 4px; margin-bottom: 5px; }
        .strength-bar {
            flex: 1; height: 4px; border-radius: 2px;
            background: #e9ecef; transition: background 0.3s;
        }
        .strength-bar.weak { background: #dc3545; }
        .strength-bar.fair { background: #fd7e14; }
        .strength-bar.good { background: #ffc107; }
        .strength-bar.strong { background: #198754; }
        .strength-label { font-size: 11px; color: #6c757d; }

        /* Requisitos de contraseña */
        .password-requirements {
            background: #f8f9fa; border-radius: 8px;
            padding: 12px 15px; margin-top: 8px; font-size: 11px;
        }
        .req-item { display: flex; align-items: center; gap: 6px; margin-bottom: 4px; color: #6c757d; }
        .req-item:last-child { margin-bottom: 0; }
        .req-icon { width: 14px; font-size: 11px; }
        .req-item.met { color: #198754; }
        .req-item.unmet { color: #dc3545; }

        .btn-register {
            width: 100%;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white; border: none; border-radius: 8px;
            padding: 12px; font-weight: 600; font-size: 14px;
            cursor: pointer; transition: all 0.3s ease; margin-top: 10px;
        }
        .btn-register:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-register:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
        .login-link { text-align: center; margin-top: 20px; color: #6c757d; font-size: 12px; }
        .login-link a { color: #2a5298; text-decoration: none; font-weight: 600; }
        .terms-link { color: #2a5298; }
        .alert-error {
            background: #fff5f5; border: 1px solid #f5c6cb;
            border-radius: 8px; padding: 12px 15px;
            color: #721c24; font-size: 12px;
            margin-bottom: 15px; display: none; align-items: center; gap: 8px;
        }
        .alert-error.show { display: flex; }
        @media (max-width: 480px) { .form-row { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo-section">
            <div class="logo-icon">
                <img src="{{ asset('logo.jpeg') }}" alt="Logo Universal Inventory">
            </div>
            <h1 class="logo-title">Universal Inventory</h1>
            <p class="logo-subtitle">Academic Assets & Resources</p>
        </div>

        <h2 class="form-title">Crear Cuenta</h2>
        <p class="form-description">Completa el formulario para registrarse en el sistema</p>

        <div class="alert-error" id="alertError">
            <i class="fas fa-exclamation-circle"></i>
            <span id="alertErrorMsg">Por favor corrige los errores antes de continuar.</span>
        </div>

        <form id="registerForm" novalidate>
            <div class="form-group">
                <label class="form-label">Nombre Completo *</label>
                <input type="text" class="form-control" id="nombre" placeholder="John Doe" autocomplete="name">
                <div class="invalid-feedback" id="nombreError">El nombre completo es obligatorio (mínimo 3 caracteres).</div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Correo Electrónico *</label>
                    <input type="email" class="form-control" id="email" placeholder="nombre@empresa.com" autocomplete="email">
                    <div class="invalid-feedback" id="emailError">Ingresa un correo válido.</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Teléfono *</label>
                    <input type="tel" class="form-control" id="telefono" placeholder="5512345678" maxlength="10" inputmode="numeric">
                    <div class="invalid-feedback" id="telefonoError">Ingresa exactamente 10 dígitos numéricos.</div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Departamento *</label>
                <select class="form-select" id="departamento">
                    <option value="" disabled selected>Selecciona un departamento</option>
                    <option value="admin">Administración</option>
                    <option value="logistica">Logística</option>
                    <option value="almacen">Almacén</option>
                    <option value="picking">Picking</option>
                    <option value="it">TI</option>
                </select>
                <div class="invalid-feedback" id="departamentoError">Selecciona un departamento.</div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Contraseña *</label>
                    <div class="input-wrapper">
                        <input type="password" class="form-control with-toggle" id="password" placeholder="••••••••" autocomplete="new-password">
                        <button type="button" class="toggle-password" onclick="togglePassword('password', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <!-- Indicador de fortaleza -->
                    <div class="password-strength" id="strengthContainer" style="display:none;">
                        <div class="strength-bars">
                            <div class="strength-bar" id="bar1"></div>
                            <div class="strength-bar" id="bar2"></div>
                            <div class="strength-bar" id="bar3"></div>
                            <div class="strength-bar" id="bar4"></div>
                        </div>
                        <span class="strength-label" id="strengthLabel"></span>
                    </div>
                    <!-- Requisitos visuales -->
                    <div class="password-requirements" id="passwordReqs" style="display:none;">
                        <div class="req-item" id="req-length"><span class="req-icon"><i class="fas fa-times"></i></span> Mínimo 8 caracteres</div>
                        <div class="req-item" id="req-upper"><span class="req-icon"><i class="fas fa-times"></i></span> Al menos una mayúscula</div>
                        <div class="req-item" id="req-number"><span class="req-icon"><i class="fas fa-times"></i></span> Al menos un número</div>
                        <div class="req-item" id="req-special"><span class="req-icon"><i class="fas fa-times"></i></span> Al menos un carácter especial (!@#$%...)</div>
                    </div>
                    <div class="invalid-feedback" id="passwordError">La contraseña no cumple los requisitos.</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Confirmar Contraseña *</label>
                    <div class="input-wrapper">
                        <input type="password" class="form-control with-toggle" id="confirmPassword" placeholder="••••••••" autocomplete="new-password">
                        <button type="button" class="toggle-password" onclick="togglePassword('confirmPassword', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback" id="confirmError">Las contraseñas no coinciden.</div>
                </div>
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="terms">
                    <label class="form-check-label" for="terms" style="font-size: 12px;">
                        Acepto los <a href="#" class="terms-link">términos y condiciones</a>
                    </label>
                </div>
                <div class="invalid-feedback" id="termsError">Debes aceptar los términos y condiciones.</div>
            </div>

            <button type="submit" class="btn-register" id="btnRegister">Crear Cuenta</button>
        </form>

        <div class="login-link">
            ¿Ya tienes una cuenta? <a href="{{ route('login') }}">Inicia sesión aquí</a>
        </div>
    </div>

    <script>
        function togglePassword(fieldId, btn) {
            const field = document.getElementById(fieldId);
            const icon = btn.querySelector('i');
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim());
        }

        function isValidPhone(phone) {
            // Solo 10 dígitos numéricos exactos
            return /^\d{10}$/.test(phone.trim());
        }

        function setFieldState(fieldId, errorId, isValid, msg) {
            const field = document.getElementById(fieldId);
            const error = document.getElementById(errorId);
            if (isValid) {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
                error.classList.remove('show');
            } else {
                field.classList.remove('is-valid');
                field.classList.add('is-invalid');
                if (msg) error.textContent = msg;
                error.classList.add('show');
            }
            return isValid;
        }

        // Validaciones individuales
        function validateNombre() {
            const val = document.getElementById('nombre').value.trim();
            if (!val) return setFieldState('nombre', 'nombreError', false, 'El nombre completo es obligatorio.');
            if (val.length < 3) return setFieldState('nombre', 'nombreError', false, 'El nombre debe tener al menos 3 caracteres.');
            if (!/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+$/.test(val)) return setFieldState('nombre', 'nombreError', false, 'El nombre solo puede contener letras y espacios.');
            return setFieldState('nombre', 'nombreError', true);
        }

        function validateEmail() {
            const val = document.getElementById('email').value.trim();
            if (!val) return setFieldState('email', 'emailError', false, 'El correo es obligatorio.');
            if (!isValidEmail(val)) return setFieldState('email', 'emailError', false, 'Ingresa un correo válido (ej: usuario@empresa.com).');
            return setFieldState('email', 'emailError', true);
        }

        function validateTelefono() {
            const val = document.getElementById('telefono').value.trim();
            if (!val) return setFieldState('telefono', 'telefonoError', false, 'El teléfono es obligatorio.');
            if (!/^\d+$/.test(val)) return setFieldState('telefono', 'telefonoError', false, 'Solo se permiten números, sin espacios ni guiones.');
            if (val.length !== 10) return setFieldState('telefono', 'telefonoError', false, `Ingresa exactamente 10 dígitos (llevas ${val.length}).`);
            return setFieldState('telefono', 'telefonoError', true);
        }

        function validateDepartamento() {
            const val = document.getElementById('departamento').value;
            if (!val) return setFieldState('departamento', 'departamentoError', false, 'Selecciona un departamento.');
            return setFieldState('departamento', 'departamentoError', true);
        }

        function checkPasswordRequirements(password) {
            return {
                length: password.length >= 8,
                upper: /[A-Z]/.test(password),
                number: /\d/.test(password),
                special: /[!@#$%^&*(),.?":{}|<>\-_=+\[\]\\\/;'`~]/.test(password)
            };
        }

        function updateReqItem(id, met) {
            const el = document.getElementById(id);
            const icon = el.querySelector('i');
            if (met) {
                el.classList.add('met');
                el.classList.remove('unmet');
                icon.classList.replace('fa-times', 'fa-check');
            } else {
                el.classList.remove('met');
                el.classList.add('unmet');
                icon.classList.replace('fa-check', 'fa-times');
            }
        }

        function getStrength(reqs) {
            const met = Object.values(reqs).filter(Boolean).length;
            if (met <= 1) return { level: 1, label: 'Muy débil', class: 'weak' };
            if (met === 2) return { level: 2, label: 'Débil', class: 'fair' };
            if (met === 3) return { level: 3, label: 'Moderada', class: 'good' };
            return { level: 4, label: 'Fuerte', class: 'strong' };
        }

        function validatePassword() {
            const val = document.getElementById('password').value;
            const reqs = checkPasswordRequirements(val);

            // Mostrar/ocultar panel de requisitos
            const reqsPanel = document.getElementById('passwordReqs');
            const strengthContainer = document.getElementById('strengthContainer');

            if (val.length > 0) {
                reqsPanel.style.display = 'block';
                strengthContainer.style.display = 'block';
            } else {
                reqsPanel.style.display = 'none';
                strengthContainer.style.display = 'none';
            }

            updateReqItem('req-length', reqs.length);
            updateReqItem('req-upper', reqs.upper);
            updateReqItem('req-number', reqs.number);
            updateReqItem('req-special', reqs.special);

            const allMet = Object.values(reqs).every(Boolean);
            const strength = getStrength(reqs);

            // Actualizar barras
            for (let i = 1; i <= 4; i++) {
                const bar = document.getElementById('bar' + i);
                bar.className = 'strength-bar';
                if (i <= strength.level) bar.classList.add(strength.class);
            }
            document.getElementById('strengthLabel').textContent = val.length > 0 ? 'Fortaleza: ' + strength.label : '';

            if (!val) return setFieldState('password', 'passwordError', false, 'La contraseña es obligatoria.');
            if (!allMet) return setFieldState('password', 'passwordError', false, 'La contraseña no cumple todos los requisitos.');
            return setFieldState('password', 'passwordError', true);
        }

        function validateConfirmPassword() {
            const pass = document.getElementById('password').value;
            const confirm = document.getElementById('confirmPassword').value;
            if (!confirm) return setFieldState('confirmPassword', 'confirmError', false, 'Confirma tu contraseña.');
            if (pass !== confirm) return setFieldState('confirmPassword', 'confirmError', false, 'Las contraseñas no coinciden.');
            return setFieldState('confirmPassword', 'confirmError', true);
        }

        function validateTerms() {
            const checked = document.getElementById('terms').checked;
            const error = document.getElementById('termsError');
            if (!checked) {
                error.classList.add('show');
                return false;
            }
            error.classList.remove('show');
            return true;
        }

        // Solo permitir dígitos en teléfono
        document.getElementById('telefono').addEventListener('keypress', function(e) {
            if (!/\d/.test(e.key)) e.preventDefault();
        });
        document.getElementById('telefono').addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 10);
            if (this.classList.contains('is-invalid')) validateTelefono();
        });

        document.getElementById('nombre').addEventListener('blur', validateNombre);
        document.getElementById('email').addEventListener('blur', validateEmail);
        document.getElementById('telefono').addEventListener('blur', validateTelefono);
        document.getElementById('departamento').addEventListener('change', validateDepartamento);
        document.getElementById('password').addEventListener('input', function() {
            validatePassword();
            if (document.getElementById('confirmPassword').value) validateConfirmPassword();
        });
        document.getElementById('confirmPassword').addEventListener('input', validateConfirmPassword);

        // Limpiar errores mientras escribe
        ['nombre', 'email', 'telefono'].forEach(id => {
            document.getElementById(id).addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    const fn = { nombre: validateNombre, email: validateEmail, telefono: validateTelefono };
                    fn[id]();
                }
            });
        });

        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const results = [
                validateNombre(),
                validateEmail(),
                validateTelefono(),
                validateDepartamento(),
                validatePassword(),
                validateConfirmPassword(),
                validateTerms()
            ];

            const alertEl = document.getElementById('alertError');
            if (results.includes(false)) {
                alertEl.classList.add('show');
                document.getElementById('alertErrorMsg').textContent = 'Por favor corrige los errores marcados antes de continuar.';
                // Hacer scroll al primer error
                const firstError = document.querySelector('.is-invalid, .invalid-feedback.show');
                if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            alertEl.classList.remove('show');
            const btn = document.getElementById('btnRegister');
            btn.disabled = true;
            btn.textContent = 'Creando cuenta...';

            // Aquí iría la llamada real al servidor (Laravel)
            setTimeout(() => {
                window.location.href = "{{ route('login') }}";
            }, 800);
        });
    </script>
</body>
</html>