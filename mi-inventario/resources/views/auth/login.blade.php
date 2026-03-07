<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Universal Inventory</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 40px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        .logo-section { text-align: center; margin-bottom: 40px; }
        
        /* Ajuste para que el logo se vea bien */
        .logo-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 20px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .logo-title {
            font-size: 28px;
            font-weight: 700;
            color: #1e3c72;
            margin-bottom: 5px;
            font-family: 'Poppins', sans-serif;
        }
        .logo-subtitle { font-size: 13px; color: #6c757d; font-weight: 500; }
        .form-title { font-size: 20px; font-weight: 700; color: #1e3c72; margin-bottom: 25px; text-align: center; }
        .form-group { margin-bottom: 20px; }
        .form-label { font-weight: 600; color: #333; font-size: 13px; margin-bottom: 8px; display: block; }
        .form-control { border: 1px solid #e9ecef; border-radius: 8px; padding: 12px 15px; font-size: 14px; transition: all 0.3s ease; height: auto; }
        .form-control:focus { border-color: #2a5298; box-shadow: 0 0 0 0.2rem rgba(42, 82, 152, 0.15); }
        .remember-forgot { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; font-size: 13px; }
        .remember-forgot a { color: #2a5298; text-decoration: none; font-weight: 600; }
        .btn-login { width: 100%; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; border: none; border-radius: 8px; padding: 12px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.3s ease; }
        .divider { display: flex; align-items: center; margin: 25px 0; color: #ccc; }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background-color: #e9ecef; }
        .divider span { padding: 0 15px; color: #6c757d; font-size: 12px; font-weight: 600; }
        .signup-link { text-align: center; margin-top: 25px; color: #6c757d; font-size: 13px; }
        .signup-link a { color: #2a5298; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-section">
            <div class="logo-icon">
                <img src="{{ asset('logo.jpeg') }}" alt="Logo Universal Inventory">
            </div>
            <h1 class="logo-title">Universal Inventory</h1>
            <p class="logo-subtitle">Academic Assets & Resources</p>
        </div>

        <h2 class="form-title">Iniciar sesión</h2>

        <form onsubmit="return handleLogin(event)">
            <div class="form-group">
                <label class="form-label">Correo electrónico</label>
                <input type="email" class="form-control" placeholder="nombre@empresa.com" required>
            </div>

            <div class="form-group">
                <label class="form-label">Contraseña</label>
                <input type="password" class="form-control" placeholder="••••••••" required>
            </div>

            <div class="remember-forgot">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">Recordar sesión</label>
                </div>
                <a href="{{ route('forgot-password') }}">¿Olvidaste tu contraseña?</a>
            </div>

            <button type="submit" class="btn-login">Iniciar sesión</button>
        </form>

        <div class="divider"><span>¿No tienes una cuenta?</span></div>
        <div class="signup-link"><a href="{{ route('register') }}">Regístrate aquí</a></div>
    </div>

    <script>
        function handleLogin(event) {
            event.preventDefault();
            setTimeout(() => {
                alert("✓ Iniciar sesión\n\nRedirigiendo al dashboard...");
                window.location.href = "{{ route('dashboard') }}";
            }, 300);
            return false;
        }
    </script>
</body>
</html>