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
<html class="scroll-smooth" lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Sesión Expirada</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        
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

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .animate-slideIn { animation: slideIn 0.5s ease-out; }
        .animate-pulse-custom { animation: pulse 2s infinite; }
        .animate-bounce-custom { animation: bounce 2s infinite; }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 min-h-screen flex items-center justify-center px-4 py-6 sm:px-6 lg:px-8">

  <!-- CONTENEDOR CENTRAL -->
  <div class="w-full max-w-md sm:max-w-lg">

    <!-- CARD PRINCIPAL -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl overflow-hidden animate-slideIn border border-slate-100 dark:border-slate-800">

      <!-- HEADER CON DEGRADADO -->
      <div class="bg-gradient-to-r from-indigo-100 to-purple-100 dark:from-indigo-900/30 dark:to-purple-900/30 px-4 sm:px-6 py-8 sm:py-10 text-center">
        <div class="text-5xl sm:text-6xl md:text-7xl mb-3 sm:mb-4 animate-pulse-custom inline-block">⏱️</div>
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white mt-2 sm:mt-3">
          ¡Sesión Expirada!
        </h1>
      </div>

      <!-- CONTENIDO PRINCIPAL -->
      <div class="px-4 sm:px-6 md:px-8 py-6 sm:py-8 space-y-5 sm:space-y-6">

        <!-- DESCRIPCIÓN -->
        <p class="text-base sm:text-lg text-slate-700 dark:text-slate-300 leading-relaxed">
          Tu sesión ha caducado por inactividad.
        </p>

        <!-- MENSAJE DE ALERTA -->
        <div class="bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-400 dark:border-amber-600 rounded-lg p-3 sm:p-4">
          <div class="flex gap-2 sm:gap-3">
            <span class="material-icons-outlined text-amber-600 dark:text-amber-400 flex-shrink-0 text-xl sm:text-2xl">info</span>
            <div class="text-left">
              <p class="font-semibold text-amber-900 dark:text-amber-200 text-sm sm:text-base">¿Qué sucedió?</p>
              <p class="text-amber-800 dark:text-amber-300 text-xs sm:text-sm mt-1 leading-relaxed">
                Por motivos de seguridad, tu sesión se cierra automáticamente después de 2 minutos sin actividad.
              </p>
            </div>
          </div>
        </div>

        <!-- EXPLICACIÓN SECUNDARIA -->
        <p class="text-base sm:text-lg text-slate-600 dark:text-slate-400 text-center">
          Para continuar, debes iniciar sesión nuevamente.
        </p>

        <!-- COUNTDOWN -->
        <div class="bg-slate-50 dark:bg-slate-800 rounded-lg p-3 sm:p-4 text-center">
          <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">⏳ Redirigiendo en</p>
          <p class="text-2xl sm:text-4xl md:text-5xl font-bold text-indigo-600 dark:text-indigo-400 mt-1 sm:mt-2 animate-bounce-custom">
            <span id="countdown">5</span>s
          </p>
        </div>

      </div>

      <!-- BOTONES DE ACCIÓN -->
      <div class="px-4 sm:px-6 md:px-8 py-6 sm:py-8 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row gap-3 sm:gap-4">
        
        <a href="../index.php" class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-2.5 sm:py-3 px-4 sm:px-6 rounded-lg transition-all duration-300 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl flex items-center justify-center gap-2 text-sm sm:text-base">
          <span class="material-icons-outlined text-lg sm:text-xl">home</span>
          Ir al Inicio
        </a>

        <a href="../pages/login.php" class="flex-1 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-900 dark:text-white font-bold py-2.5 sm:py-3 px-4 sm:px-6 rounded-lg transition-all duration-300 transform hover:scale-105 active:scale-95 shadow-md hover:shadow-lg flex items-center justify-center gap-2 text-sm sm:text-base">
          <span class="material-icons-outlined text-lg sm:text-xl">login</span>
          Iniciar Sesión
        </a>

      </div>

      <!-- PIE DE INFORMACIÓN -->
      <div class="px-4 sm:px-6 md:px-8 py-4 text-center border-t border-slate-200 dark:border-slate-700">
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-500">
          Si experimentas problemas, contacta con soporte
        </p>
      </div>

    </div>

    <!-- INFORMACIÓN ADICIONAL EN MOBILE -->
    <div class="mt-6 sm:mt-8 text-center">
      <p class="text-xs sm:text-sm text-white/70 dark:text-slate-300">
        Esta protección ayuda a mantener tu cuenta segura
      </p>
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
