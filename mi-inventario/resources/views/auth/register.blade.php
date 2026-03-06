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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-container {
            width: 100%;
            max-width: 550px;
            padding: 40px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 35px;
        }

        .logo-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 35px;
        }

        .logo-title {
            font-size: 24px;
            font-weight: 700;
            color: #1e3c72;
            margin-bottom: 5px;
            font-family: 'Poppins', sans-serif;
        }

        .logo-subtitle {
            font-size: 12px;
            color: #6c757d;
            font-weight: 500;
        }

        .form-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e3c72;
            margin-bottom: 8px;
            text-align: center;
        }

        .form-description {
            font-size: 12px;
            color: #6c757d;
            text-align: center;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            font-size: 12px;
            margin-bottom: 6px;
            display: block;
        }

        .form-control,
        .form-select {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 11px 13px;
            font-size: 13px;
            transition: all 0.3s ease;
            height: auto;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #2a5298;
            box-shadow: 0 0 0 0.2rem rgba(42, 82, 152, 0.15);
        }

        .form-control::placeholder {
            color: #adb5bd;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-row.full {
            grid-template-columns: 1fr;
        }

        .password-requirements {
            background: #f0f4f8;
            border: 1px solid #e0e7f1;
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 20px;
            font-size: 11px;
            color: #5a6c7d;
        }

        .password-requirements ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .password-requirements li {
            padding: 3px 0;
        }

        .password-requirements li:before {
            content: "• ";
            color: #2a5298;
            font-weight: bold;
        }

        .form-check {
            padding-left: 0;
            margin-bottom: 15px;
        }

        .form-check-input {
            border-color: #e9ecef;
            margin-top: 3px;
            cursor: pointer;
            width: 18px;
            height: 18px;
        }

        .form-check-input:checked {
            background-color: #2a5298;
            border-color: #2a5298;
        }

        .form-check-label {
            cursor: pointer;
            user-select: none;
            color: #333;
            font-size: 12px;
            margin-left: 8px;
        }

        .terms-link {
            color: #2a5298;
            text-decoration: none;
            font-weight: 600;
        }

        .terms-link:hover {
            color: #1e3c72;
            text-decoration: underline;
        }

        .btn-register {
            width: 100%;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-register:hover {
            background: linear-gradient(135deg, #152d54 0%, #1d3f70 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(30, 60, 114, 0.3);
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #6c757d;
            font-size: 12px;
        }

        .login-link a {
            color: #2a5298;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            color: #1e3c72;
        }

        @media (max-width: 480px) {
            .register-container {
                padding: 25px 20px;
            }

            .logo-icon {
                width: 55px;
                height: 55px;
                font-size: 28px;
            }

            .logo-title {
                font-size: 20px;
            }

            .form-title {
                font-size: 16px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo-section">
            <div class="logo-icon">🦅</div>
            <h1 class="logo-title">Universal Inventory</h1>
            <p class="logo-subtitle">Academic Assets & Resources</p>
        </div>

        <h2 class="form-title">Crear Cuenta</h2>
        <p class="form-description">Completa el formulario para registrarse en el sistema</p>

        <form onsubmit="return handleRegister(event)">
            <div class="form-group form-row">
                <div>
                    <label class="form-label">Nombre Completo *</label>
                    <input type="text" class="form-control" placeholder="John Doe" required>
                </div>
            </div>

            <div class="form-group form-row">
                <div>
                    <label class="form-label">Correo Electrónico *</label>
                    <input type="email" class="form-control" placeholder="nombre@empresa.com" required>
                </div>
                <div>
                    <label class="form-label">Teléfono *</label>
                    <input type="tel" class="form-control" placeholder="+1 234 567 8900" required>
                </div>
            </div>

            <div class="form-group form-row full">
                <div>
                    <label class="form-label">Departamento *</label>
                    <select class="form-select" required>
                        <option selected>Selecciona un departamento</option>
                        <option value="admin">Administración</option>
                        <option value="logistica">Logística</option>
                        <option value="almacen">Almacén</option>
                        <option value="picking">Picking</option>
                        <option value="it">TI</option>
                    </select>
                </div>
            </div>

            <div class="form-group form-row">
                <div>
                    <label class="form-label">Contraseña *</label>
                    <input type="password" class="form-control" placeholder="••••••••" required>
                </div>
                <div>
                    <label class="form-label">Confirmar Contraseña *</label>
                    <input type="password" class="form-control" placeholder="••••••••" required>
                </div>
            </div>

            <div class="password-requirements">
                <strong>Requisitos de la contraseña:</strong>
                <ul>
                    <li>Mínimo 8 caracteres</li>
                    <li>Al menos una letra mayúscula</li>
                    <li>Al menos un número</li>
                    <li>Al menos un carácter especial (ej. #, !, etc.)</li>
                </ul>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="terms" required>
                <label class="form-check-label" for="terms">
                    Acepto los <a href="#" class="terms-link">términos y condiciones</a> y la <a href="#" class="terms-link">política de privacidad</a>
                </label>
            </div>

            <button type="submit" class="btn-register">Crear Cuenta</button>
        </form>

        <div class="login-link">
            ¿Ya tienes una cuenta? <a href="{{ route('login') }}">Inicia sesión aquí</a>
        </div>
    </div>

    <script>
        function handleRegister(event) {
            event.preventDefault();
            alert("✓ Cuenta creada exitosamente\n\nAhora puedes iniciar sesión con tus credenciales");
            window.location.href = "{{ route('login') }}";
            return false;
        }
    </script>
</body>
</html>