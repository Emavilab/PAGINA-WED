<?php
/**
 * Página de Sesión Expirada
 * Se muestra cuando el usuario ha estado inactivo por más de 2 minutos
 * Aquí se asegura que la sesión esté completamente destruida
 */

// Asegurar que la sesión esté destruida completamente
if (session_status() !== PHP_SESSION_NONE) {
    $_SESSION = array();
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    
    session_destroy();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesión Expirada</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
            padding: 60px 40px;
            max-width: 500px;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .icon {
            font-size: 80px;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }

        h1 {
            color: #333;
            font-size: 32px;
            margin-bottom: 15px;
        }

        p {
            color: #666;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .message {
            background: #fff3cd;
            border-left: 5px solid #ffc107;
            padding: 15px;
            margin: 25px 0;
            border-radius: 5px;
            text-align: left;
            color: #856404;
        }

        .countdown {
            font-size: 14px;
            color: #999;
            margin-top: 20px;
        }

        .button-container {
            margin-top: 30px;
        }

        a {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 40px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        a:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">⏱️</div>
        <h1>¡Sesión Expirada!</h1>
        <p>Tu sesión ha caducado por inactividad.</p>
        
        <div class="message">
            <strong>¿Qué sucedió?</strong><br>
            Por motivos de seguridad, tu sesión se cierra automáticamente después de 2 minutos sin actividad.
        </div>

        <p>Para continuar, debes iniciar sesión nuevamente.</p>

        <div class="countdown">
            Redirigiendo en <span id="countdown">5</span> segundos...
        </div>

        <div class="button-container">
            <a href="../index.php">Ir al Inicio</a>
        </div>
    </div>

    <script>
        // Countdown para redirección automática
        let seconds = 5;
        const countdownElement = document.getElementById('countdown');

        const interval = setInterval(() => {
            seconds--;
            countdownElement.textContent = seconds;

            if (seconds <= 0) {
                clearInterval(interval);
                window.location.href = '../index.php';
            }
        }, 1000);
    </script>
</body>
</html>
