<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Gracias por tu participación! - HairLife</title>

    <!-- Importamos Bootstrap CSS. Esto nos da un conjunto de estilos base para
         un diseño responsivo y componentes de UI como botones. -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Importamos Bootstrap Icons. Nos permite usar iconos fácilmente en la página. -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Cargamos fuentes personalizadas de Google Fonts para un diseño más atractivo. -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        /* Definimos variables CSS para nuestros colores. Esto es clave para mantener
           la consistencia visual y hacer cambios de color de forma centralizada. */
        :root {
            --purple-primary: RebeccaPurple;
            --purple-secondary: MediumPurple;
            --purple-dark: Indigo;
            --purple-medium: MediumSlateBlue;
            --purple-light: Thistle;
            --purple-very-light: Lavender;
            --purple-background: GhostWhite;

            --text-on-purple: white;
            --card-bg: white;
            --border-color: var(--purple-very-light);
            --card-shadow-elegant: rgba(102, 51, 153, 0.18);
            --input-focus-color: var(--purple-secondary);
            --input-focus-box-shadow: rgba(147, 112, 219, 0.25);
        }

        /* Estilos generales para el cuerpo de la página.
           Usamos Flexbox para asegurar que el pie de página siempre esté al final
           de la pantalla, incluso si el contenido es corto. */
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--purple-background);
            color: darkslategrey;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            text-align: center;
            /* Centra el texto por defecto en toda la página. */
            box-sizing: border-box;
            position: relative;
            z-index: 0;
            margin: 0;
        }

        /* Este elemento crea un patrón de fondo sutil que se repite. */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-image: url('/storage/imagenes/fondo.jpg');
            background-repeat: repeat;
            background-size: 250px;
            opacity: 0.2;
            z-index: -1;
            pointer-events: none;
        }

        /* Contenedor principal que ayuda a centrar el contenido de la tarjeta
           y a que el pie de página se mantenga abajo. */
        .main-gracias-content {
            flex-grow: 1;
            /* Hace que este contenedor ocupe todo el espacio vertical disponible. */
            display: flex;
            flex-direction: column;
            justify-content: center;
            /* Centra el contenido verticalmente. */
            align-items: center;
            /* Centra el contenido horizontalmente. */
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
            /* text-align: center; <-- Este es redundante aquí si ya está en el body y el contenido se centra con flexbox. */
        }

        /* Estilos para la "tarjeta" principal de agradecimiento. */
        .gracias-container {
            background-color: var(--card-bg);
            border-radius: 15px;
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 35px var(--card-shadow-elegant);
            max-width: 650px;
            width: 100%;
            padding: 50px 40px;
        }

        /* Estilos para el icono de verificación de Bootstrap Icons. */
        .gracias-icon {
            font-size: 5.5rem;
            color: var(--purple-primary);
            margin-bottom: 25px;
            display: block;
        }

        /* Estilos para el título y los párrafos. */
        .gracias-container h1 {
            font-family: 'Dancing Script', cursive;
            color: var(--purple-dark);
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .gracias-container p {
            font-size: 1.15rem;
            line-height: 1.7;
            margin-bottom: 30px;
            color: #5a6268;
        }

        .gracias-container p.session-success {
            color: var(--purple-medium);
            font-weight: 500;
        }

        /* Estilos para el spinner de carga, inicialmente oculto. */
        .loader-container {
            display: none;
            padding: 20px 0;
            text-align: center;
        }

        .loader {
            border: 8px solid #f3f3f3;
            border-top: 8px solid var(--purple-primary);
            border-radius: 50%;
            width: 70px;
            height: 70px;
            animation: spin 1.5s linear infinite;
            margin: 25px auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .loading-text {
            font-size: 1.3em;
            color: var(--purple-secondary);
            font-weight: 500;
        }

        /* Estilos para el botón "Volver al inicio".
           Combina clases de Bootstrap (`btn`, `btn-lg`) para la base y el tamaño,
           y nuestras clases personalizadas (`btn-purple-home`) para el color y el hover. */
        .btn-purple-home {
            background-color: var(--purple-primary);
            color: var(--text-on-purple);
            border: none;
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: 500;
            transition: background-color 0.2s ease-in-out;
        }

        .btn-purple-home:hover,
        .btn-purple-home:focus {
            background-color: var(--purple-dark);
            color: var(--text-on-purple);
        }

        /* Estilos para el pie de página, que siempre se mantiene en la parte inferior. */
        .site-footer {
            background-color: var(--purple-dark);
            color: rgba(255, 255, 255, 0.8);
            padding: 5px 0;
            text-align: center;
            font-size: 0.9em;
            margin-top: auto;
        }
    </style>
</head>

<body>
    {{-- Contenido principal de la página. --}}
    <div class="main-gracias-content">
        <div class="gracias-container">
            <!-- Usamos un icono de Bootstrap Icons. -->
            <i class="bi bi-check-circle-fill gracias-icon"></i>
            <h1>¡Cuestionario Enviado!</h1>

            <!-- Esta lógica de Blade muestra un mensaje diferente si hay un mensaje de éxito en la sesión. -->
            @if (session('success'))
            <p class="session-success">{{ session('success') }}</p>
            @else
            <p>Gracias por completar nuestro cuestionario y dedicar tu tiempo.</p>
            @endif

            {{-- Este bloque de Blade decide si mostrar el loader o el botón de volver al inicio. --}}
            @if (isset($recomendacionId) && $recomendacionId !== null)
            <div class="loader-container" id="loaderContainer">
                <div class="loader"></div>
                <p class="loading-text">Estamos preparando tu recomendación personalizada...</p>
            </div>
            @else
            <p>Revisaremos tus respuestas con atención.</p>
            <!-- Este es un botón que usa clases de Bootstrap para su tamaño (`btn`, `btn-lg`)
                 y nuestras clases personalizadas para el estilo (`btn-purple-home`). -->
            <a href="{{ route('crear.nick') }}" class="btn btn-lg btn-purple-home mt-3">Volver al inicio</a>
            @endif
        </div>
    </div>

    {{-- El pie de página. --}}
    <footer class="site-footer">
        <p>&copy; {{ date('Y') }} HairLife. Todos los derechos reservados.</p>
    </footer>

    <!-- Este script solo se ejecuta si hay un ID de recomendación.
         Muestra el spinner de carga y luego redirecciona la página después de un tiempo. -->
    @if (isset($recomendacionId) && $recomendacionId !== null)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var loaderContainer = document.getElementById('loaderContainer');
            if (loaderContainer) {
                loaderContainer.style.display = 'block';
            }

            setTimeout(function() {
                window.location.href = "{{ route('recomendacion.ver_producto', ['id' => $recomendacionId]) }}";
            }, 5000); // Redirecciona después de 5 segundos.
        });
    </script>
    @endif
    <!-- Este script de Bootstrap no es estrictamente necesario para la funcionalidad actual de esta página,
         ya que no se utilizan componentes JS interactivos de Bootstrap aquí. Podría eliminarse si
         esta página es independiente o si no se usa en otras partes de la aplicación. -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>