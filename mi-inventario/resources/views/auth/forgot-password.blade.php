<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Universal Inventory</title>
    
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
        }
        .recovery-container {
            width: 100%; max-width: 420px;
            padding: 40px; background: white;
            border-radius: 16px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        .logo-section { text-align: center; margin-bottom: 40px; }
        .logo-icon {
            width: 100px; height: 100px; background: transparent;
            border-radius: 12px; display: flex; align-items: center;
            justify-content: center; margin: 0 auto 20px; overflow: hidden;
        }
        .logo-icon img { width: 100%; height: 100%; object-fit: contain; }
        .logo-title { font-size: 28px; font-weight: 700; color: #1e3c72; margin-bottom: 5px; font-family: 'Poppins', sans-serif; }
        .logo-subtitle { font-size: 13px; color: #6c757d; font-weight: 500; }
        .form-title { font-size: 20px; font-weight: 700; color: #1e3c72; margin-bottom: 12px; text-align: center; }
        .form-description { font-size: 13px; color: #6c757d; text-align: center; margin-bottom: 25px; line-height: 1.5; }
        .form-group { margin-bottom: 20px; }
        .form-label { font-weight: 600; color: #333; font-size: 13px; margin-bottom: 8px; display: block; }
        .form-control {
            border: 1.5px solid #e9ecef; border-radius: 8px;
            padding: 12px 15px; font-size: 14px;
            transition: all 0.3s ease; height: auto; width: 100%;
        }
        .form-control:focus { border-color: #2a5298; box-shadow: 0 0 0 0.2rem rgba(42, 82, 152, 0.15); outline: none; }
        .form-control.is-invalid { border-color: #dc3545; }
        .form-control.is-valid { border-color: #198754; }
        .invalid-feedback { color: #dc3545; font-size: 12px; margin-top: 5px; display: none; }
        .invalid-feedback.show { display: block; }
        .btn-send {
            width: 100%;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white; border: none; border-radius: 8px;
            padding: 12px; font-weight: 600; font-size: 14px;
            cursor: pointer; transition: all 0.3s ease; margin-bottom: 15px;
        }
        .btn-send:hover { opacity: 0.9; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(30, 60, 114, 0.3); }
        .btn-send:disabled { opacity: 0.6; cursor: not-allowed; transform: none; box-shadow: none; }
        .back-link { text-align: center; color: #6c757d; font-size: 13px; }
        .back-link a { color: #2a5298; text-decoration: none; font-weight: 600; }
        .info-box {
            background: #f0f4f8; border-left: 4px solid #2a5298;
            border-radius: 8px; padding: 15px; margin-bottom: 25px;
            font-size: 12px; color: #5a6c7d;
        }
        .info-box i { color: #2a5298; margin-right: 8px; }

        /* Panel de éxito */
        .success-panel {
            text-align: center; display: none;
        }
        .success-panel .success-icon {
            width: 70px; height: 70px;
            background: #d1fae5; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px; font-size: 28px; color: #059669;
        }
        .success-panel h3 { color: #1e3c72; font-weight: 700; margin-bottom: 10px; }
        .success-panel p { color: #6c757d; font-size: 13px; line-height: 1.6; margin-bottom: 20px; }

        @media (max-width: 480px) { .logo-icon { width: 80px; height: 80px; } }
    </style>
</head>
<body>
    <div class="recovery-container">
        <div class="logo-section">
            <div class="logo-icon">
                <img src="{{ asset('logo.jpeg') }}" alt="Logo Universal Inventory">
            </div>
            <h1 class="logo-title">Universal Inventory</h1>
            <p class="logo-subtitle">Academic Assets & Resources</p>
        </div>

        <!-- Formulario de recuperación -->
        <div id="formPanel">
            <h2 class="form-title">Recuperar Contraseña</h2>
            <p class="form-description">Ingresa tu correo electrónico y te enviaremos instrucciones para restablecer tu contraseña</p>

            <div class="info-box">
                <i class="fas fa-info-circle"></i>
                Recibirás un email con un enlace para crear una nueva contraseña
            </div>

            <form id="recoveryForm" novalidate>
                <div class="form-group">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" class="form-control" id="email" placeholder="nombre@empresa.com" autocomplete="email">
                    <div class="invalid-feedback" id="emailError">Ingresa un correo electrónico válido.</div>
                </div>

                <button type="submit" class="btn-send" id="btnSend">
                    <i class="fas fa-envelope"></i> Enviar instrucciones
                </button>
            </form>

            <div class="back-link">
                <i class="fas fa-arrow-left"></i> <a href="{{ route('login') }}">Volver al inicio de sesión</a>
            </div>
        </div>

        <!-- Panel de éxito (se muestra después de enviar) -->
        <div class="success-panel" id="successPanel">
            <div class="success-icon"><i class="fas fa-envelope-open-text"></i></div>
            <h3>¡Correo enviado!</h3>
            <p>Hemos enviado las instrucciones de recuperación a <strong id="emailSent"></strong>. Revisa también tu carpeta de spam.</p>
            <a href="{{ route('login') }}" style="display: inline-block; background: linear-gradient(135deg, #1e3c72, #2a5298); color: white; padding: 10px 25px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px;">
                Volver al inicio de sesión
            </a>
        </div>
    </div>

    <script>
        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim());
        }

        function validateEmail() {
            const val = document.getElementById('email').value.trim();
            const field = document.getElementById('email');
            const error = document.getElementById('emailError');

            if (!val) {
                field.classList.add('is-invalid');
                field.classList.remove('is-valid');
                error.textContent = 'El correo electrónico es obligatorio.';
                error.classList.add('show');
                return false;
            }
            if (!isValidEmail(val)) {
                field.classList.add('is-invalid');
                field.classList.remove('is-valid');
                error.textContent = 'Ingresa un correo válido (ej: usuario@empresa.com).';
                error.classList.add('show');
                return false;
            }
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            error.classList.remove('show');
            return true;
        }

        document.getElementById('email').addEventListener('blur', validateEmail);
        document.getElementById('email').addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) validateEmail();
        });

        document.getElementById('recoveryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            if (!validateEmail()) return;

            const emailVal = document.getElementById('email').value.trim();
            const btn = document.getElementById('btnSend');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

            // Aquí iría la llamada real al servidor (Laravel)
            setTimeout(() => {
                document.getElementById('formPanel').style.display = 'none';
                document.getElementById('emailSent').textContent = emailVal;
                document.getElementById('successPanel').style.display = 'block';
            }, 1000);
        });
    </script>
</body>
</html>