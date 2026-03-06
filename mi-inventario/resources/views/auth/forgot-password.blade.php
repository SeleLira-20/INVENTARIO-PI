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
        }

        .recovery-container {
            width: 100%;
            max-width: 420px;
            padding: 40px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
        }

        .logo-title {
            font-size: 28px;
            font-weight: 700;
            color: #1e3c72;
            margin-bottom: 5px;
            font-family: 'Poppins', sans-serif;
        }

        .logo-subtitle {
            font-size: 13px;
            color: #6c757d;
            font-weight: 500;
        }

        .form-title {
            font-size: 20px;
            font-weight: 700;
            color: #1e3c72;
            margin-bottom: 12px;
            text-align: center;
        }

        .form-description {
            font-size: 13px;
            color: #6c757d;
            text-align: center;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            font-size: 13px;
            margin-bottom: 8px;
            display: block;
        }

        .form-control {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
            height: auto;
        }

        .form-control:focus {
            border-color: #2a5298;
            box-shadow: 0 0 0 0.2rem rgba(42, 82, 152, 0.15);
        }

        .form-control::placeholder {
            color: #adb5bd;
        }

        .btn-send {
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
            margin-bottom: 15px;
        }

        .btn-send:hover {
            background: linear-gradient(135deg, #152d54 0%, #1d3f70 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(30, 60, 114, 0.3);
        }

        .back-link {
            text-align: center;
            color: #6c757d;
            font-size: 13px;
        }

        .back-link a {
            color: #2a5298;
            text-decoration: none;
            font-weight: 600;
        }

        .back-link a:hover {
            color: #1e3c72;
        }

        .info-box {
            background: #f0f4f8;
            border-left: 4px solid #2a5298;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
            font-size: 12px;
            color: #5a6c7d;
        }

        .info-box i {
            color: #2a5298;
            margin-right: 8px;
        }

        @media (max-width: 480px) {
            .recovery-container {
                padding: 30px 20px;
            }

            .logo-icon {
                width: 60px;
                height: 60px;
                font-size: 30px;
            }

            .logo-title {
                font-size: 24px;
            }

            .form-title {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="recovery-container">
        <div class="logo-section">
            <div class="logo-icon">🦅</div>
            <h1 class="logo-title">Universal Inventory</h1>
            <p class="logo-subtitle">Academic Assets & Resources</p>
        </div>

        <h2 class="form-title">Recuperar Contraseña</h2>
        <p class="form-description">Ingresa tu correo electrónico y te enviaremos instrucciones para restablecer tu contraseña</p>

        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            Recibirás un email con un enlace para crear una nueva contraseña
        </div>

        <form onsubmit="return handleRecovery(event)">
            <div class="form-group">
                <label class="form-label">Correo electrónico</label>
                <input type="email" class="form-control" placeholder="nombre@empresa.com" required>
            </div>

            <button type="submit" class="btn-send">
                <i class="fas fa-envelope"></i> Enviar instrucciones
            </button>
        </form>

        <div class="back-link">
            <i class="fas fa-arrow-left"></i> <a href="{{ route('login') }}">Volver al inicio de sesión</a>
        </div>
    </div>

    <script>
        function handleRecovery(event) {
            event.preventDefault();
            alert("✓ Instrucciones enviadas\n\nRevisa tu correo electrónico para seguir los pasos de recuperación");
            window.location.href = "{{ route('login') }}";
            return false;
        }
    </script>
</body>
</html>